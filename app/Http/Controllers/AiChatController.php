<?php

namespace App\Http\Controllers;

use App\Services\GeminiService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class AiChatController extends Controller
{
    protected GeminiService $geminiService;

    public function __construct(GeminiService $geminiService)
    {
        $this->geminiService = $geminiService;
    }

    /**
     * Show dedicated full-screen AI Chat room.
     */
    public function showPage(Request $request)
    {
        $isGuest = !auth()->check();
        $guestRemaining = 5;
        $guestUsed = 0;

        if ($isGuest) {
            $ip = $request->ip() ?: '127.0.0.1';
            $ipKey = 'ai_chat_guest_ip_' . md5($ip);
            $guestToken = $request->cookie('edvora_ai_token') ?? $request->session()->get('edvora_ai_token');
            $tokenKey = $guestToken ? ('ai_chat_guest_token_' . $guestToken) : null;
            $ipCount = (int) \Illuminate\Support\Facades\Cache::get($ipKey, 0);
            $tokenCount = $tokenKey ? (int) \Illuminate\Support\Facades\Cache::get($tokenKey, 0) : 0;
            $sessionCount = (int) session()->get('ai_guest_question_count', 0);
            $guestUsed = max($ipCount, $tokenCount, $sessionCount);
            $guestRemaining = max(0, 5 - $guestUsed);
        }

        $user = auth()->user();

        return view('ai.chat', compact('isGuest', 'guestRemaining', 'guestUsed', 'user'));
    }

    /**
     * Handle AI Chat input from frontend.
     */
    public function __invoke(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'message' => 'required|string|max:1000',
            'topic' => 'nullable|string|max:50',
            'language' => 'nullable|string|in:en,fa,auto',
            'history' => 'nullable|array',
            'history.*.role' => 'nullable|string|in:user,model,assistant',
            'history.*.text' => 'nullable|string|max:1000',
            'history.*.content' => 'nullable|string|max:1000',
        ]);

        $message = trim($validated['message']);
        $history = $validated['history'] ?? [];
        $topic = $validated['topic'] ?? 'all';

        // Automatically detect language from user's message
        $hasPersian = (bool) preg_match('/[\x{0600}-\x{06FF}]/u', $message);
        $language = $hasPersian ? 'fa' : ($validated['language'] ?? 'en');

        // Enforce 5-question limit for unauthenticated (guest) users
        $isGuest = !auth()->check();
        $maxQuestions = 5;
        $guestToken = null;
        $ipKey = null;
        $tokenKey = null;
        $currentUsed = 0;

        if ($isGuest) {
            $ip = $request->ip() ?: '127.0.0.1';
            $guestToken = $request->cookie('edvora_ai_token') ?? $request->session()->get('edvora_ai_token');
            if (!$guestToken) {
                $guestToken = \Illuminate\Support\Str::uuid()->toString();
                $request->session()->put('edvora_ai_token', $guestToken);
            }

            $ipKey = 'ai_chat_guest_ip_' . md5($ip);
            $tokenKey = 'ai_chat_guest_token_' . $guestToken;

            $ipCount = (int) \Illuminate\Support\Facades\Cache::get($ipKey, 0);
            $tokenCount = (int) \Illuminate\Support\Facades\Cache::get($tokenKey, 0);
            $sessionCount = (int) session()->get('ai_guest_question_count', 0);

            $currentUsed = max($ipCount, $tokenCount, $sessionCount);

            if ($currentUsed >= $maxQuestions) {
                return response()->json([
                    'success' => false,
                    'limit_reached' => true,
                    'is_guest' => true,
                    'used' => $currentUsed,
                    'remaining' => 0,
                    'max' => $maxQuestions,
                    'message' => $language === 'fa'
                        ? 'شما به سقف ۵ سوال رایگان برای کاربر مهمان رسیده‌اید. برای ادامه گفت‌وگو لطفاً وارد حساب کاربری خود شوید یا ثبت‌نام کنید.'
                        : 'You have reached the limit of 5 free questions for guest users. Please log in or sign up to continue unlimited chatting.',
                ], 429)->withCookie(cookie('edvora_ai_token', $guestToken, 60 * 24 * 7));
            }
        }

        try {
            $result = $this->geminiService->chat($message, $history, $topic, $language);

            if (!$result['success']) {
                return response()->json([
                    'success' => false,
                    'message' => $result['message'],
                ], 422);
            }

            $responseMessage = trim($result['message'] ?? '');
            if ($responseMessage === '') {
                $responseMessage = $language === 'fa'
                    ? 'پاسخی از دستیار هوشمند دریافت نشد. لطفاً سوال خود را دوباره مطرح بفرمایید.'
                    : 'No response received. Please try rephrasing your question.';
            }

            $responsePayload = [
                'success' => true,
                'message' => $responseMessage,
                'sources' => $result['sources'] ?? [],
                'is_guest' => $isGuest,
            ];

            if ($isGuest) {
                $newUsed = $currentUsed + 1;
                $expiresAt = now()->addDays(7);
                \Illuminate\Support\Facades\Cache::put($ipKey, $newUsed, $expiresAt);
                \Illuminate\Support\Facades\Cache::put($tokenKey, $newUsed, $expiresAt);
                session()->put('ai_guest_question_count', $newUsed);

                $remaining = max(0, $maxQuestions - $newUsed);
                $responsePayload['used'] = $newUsed;
                $responsePayload['remaining'] = $remaining;
                $responsePayload['max'] = $maxQuestions;
                $responsePayload['limit_reached'] = ($remaining === 0);

                return response()->json($responsePayload)
                    ->withCookie(cookie('edvora_ai_token', $guestToken, 60 * 24 * 7));
            }

            return response()->json($responsePayload);
        } catch (\Throwable $e) {
            return response()->json([
                'success' => false,
                'message' => 'متأسفانه در پردازش درخواست شما خطایی رخ داده است. لطفاً دوباره تلاش کنید.',
                'error_debug' => config('app.debug') ? $e->getMessage() : null,
            ], 500);
        }
    }
}
