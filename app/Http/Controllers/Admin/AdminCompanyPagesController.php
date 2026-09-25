<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\PageContent;
use App\Services\GeminiService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class AdminCompanyPagesController extends Controller
{
    protected GeminiService $geminiService;

    public function __construct(GeminiService $geminiService)
    {
        $this->geminiService = $geminiService;
    }

    /**
     * Map of manageable public pages.
     */
    protected function getPagesMap(): array
    {
        return [
            'about' => [
                'key' => 'page_about',
                'title' => 'درباره ما (About Us)',
                'route' => '/about',
                'description' => 'مدیریت محتوای معرفی آموزشگاه، ماموریت، چشم‌انداز و ارزش‌های ادورا تک',
            ],
            'story' => [
                'key' => 'page_story',
                'title' => 'داستان ما (Our Story)',
                'route' => '/story',
                'description' => 'مدیریت داستان شکل‌گیری، تاریخچه و مسیر رشد پلتفرم آموزشی ادورا',
            ],
            'how-we-work' => [
                'key' => 'page_how_we_work',
                'title' => 'نحوه کار ما (How We Work)',
                'route' => '/how-we-work',
                'description' => 'مدیریت توضیحات متدلوژی آموزشی، مراحل یادگیری و استانداردها',
            ],
            'terms' => [
                'key' => 'page_terms',
                'title' => 'قوانین و شرایط (Terms)',
                'route' => '/terms',
                'description' => 'مدیریت شرایط استفاده، تعهدات کاربر و قوانین قانونی وب‌سایت',
            ],
            'privacy' => [
                'key' => 'page_privacy',
                'title' => 'حریم خصوصی (Privacy)',
                'route' => '/privacy',
                'description' => 'مدیریت سیاست‌های حفظ حریم خصوصی کاربران و نحوه نگهداری داده‌ها',
            ],
        ];
    }

    /**
     * Display page editor.
     */
    public function index(Request $request, string $pageKey = 'about'): Response
    {
        $pagesMap = $this->getPagesMap();

        if (!array_key_exists($pageKey, $pagesMap)) {
            $pageKey = 'about';
        }

        $currentPage = $pagesMap[$pageKey];
        $content = PageContent::get($currentPage['key'], []);

        return Inertia::render('Admin/CompanyPages/Index', [
            'pagesList' => array_values(array_map(function ($k, $item) use ($pageKey) {
                return [
                    'key' => $k,
                    'title' => $item['title'],
                    'route' => $item['route'],
                    'description' => $item['description'],
                    'active' => $k === $pageKey,
                ];
            }, array_keys($pagesMap), $pagesMap)),
            'currentPageKey' => $pageKey,
            'currentPage' => $currentPage,
            'content' => $content,
        ]);
    }

    /**
     * Save/Update page content.
     */
    public function update(Request $request, string $pageKey)
    {
        $pagesMap = $this->getPagesMap();

        if (!array_key_exists($pageKey, $pagesMap)) {
            return back()->with('error', 'صفحه مورد نظر یافت نشد.');
        }

        $request->validate([
            'content' => 'required|array',
        ]);

        $page = $pagesMap[$pageKey];
        PageContent::set($page['key'], $request->input('content'), $page['title']);

        return back()->with('success', "محتوای صفحه «{$page['title']}» با موفقیت بروزرسانی و ذخیره شد.");
    }

    /**
     * Generate or refine content using Gemini AI (supports Text & Voice Audio Base64).
     */
    public function generateAi(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'page_key' => 'required|string',
            'prompt' => 'nullable|string',
            'current_text' => 'nullable|string',
            'audio_data' => 'nullable|string',
            'mime_type' => 'nullable|string',
        ]);

        $pagesMap = $this->getPagesMap();
        $pageKey = $validated['page_key'];
        $pageTitle = $pagesMap[$pageKey]['title'] ?? 'صفحات عمومی';

        $result = $this->geminiService->generatePageContentText(
            $pageTitle,
            $validated['prompt'] ?? '',
            $validated['current_text'] ?? '',
            $validated['audio_data'] ?? null,
            $validated['mime_type'] ?? 'audio/webm'
        );

        if (!$result['success']) {
            return response()->json([
                'success' => false,
                'message' => $result['message'] ?? 'خطا در ارتباط با هوش مصنوعی Gemini',
            ], 422);
        }

        return response()->json([
            'success' => true,
            'text' => $result['text'],
        ]);
    }
}
