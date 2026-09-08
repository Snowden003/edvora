<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Mail\BroadcastMail;
use App\Models\BroadcastEmail;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;
use Inertia\Inertia;
use Inertia\Response;

class AdminBroadcastEmailController extends Controller
{
    public function index(): Response
    {
        $stats = [
            'students_count'  => User::where('role', 'student')->count(),
            'teachers_count'  => User::where('role', 'teacher')->count(),
            'all_users_count' => User::count(),
        ];

        $history = BroadcastEmail::with('sender')
            ->latest()
            ->paginate(15)
            ->through(fn ($item) => [
                'id'               => $item->id,
                'subject'          => $item->subject,
                'preheader'        => $item->preheader,
                'target_audience'  => $item->target_audience,
                'recipients_count' => $item->recipients_count,
                'banner_url'       => $item->banner_url,
                'cta_text'         => $item->cta_text,
                'cta_url'          => $item->cta_url,
                'sent_at'          => $item->sent_at ? $item->sent_at->format('Y-m-d H:i') : null,
                'sender_name'      => $item->sender?->name ?? 'Admin',
            ]);

        return Inertia::render('Admin/BroadcastEmail/Index', [
            'stats'         => $stats,
            'history'       => $history,
            'defaultBanner' => asset('assets/images/hero_logo_design.png'),
        ]);
    }

    public function send(Request $request)
    {
        $validated = $request->validate([
            'subject'         => 'required|string|max:255',
            'preheader'       => 'nullable|string|max:255',
            'body'            => 'required|string|min:10',
            'target_audience' => 'required|in:students,teachers,all',
            'banner_file'     => 'nullable|image|mimes:jpeg,png,jpg,webp,gif|max:6144',
            'banner_url'      => 'nullable|url|max:500',
            'cta_text'        => 'nullable|string|max:60',
            'cta_url'         => 'nullable|url|max:255',
        ]);

        $bannerPath = null;
        if ($request->hasFile('banner_file')) {
            $bannerPath = $request->file('banner_file')->store('broadcasts', 'public');
        } elseif (!empty($validated['banner_url'])) {
            $bannerPath = $validated['banner_url'];
        }

        // Determine target query
        $query = User::query()->whereNotNull('email')->where('email', '!=', '');
        if ($validated['target_audience'] === 'students') {
            $query->where('role', 'student');
        } elseif ($validated['target_audience'] === 'teachers') {
            $query->where('role', 'teacher');
        }

        $recipientsCount = (clone $query)->count();

        if ($recipientsCount === 0) {
            return back()->with('error', 'هیچ کاربری در این دسته‌بندی برای دریافت ایمیل یافت نشد.');
        }

        // Save campaign record
        $campaign = BroadcastEmail::create([
            'subject'          => $validated['subject'],
            'preheader'        => $validated['preheader'] ?? null,
            'body'             => $validated['body'],
            'banner_image'     => $bannerPath,
            'target_audience'  => $validated['target_audience'],
            'recipients_count' => $recipientsCount,
            'cta_text'         => $validated['cta_text'] ?? null,
            'cta_url'          => $validated['cta_url'] ?? null,
            'sent_by_user_id'  => $request->user()?->id,
            'sent_at'          => now(),
        ]);

        $bannerFullUrl = $campaign->banner_url ?: asset('assets/images/hero_logo_design.png');

        // Dispatch in chunks to avoid timeouts & memory issues
        $sentCount = 0;
        $failedCount = 0;

        $query->chunk(50, function ($users) use ($validated, $bannerFullUrl, $campaign, &$sentCount, &$failedCount) {
            foreach ($users as $user) {
                try {
                    Mail::to($user->email)->send(new BroadcastMail(
                        recipient: $user,
                        mailSubject: $validated['subject'],
                        mailBody: $validated['body'],
                        bannerUrl: $bannerFullUrl,
                        bannerImage: $campaign->banner_image,
                        preheader: $validated['preheader'] ?? null,
                        ctaText: $validated['cta_text'] ?? null,
                        ctaUrl: $validated['cta_url'] ?? null,
                    ));
                    $sentCount++;
                } catch (\Throwable $e) {
                    $failedCount++;
                    Log::error("Broadcast email error sending to {$user->email}: " . $e->getMessage());
                }
            }
        });

        $campaign->update(['recipients_count' => $sentCount]);

        return back()->with('success', "ایمیل همگانی با موفقیت برای {$sentCount} کاربر ارسال شد.");
    }

    public function sendTest(Request $request)
    {
        $validated = $request->validate([
            'subject'         => 'required|string|max:255',
            'preheader'       => 'nullable|string|max:255',
            'body'            => 'required|string|min:5',
            'banner_file'     => 'nullable|image|mimes:jpeg,png,jpg,webp,gif|max:6144',
            'banner_url'      => 'nullable|url|max:500',
            'cta_text'        => 'nullable|string|max:60',
            'cta_url'         => 'nullable|url|max:255',
        ]);

        $bannerPath = null;
        if ($request->hasFile('banner_file')) {
            $bannerPath = $request->file('banner_file')->store('broadcasts/temp', 'public');
            $bannerFullUrl = asset('storage/' . $bannerPath);
        } elseif (!empty($validated['banner_url'])) {
            $bannerFullUrl = $validated['banner_url'];
        } else {
            $bannerFullUrl = asset('assets/images/hero_logo_design.png');
        }

        $admin = $request->user();

        try {
            Mail::to($admin->email)->send(new BroadcastMail(
                recipient: $admin,
                mailSubject: '[تست] ' . $validated['subject'],
                mailBody: $validated['body'],
                bannerUrl: $bannerFullUrl,
                preheader: $validated['preheader'] ?? null,
                ctaText: $validated['cta_text'] ?? null,
                ctaUrl: $validated['cta_url'] ?? null,
            ));

            return back()->with('success', "ایمیل آزمایشی با موفقیت به ایمیل شما ({$admin->email}) ارسال شد.");
        } catch (\Throwable $e) {
            Log::error("Test broadcast email failed: " . $e->getMessage());
            return back()->with('error', "ارسال ایمیل آزمایشی ناموفق بود: " . $e->getMessage());
        }
    }
}
