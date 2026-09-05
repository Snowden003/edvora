<?php

namespace App\Services;

use App\Models\Book;
use App\Models\Category;
use App\Models\ClassSession;
use App\Models\Course;
use App\Models\Enrollment;
use App\Models\Event;
use App\Models\Review;
use App\Models\User;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class GeminiService
{
    protected string $apiKey;
    protected array $models = [
        'gemini-3.5-flash-lite',
        'gemini-3.1-flash-lite',
        'gemini-flash-lite-latest',
        'gemini-3.5-flash',
    ];

    public function __construct()
    {
        $this->apiKey = trim(config('services.gemini.key') ?? '');
    }

    /**
     * Send chat request to Gemini with DB Context & Function Calling tools.
     */
    public function chat(string $userMessage, array $history = [], ?string $topic = 'all', ?string $language = 'en'): array
    {
        if (empty($this->apiKey)) {
            return [
                'success' => false,
                'message' => $language === 'fa' 
                    ? 'کلید Gemini API در فایل .env تنظیم نشده است.' 
                    : 'Gemini API key is not configured in .env file.',
                'sources' => [],
            ];
        }

        // 1. Gather current platform stats snapshot
        $platformSnapshot = $this->getPlatformSummaryData();

        // 2. Gather scoped context specifically for the chosen topic (courses, teachers, events, etc.)
        $scopedData = $this->getScopedTopicContext($topic ?? 'all', $userMessage);
        $scopedContextText = $scopedData['context'];
        $initialSources = $scopedData['sources'];

        // 3. Define tools for Gemini function calling
        $tools = [
            [
                'function_declarations' => [
                    [
                        'name' => 'get_platform_summary',
                        'description' => 'دریافت آمار کلی پلتفرم ادورا تک شامل تعداد دوره‌ها، اساتید، دانشجویان، رویدادها، کتاب‌ها و جلسات فعال.',
                        'parameters' => [
                            'type' => 'OBJECT',
                            'properties' => (object)[],
                        ],
                    ],
                    [
                        'name' => 'search_courses',
                        'description' => 'جستجوی دوره‌های آموزشی فعال براساس کلمه کلیدی، سطح آموزشی یا دسته‌بندی.',
                        'parameters' => [
                            'type' => 'OBJECT',
                            'properties' => [
                                'query' => [
                                    'type' => 'STRING',
                                    'description' => 'کلمه کلیدی جهت جستجوی نام یا توضیحات دوره',
                                ],
                                'level' => [
                                    'type' => 'STRING',
                                    'description' => 'سطح دوره: beginner, intermediate, advanced',
                                ],
                                'limit' => [
                                    'type' => 'INTEGER',
                                    'description' => 'تعداد نتایج (پیش‌فرض ۵)',
                                ],
                            ],
                        ],
                    ],
                    [
                        'name' => 'get_course_details',
                        'description' => 'دریافت جزئیات کامل یک دوره آموزشی خاص شامل استاد، دروس، مدت زمان و امتیاز.',
                        'parameters' => [
                            'type' => 'OBJECT',
                            'properties' => [
                                'course_identifier' => [
                                    'type' => 'STRING',
                                    'description' => 'نام دوره یا اسلاگ (slug) دوره',
                                ],
                            ],
                            'required' => ['course_identifier'],
                        ],
                    ],
                    [
                        'name' => 'list_teachers',
                        'description' => 'دریافت لیست اساتید فعال ادورا تک و تخصص‌ها یا دوره‌هایی که تدریس می‌کنند.',
                        'parameters' => [
                            'type' => 'OBJECT',
                            'properties' => [
                                'query' => [
                                    'type' => 'STRING',
                                    'description' => 'جستجوی نام یا بیوگرافی استاد (اختیاری)',
                                ],
                            ],
                        ],
                    ],
                    [
                        'name' => 'get_upcoming_events',
                        'description' => 'دریافت لیست رویدادها، وبینارها و کارگاه‌های زنده پیش‌رو.',
                        'parameters' => [
                            'type' => 'OBJECT',
                            'properties' => [
                                'limit' => [
                                    'type' => 'INTEGER',
                                    'description' => 'تعداد رویدادها (پیش‌فرض ۵)',
                                ],
                            ],
                        ],
                    ],
                    [
                        'name' => 'get_free_books',
                        'description' => 'دریافت لیست کتاب‌ها و منابع آموزشی رایگان قابل دانلود در سایت.',
                        'parameters' => [
                            'type' => 'OBJECT',
                            'properties' => [
                                'query' => [
                                    'type' => 'STRING',
                                    'description' => 'کلمه کلیدی جستجوی کتاب (اختیاری)',
                                ],
                            ],
                        ],
                    ],
                    [
                        'name' => 'get_active_classes',
                        'description' => 'بررسی جلسات آنلاین و کلاس‌های زنده در حال برگزاری در همین لحظه.',
                        'parameters' => [
                            'type' => 'OBJECT',
                            'properties' => (object)[],
                        ],
                    ],
                    [
                        'name' => 'get_categories',
                        'description' => 'دریافت تمام دسته‌بندی‌های آموزشی موجود در پلتفرم ادورا تک.',
                        'parameters' => [
                            'type' => 'OBJECT',
                            'properties' => (object)[],
                        ],
                    ],
                    [
                        'name' => 'send_unresolved_issue_to_admin',
                        'description' => 'ارسال گزارش و ایمیل خلاصه مشکل یا سوال حل‌نشده کاربر به ایمیل مدیریت و پشتیبانی ادورا، زمانی که دستیار هوش مصنوعی پاسخ یا راه حل مشکل را در دیتابیس سایت ندارد یا مشکلی رخ داده که نیاز به بررسی تیم پشتیبانی است.',
                        'parameters' => [
                            'type' => 'OBJECT',
                            'properties' => [
                                'user_question' => [
                                    'type' => 'STRING',
                                    'description' => 'متن سوال یا مشکل مطرح‌شده توسط کاربر',
                                ],
                                'issue_summary' => [
                                    'type' => 'STRING',
                                    'description' => 'خلاصه دقیق مشکل یا دلیل عدم توانایی در پاسخگویی برای ایمیل به ادمین',
                                ],
                                'user_contact' => [
                                    'type' => 'STRING',
                                    'description' => 'اطلاعات تماس یا ایمیل کاربر در صورت ذکر شدن (اختیاری)',
                                ],
                            ],
                            'required' => ['user_question', 'issue_summary'],
                        ],
                    ],
                ],
            ],
        ];

        $langRule = ($language === 'fa')
            ? "قانون زبان: کاربر زبان فارسی را انتخاب کرده است. پاسخ خود را حتماً به زبان فارسی روان، شیوا و محترمانه بنویسید."
            : "LANGUAGE RULE: The user selected English. You MUST respond completely in clear, natural, and professional English.";

        $topicRule = "";
        if (!empty($topic) && $topic !== 'all') {
            $topicRule = "\nموضوع فیلتر شده توسط کاربر: [{$topic}]. کاربر سوال خود را مشخصاً در این بخش مطرح کرده است. اولویت شما پاسخگویی متمرکز و تخصصی در این حوزه می‌باشد.\n";
        }

        $systemInstruction = "شما دستیار هوشمند و رسمی پشتیبانی آموزشی پلتفرم «ادورا» (Edvora / edvoratech.com) هستید. 
وظیفه شما فقط و فقط راهنمایی و حل مشکلات کاربران در خصوص وب‌سایت، امکانات، دوره‌ها، اساتید، کتاب‌ها، کلاس‌های زنده و رویدادهای ادورا است.

{$langRule}
{$topicRule}

قوانین الزامی و حیاتی (MANDATORY OPERATIONAL RULES):
۱. **محدوده عملکرد:** این چت‌بات فقط و فقط برای حل کردن مشکلات و سوالات مربوط به وب‌سایت ادورا است، نه حل کردن کد یا مسائل سیاسی و حقوقی.
۲. **عدم پذیرش فایل و ویس:** فایل یا ویس نمی‌گیرید. همواره مثل یک مربی و راهنمای آموزشی شایسته، محترمانه و دقیق با کاربر صحبت کنید.
۳. **جملات کوتاه و کاربردی:** از گفتن جملات طولانی خودداری کنید. تمام جملات و پاسخ‌ها باید کاملاً کوتاه، مختصر، مفید و کاربردی باشند (حداکثر ۱ تا ۳ جمله خلاصه).
۴. **عبارت پیش‌فرض برای رد درخواست‌های نامربوط:** در مواجهه با ارسال کدهای برنامه‌نویسی کاربران، درخواست حل یا دیباگ کد، یا سوالات متفرقه و خارج از وب‌سایت (سیاسی، حقوقی، تکالیف و...)، دقیقاً و عینا از این پاسخ استفاده کنید:
«من فقط میتوانم به شما در زمینه استفاده از امکانات، دورهها و سوالات مربوط به وبسایت ادورا کمک کنم و مجاز به حل کد یا بررسی تکهکدهای برنامهنویسی نیستم. برای این مورد میتوانید از انجمنها یا سایر ابزارها استفاده کنید.»
(در زبان انگلیسی: \"I can only assist you with website features, courses, and inquiries regarding the Edvora platform, and I am not authorized to solve code or review programming snippets. For this, you can use developer forums or other tools.\")
۵. **ارجاع مشکلات حل‌نشده به ایمیل مدیریت:** اگر مشکلی را دیدید که نمی‌دانید چگونه حل می‌شود یا در دیتابیس وب‌سایت پاسخی برای آن وجود ندارد، بلافاصله ابزار «send_unresolved_issue_to_admin» را صدا بزنید تا خلاصه مشکل به ایمیل مدیریت ارسال شود. پس از ارسال، در یک جمله کوتاه و آرامش‌بخش به کاربر اطلاع دهید که:
«این موضوع ثبت و برای بررسی و پیگیری به ایمیل مدیریت و پشتیبانی ادورا ارسال شد. همکاران ما به زودی آن را بررسی می‌کنند.»

اطلاعات زنده کنونی سایت:
- تعداد دوره‌ها: {$platformSnapshot['total_courses']}
- تعداد اساتید: {$platformSnapshot['total_teachers']}
- تعداد دانشجویان ثبت‌نام شده: {$platformSnapshot['total_students']}
- تعداد دوره‌های تکمیل شده: {$platformSnapshot['completed_enrollments']}
- تعداد رویدادها/وبینارها: {$platformSnapshot['total_events']}
- تعداد کتاب‌های رایگان: {$platformSnapshot['total_books']}
- کلاس‌های زنده هم‌اکنون: {$platformSnapshot['active_classes_count']}

{$scopedContextText}";

        // Build request contents
        $contents = [];

        // Append historical turns
        foreach ($history as $item) {
            $role = ($item['role'] ?? 'user') === 'user' ? 'user' : 'model';
            $text = $item['text'] ?? ($item['content'] ?? '');
            if (!empty($text)) {
                $contents[] = [
                    'role' => $role,
                    'parts' => [['text' => $text]],
                ];
            }
        }

        // Append current user message
        $contents[] = [
            'role' => 'user',
            'parts' => [['text' => $userMessage]],
        ];

        $payload = [
            'system_instruction' => [
                'parts' => [['text' => $systemInstruction]],
            ],
            'contents' => $contents,
            'tools' => $tools,
            'generationConfig' => [
                'temperature' => 0.4,
                'topP' => 0.95,
                'maxOutputTokens' => 1500,
            ],
        ];

        $usedSources = $initialSources;
        $apiResponse = $this->callGeminiApi($payload);

        if (!$apiResponse['success']) {
            return [
                'success' => false,
                'message' => $apiResponse['error'] ?? 'خطا در ارتباط با هوش مصنوعی.',
                'sources' => [],
            ];
        }

        $candidate = $apiResponse['data']['candidates'][0] ?? null;
        if (!$candidate) {
            return [
                'success' => false,
                'message' => 'پاسخی از مدل هوش مصنوعی دریافت نشد.',
                'sources' => [],
            ];
        }

        $parts = $candidate['content']['parts'] ?? [];
        $functionCalls = [];
        $responseText = '';

        foreach ($parts as $part) {
            if (isset($part['functionCall'])) {
                $functionCalls[] = $part['functionCall'];
            }
            if (isset($part['text'])) {
                $responseText .= $part['text'];
            }
        }

        // If Gemini called one or more tools (Function Calling)
        if (!empty($functionCalls)) {
            $toolResponsesParts = [];

            foreach ($functionCalls as $call) {
                $fnName = $call['name'];
                $fnArgs = $call['args'] ?? [];
                $toolResult = $this->executeTool($fnName, $fnArgs);
                $usedSources[] = $toolResult['source_name'] ?? $fnName;

                $toolResponsesParts[] = [
                    'functionResponse' => [
                        'name' => $fnName,
                        'response' => [
                            'name' => $fnName,
                            'content' => $toolResult['data'],
                        ],
                    ],
                ];
            }

            // Append assistant tool call content & tool response content to conversation
            $contents[] = [
                'role' => 'model',
                'parts' => $parts,
            ];
            $contents[] = [
                'role' => 'user',
                'parts' => $toolResponsesParts,
            ];

            $secondPayload = [
                'system_instruction' => [
                    'parts' => [['text' => $systemInstruction]],
                ],
                'contents' => $contents,
                'generationConfig' => [
                    'temperature' => 0.4,
                    'maxOutputTokens' => 1500,
                ],
            ];

            $secondResponse = $this->callGeminiApi($secondPayload);

            if ($secondResponse['success']) {
                $secondParts = $secondResponse['data']['candidates'][0]['content']['parts'] ?? [];
                $responseText = '';
                foreach ($secondParts as $sp) {
                    if (isset($sp['text'])) {
                        $responseText .= $sp['text'];
                    }
                }
            }
        }

        return [
            'success' => true,
            'message' => trim($responseText),
            'sources' => array_values(array_unique($usedSources)),
        ];
    }

    /**
     * Call Gemini REST API trying working models in sequence.
     */
    protected function callGeminiApi(array $payload): array
    {
        foreach ($this->models as $model) {
            $url = "https://generativelanguage.googleapis.com/v1beta/models/{$model}:generateContent?key=" . $this->apiKey;

            try {
                $response = Http::withoutVerifying()
                    ->timeout(20)
                    ->post($url, $payload);

                if ($response->successful()) {
                    return [
                        'success' => true,
                        'model' => $model,
                        'data' => $response->json(),
                    ];
                }

                Log::warning("Gemini model {$model} returned status " . $response->status() . ": " . $response->body());
            } catch (\Throwable $e) {
                Log::error("Gemini API Exception for model {$model}: " . $e->getMessage());
            }
        }

        return [
            'success' => false,
            'error' => 'سرویس هوش مصنوعی در حال حاضر پاسخگو نیست. لطفا دقایقی دیگر تلاش کنید.',
        ];
    }

    /**
     * Execute local database tool functions called by Gemini.
     */
    protected function executeTool(string $name, array $args): array
    {
        switch ($name) {
            case 'get_platform_summary':
                return [
                    'source_name' => 'آمار پلتفرم ادورا تک',
                    'data' => $this->getPlatformSummaryData(),
                ];

            case 'search_courses':
                $query = $args['query'] ?? '';
                $level = $args['level'] ?? null;
                $limit = min((int)($args['limit'] ?? 5), 10);

                $coursesQuery = Course::with(['teacher:id,name', 'category:id,name'])
                    ->where('status', '!=', 'draft');

                if (!empty($query)) {
                    $coursesQuery->where(function ($q) use ($query) {
                        $q->where('title', 'like', "%{$query}%")
                          ->orWhere('description', 'like', "%{$query}%");
                    });
                }

                if (!empty($level)) {
                    $coursesQuery->where('level', $level);
                }

                $courses = $coursesQuery->latest()->limit($limit)->get()->map(function ($c) {
                    return [
                        'id' => $c->id,
                        'title' => $c->title,
                        'slug' => $c->slug,
                        'level' => $c->level,
                        'duration_hours' => $c->duration_hours,
                        'category' => $c->category->name ?? 'عمومی',
                        'teacher' => $c->teacher->name ?? 'ادورا تک',
                        'rating' => $c->rating,
                        'enrolled_count' => $c->enrolled_count ?? 0,
                    ];
                });

                return [
                    'source_name' => 'دوره‌های آموزشی',
                    'data' => [
                        'count' => $courses->count(),
                        'courses' => $courses->toArray(),
                    ],
                ];

            case 'get_course_details':
                $identifier = $args['course_identifier'] ?? '';
                $course = Course::with(['teacher:id,name', 'category:id,name', 'lessons' => function ($q) {
                    $q->select('id', 'course_id', 'title', 'duration', 'position')->orderBy('position');
                }])
                ->where('slug', $identifier)
                ->orWhere('title', 'like', "%{$identifier}%")
                ->first();

                if (!$course) {
                    return [
                        'source_name' => 'دوره‌های آموزشی',
                        'data' => ['found' => false, 'message' => "دوره‌ای با مشخصات '{$identifier}' یافت نشد."],
                    ];
                }

                return [
                    'source_name' => "جزئیات دوره {$course->title}",
                    'data' => [
                        'found' => true,
                        'title' => $course->title,
                        'slug' => $course->slug,
                        'description' => strip_tags($course->description),
                        'level' => $course->level,
                        'duration_hours' => $course->duration_hours,
                        'teacher' => $course->teacher->name ?? 'ادورا تک',
                        'category' => $course->category->name ?? 'عمومی',
                        'rating' => $course->rating,
                        'lessons_count' => $course->lessons->count(),
                        'lessons' => $course->lessons->take(10)->map(fn ($l) => [
                            'title' => $l->title,
                            'duration' => $l->duration,
                        ])->toArray(),
                    ],
                ];

            case 'list_teachers':
                $query = $args['query'] ?? '';
                $teachersQuery = User::where('role', 'teacher');

                if (!empty($query)) {
                    $teachersQuery->where(function ($q) use ($query) {
                        $q->where('name', 'like', "%{$query}%")
                          ->orWhere('bio', 'like', "%{$query}%");
                    });
                }

                $teachers = $teachersQuery->withCount('courses')->limit(8)->get()->map(fn ($t) => [
                    'name' => $t->name,
                    'bio' => $t->bio ?? 'مدرس برجسته ادورا تک',
                    'courses_count' => $t->courses_count,
                ]);

                return [
                    'source_name' => 'اساتید ادورا تک',
                    'data' => [
                        'count' => $teachers->count(),
                        'teachers' => $teachers->toArray(),
                    ],
                ];

            case 'get_upcoming_events':
                $limit = min((int)($args['limit'] ?? 5), 10);
                $events = Event::where('start_date', '>=', now())
                    ->where('status', 'active')
                    ->orderBy('start_date')
                    ->limit($limit)
                    ->get()
                    ->map(fn ($e) => [
                        'title' => $e->title,
                        'slug' => $e->slug,
                        'date' => $e->start_date ? $e->start_date->format('Y-m-d H:i') : 'به زودی',
                        'location' => $e->location ?? 'آنلاین',
                        'capacity' => $e->capacity,
                    ]);

                return [
                    'source_name' => 'رویدادها و وبینارها',
                    'data' => [
                        'count' => $events->count(),
                        'events' => $events->toArray(),
                    ],
                ];

            case 'get_free_books':
                $query = $args['query'] ?? '';
                $booksQuery = Book::query();

                if (!empty($query)) {
                    $booksQuery->where('title', 'like', "%{$query}%")
                               ->orWhere('author', 'like', "%{$query}%");
                }

                $books = $booksQuery->latest()->limit(6)->get()->map(fn ($b) => [
                    'title' => $b->title,
                    'slug' => $b->slug,
                    'author' => $b->author,
                    'category' => $b->category ?? 'عمومی',
                    'download_count' => $b->download_count ?? 0,
                ]);

                return [
                    'source_name' => 'کتاب‌های رایگان',
                    'data' => [
                        'count' => $books->count(),
                        'books' => $books->toArray(),
                    ],
                ];

            case 'get_active_classes':
                $activeSessions = ClassSession::with('course:id,title')
                    ->where('status', 'active')
                    ->get()
                    ->map(fn ($s) => [
                        'course_title' => $s->course->title ?? 'کلاس زنده',
                        'session_title' => $s->title,
                        'start_time' => $s->start_time ? $s->start_time->format('H:i') : null,
                    ]);

                return [
                    'source_name' => 'کلاس‌های زنده در حال برگزاری',
                    'data' => [
                        'count' => $activeSessions->count(),
                        'sessions' => $activeSessions->toArray(),
                    ],
                ];

            case 'get_categories':
                $categories = Category::withCount('courses')->get()->map(fn ($c) => [
                    'name' => $c->name,
                    'slug' => $c->slug,
                    'courses_count' => $c->courses_count,
                ]);

                return [
                    'source_name' => 'دسته‌بندی‌های آموزشی',
                    'data' => [
                        'categories' => $categories->toArray(),
                    ],
                ];

            case 'send_unresolved_issue_to_admin':
                $userQuestion = $args['user_question'] ?? '';
                $issueSummary = $args['issue_summary'] ?? '';
                $userContact = $args['user_contact'] ?? null;

                $adminEmail = config('app.admin_notification_email') ?: env('ADMIN_NOTIFICATION_EMAIL');
                $userName = auth()->check() ? auth()->user()->name : null;
                $userEmail = auth()->check() ? auth()->user()->email : null;

                if ($adminEmail) {
                    try {
                        \Illuminate\Support\Facades\Mail::to($adminEmail)
                            ->send(new \App\Mail\AdminAiUnresolvedIssue(
                                userQuestion: $userQuestion,
                                issueSummary: $issueSummary,
                                userContact: $userContact,
                                userName: $userName,
                                userEmail: $userEmail
                            ));

                        Log::info("[EdvoraAI] Unresolved issue sent to admin: {$adminEmail}");

                        return [
                            'source_name' => 'ارجاع به پشتیبانی ادورا',
                            'data' => [
                                'sent' => true,
                                'message' => 'خلاصه مشکل و سوال کاربر با موفقیت به ایمیل مدیریت و پشتیبانی ادورا ارسال شد.',
                            ],
                        ];
                    } catch (\Throwable $e) {
                        Log::error("[EdvoraAI] Failed to send admin escalation email: " . $e->getMessage());
                    }
                }

                return [
                    'source_name' => 'ارجاع به پشتیبانی ادورا',
                    'data' => [
                        'sent' => false,
                        'message' => 'مشکل ثبت شد اما در ارسال ایمیل خطایی رخ داد.',
                    ],
                ];

            default:
                return [
                    'source_name' => 'دیتابیس سیستم',
                    'data' => ['result' => 'اطلاعات موجود در دیتابیس استخراج شد.'],
                ];
        }
    }

    /**
     * Snapshot summary of system data.
     */
    protected function getPlatformSummaryData(): array
    {
        try {
            return [
                'total_courses' => Course::where('status', '!=', 'draft')->count(),
                'total_teachers' => User::where('role', 'teacher')->count(),
                'total_students' => User::where('role', 'student')->count(),
                'completed_enrollments' => Enrollment::where('status', 'completed')->count(),
                'total_events' => Event::where('status', 'active')->count(),
                'total_books' => Book::count(),
                'active_classes_count' => ClassSession::where('status', 'active')->count(),
            ];
        } catch (\Throwable $e) {
            return [
                'total_courses' => 0,
                'total_teachers' => 0,
                'total_students' => 0,
                'completed_enrollments' => 0,
                'total_events' => 0,
                'total_books' => 0,
                'active_classes_count' => 0,
            ];
        }
    }

    /**
     * Build targeted database context based on user-selected topic.
     */
    protected function getScopedTopicContext(string $topic, string $query): array
    {
        $context = "";
        $sources = [];

        try {
            switch ($topic) {
                case 'courses':
                    $sources[] = 'دیتابیس دوره‌های آموزشی (Courses DB)';
                    $coursesQuery = Course::with(['teacher:id,name', 'category:id,name'])
                        ->where('status', '!=', 'draft');
                    
                    if (!empty($query)) {
                        $coursesQuery->where(function ($q) use ($query) {
                            $q->where('title', 'like', "%{$query}%")
                              ->orWhere('description', 'like', "%{$query}%");
                        });
                    }

                    $courses = $coursesQuery->latest()->limit(15)->get();
                    if ($courses->isNotEmpty()) {
                        $list = $courses->map(function ($c) {
                            $desc = \Illuminate\Support\Str::limit(strip_tags($c->description), 90);
                            return "- [{$c->title}] (سطح: {$c->level}، استاد: " . ($c->teacher->name ?? 'ادورا') . "، مدت: {$c->duration_hours}h، دسته: " . ($c->category->name ?? 'عمومی') . ") - اسلاگ: /courses/{$c->slug}";
                        })->implode("\n");
                        $context = "اطلاعات اختصاصی دوره‌های آموزشی سایت:\n{$list}\n";
                    }
                    break;

                case 'teachers':
                    $sources[] = 'دیتابیس اساتید (Teachers DB)';
                    $teachers = User::where('role', 'teacher')
                        ->withCount('courses')
                        ->limit(15)
                        ->get(['id', 'name', 'bio']);
                    if ($teachers->isNotEmpty()) {
                        $list = $teachers->map(fn($t) => "- استاد [{$t->name}]: " . ($t->bio ?? 'مدرس تخصصی') . " (تعداد دوره‌ها: {$t->courses_count})")->implode("\n");
                        $context = "اطلاعات اختصاصی اساتید و مربیان پلتفرم ادورا:\n{$list}\n";
                    }
                    break;

                case 'events':
                    $sources[] = 'دیتابیس رویدادها و وبینارها (Events DB)';
                    $events = Event::where('status', 'active')
                        ->orderBy('start_date')
                        ->limit(10)
                        ->get(['id', 'title', 'slug', 'start_date', 'location']);
                    if ($events->isNotEmpty()) {
                        $list = $events->map(fn($e) => "- رویداد [{$e->title}] (تاریخ: " . ($e->start_date ? $e->start_date->format('Y-m-d H:i') : 'به زودی') . "، مکان: {$e->location}) - لینک: /events/{$e->slug}")->implode("\n");
                        $context = "اطلاعات اختصاصی رویدادها و وبینارهای ادورا:\n{$list}\n";
                    }
                    break;

                case 'books':
                    $sources[] = 'دیتابیس کتاب‌های رایگان (Books DB)';
                    $books = Book::latest()->limit(15)->get(['id', 'title', 'slug', 'author', 'category']);
                    if ($books->isNotEmpty()) {
                        $list = $books->map(fn($b) => "- کتاب [{$b->title}] (نویسنده: {$b->author}، دسته: {$b->category}) - لینک: /books/{$b->slug}")->implode("\n");
                        $context = "اطلاعات اختصاصی کتاب‌های آموزشی رایگان ادورا:\n{$list}\n";
                    }
                    break;

                case 'classes':
                    $sources[] = 'دیتابیس کلاس‌های زنده (Live Classes DB)';
                    $sessions = ClassSession::with('course:id,title')->where('status', 'active')->limit(10)->get();
                    if ($sessions->isNotEmpty()) {
                        $list = $sessions->map(fn($s) => "- کلاس زنده [{$s->title}] مربوط به دوره (" . ($s->course->title ?? 'دوره ادورا') . ") - زمان شروع: " . ($s->start_time ? $s->start_time->format('H:i') : 'نامشخص'))->implode("\n");
                        $context = "اطلاعات اختصاصی جلسات زنده آنلاین:\n{$list}\n";
                    } else {
                        $context = "در حال حاضر هیچ جلسه زنده‌ای در حال برگزاری نیست.\n";
                    }
                    break;

                default:
                    // 'all' or general query
                    break;
            }
        } catch (\Throwable $e) {
            Log::warning("Error building scoped context for {$topic}: " . $e->getMessage());
        }

        return [
            'context' => $context,
            'sources' => $sources,
        ];
    }
}
