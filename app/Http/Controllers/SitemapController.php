<?php

namespace App\Http\Controllers;

use App\Models\Book;
use App\Models\Category;
use App\Models\Course;
use App\Models\Event;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Cache;

class SitemapController extends Controller
{
    /**
     * Render the dynamic XML sitemap.
     */
    public function index(Request $request): Response
    {
        // Allow forcing a fresh render via ?refresh=1
        if ($request->boolean('refresh')) {
            Cache::forget('sitemap_xml_content');
        }

        $xml = Cache::remember('sitemap_xml_content', now()->addHours(2), function () {
            return $this->generateXml();
        });

        return response($xml, 200, [
            'Content-Type' => 'application/xml; charset=utf-8',
            'X-Robots-Tag' => 'noindex', // Prevents the sitemap file itself from appearing as a search result
        ]);
    }

    /**
     * Generate the XML string for all sitemap items.
     */
    public function generateXml(): string
    {
        $items = collect();

        // 1. Static Pages
        $staticPages = [
            ['route' => 'home',         'priority' => '1.0', 'changefreq' => 'daily'],
            ['route' => 'courses.index','priority' => '0.9', 'changefreq' => 'daily'],
            ['route' => 'teachers.index','priority' => '0.8', 'changefreq' => 'weekly'],
            ['route' => 'events.index', 'priority' => '0.8', 'changefreq' => 'daily'],
            ['route' => 'books.index',  'priority' => '0.8', 'changefreq' => 'weekly'],
            ['route' => 'leaderboard',  'priority' => '0.7', 'changefreq' => 'daily'],
            ['route' => 'roadmap',      'priority' => '0.7', 'changefreq' => 'weekly'],
            ['route' => 'foundation',   'priority' => '0.6', 'changefreq' => 'monthly'],
            ['route' => 'about',        'priority' => '0.6', 'changefreq' => 'monthly'],
            ['route' => 'story',        'priority' => '0.6', 'changefreq' => 'monthly'],
            ['route' => 'how-we-work',  'priority' => '0.6', 'changefreq' => 'monthly'],
            ['route' => 'scoring.help', 'priority' => '0.5', 'changefreq' => 'monthly'],
            ['route' => 'contact',      'priority' => '0.5', 'changefreq' => 'monthly'],
            ['route' => 'faq',          'priority' => '0.5', 'changefreq' => 'monthly'],
            ['route' => 'terms',        'priority' => '0.3', 'changefreq' => 'yearly'],
            ['route' => 'privacy',      'priority' => '0.3', 'changefreq' => 'yearly'],
        ];

        foreach ($staticPages as $page) {
            if (\Illuminate\Support\Facades\Route::has($page['route'])) {
                $items->push([
                    'loc'        => route($page['route']),
                    'lastmod'    => now()->startOfDay()->toAtomString(),
                    'changefreq' => $page['changefreq'],
                    'priority'   => $page['priority'],
                ]);
            }
        }

        // 2. Course Categories
        $categories = Category::query()
            ->whereHas('courses', fn ($q) => $q->where('status', '!=', 'draft'))
            ->whereNotNull('slug')
            ->select(['slug', 'updated_at'])
            ->get();

        foreach ($categories as $category) {
            $items->push([
                'loc'        => route('courses.index', ['category' => $category->slug]),
                'lastmod'    => $category->updated_at?->toAtomString(),
                'changefreq' => 'weekly',
                'priority'   => '0.6',
            ]);
        }

        // 3. Courses (Published / Active)
        $courses = Course::query()
            ->where('status', '!=', 'draft')
            ->whereNotNull('slug')
            ->select(['slug', 'updated_at'])
            ->orderByDesc('updated_at')
            ->get();

        foreach ($courses as $course) {
            $items->push([
                'loc'        => route('courses.detail', $course->slug),
                'lastmod'    => $course->updated_at?->toAtomString(),
                'changefreq' => 'weekly',
                'priority'   => '0.8',
            ]);
        }

        // 4. Books (Published Library)
        $books = Book::query()
            ->where('is_published', true)
            ->whereNotNull('slug')
            ->select(['slug', 'updated_at'])
            ->orderByDesc('updated_at')
            ->get();

        foreach ($books as $book) {
            $items->push([
                'loc'        => route('books.show', $book->slug),
                'lastmod'    => $book->updated_at?->toAtomString(),
                'changefreq' => 'weekly',
                'priority'   => '0.7',
            ]);
        }

        // 5. Events (Active)
        $events = Event::query()
            ->where('status', 'active')
            ->whereNotNull('slug')
            ->select(['slug', 'updated_at'])
            ->orderByDesc('updated_at')
            ->get();

        foreach ($events as $event) {
            $items->push([
                'loc'        => route('events.detail', $event->slug),
                'lastmod'    => $event->updated_at?->toAtomString(),
                'changefreq' => 'weekly',
                'priority'   => '0.8',
            ]);
        }

        // 6. Teachers (Active Profile Directory)
        $teachers = User::query()
            ->where('role', 'teacher')
            ->where('status', 'active')
            ->whereHas('teacher', fn ($q) => $q->where('is_verified', true))
            ->whereNotNull('department')
            ->where('department', '!=', '')
            ->select(['id', 'updated_at'])
            ->orderByDesc('updated_at')
            ->get();

        foreach ($teachers as $teacher) {
            $items->push([
                'loc'        => route('teachers.show', $teacher->id),
                'lastmod'    => $teacher->updated_at?->toAtomString(),
                'changefreq' => 'weekly',
                'priority'   => '0.7',
            ]);
        }

        return view('sitemap', ['items' => $items])->render();
    }
}
