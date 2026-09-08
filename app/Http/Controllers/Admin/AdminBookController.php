<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Book;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Inertia\Inertia;
use Inertia\Response;

class AdminBookController extends Controller
{
    /**
     * Display a listing of books and articles.
     */
    public function index(Request $request): Response
    {
        $query = Book::with('category');

        // Search Filter
        if ($search = $request->input('search')) {
            $query->where(function ($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                  ->orWhere('author', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%")
                  ->orWhere('slug', 'like', "%{$search}%");
            });
        }

        // Category Filter
        if ($categoryId = $request->input('category_id')) {
            $query->where('category_id', $categoryId);
        }

        // Status / Published Filter
        if ($request->has('is_published') && $request->input('is_published') !== '') {
            $query->where('is_published', filter_var($request->input('is_published'), FILTER_VALIDATE_BOOLEAN));
        }

        // Sorting
        $sortBy = $request->input('sort_by', 'created_at');
        $sortDir = $request->input('sort_dir', 'desc');
        $allowedSorts = ['title', 'author', 'created_at', 'download_count', 'view_count', 'is_published'];

        if (in_array($sortBy, $allowedSorts)) {
            $query->orderBy($sortBy, $sortDir === 'asc' ? 'asc' : 'desc');
        } else {
            $query->latest();
        }

        $books = $query->paginate(12)->withQueryString()->through(fn($book) => [
            'id' => $book->id,
            'title' => $book->title,
            'slug' => $book->slug,
            'author' => $book->author ?? 'نامشخص',
            'description' => $book->description,
            'category_id' => $book->category_id,
            'category_name' => $book->category?->name ?? 'بدون دسته‌بندی',
            'cover_image' => $book->cover_image ? (Str::startsWith($book->cover_image, ['http://', 'https://']) ? $book->cover_image : Storage::url($book->cover_image)) : null,
            'pdf_file' => $book->pdf_file ? Storage::url($book->pdf_file) : null,
            'is_published' => (bool)$book->is_published,
            'download_count' => (int)$book->download_count,
            'view_count' => (int)$book->view_count,
            'created_at' => $book->created_at?->format('Y-m-d H:i'),
            'updated_at' => $book->updated_at?->format('Y-m-d H:i'),
        ]);

        // KPI Summary
        $summary = [
            'total' => Book::count(),
            'published' => Book::where('is_published', true)->count(),
            'draft' => Book::where('is_published', false)->count(),
            'total_downloads' => Book::sum('download_count'),
            'total_views' => Book::sum('view_count'),
            'categories_count' => Category::whereHas('books')->count(),
        ];

        $categories = Category::select('id', 'name', 'slug')->orderBy('name')->get();

        return Inertia::render('Admin/Books/Index', [
            'books' => $books,
            'summary' => $summary,
            'categories' => $categories,
            'filters' => $request->only(['search', 'category_id', 'is_published', 'sort_by', 'sort_dir']),
        ]);
    }

    /**
     * Show the form for creating a new book.
     */
    public function create(): Response
    {
        $categories = Category::select('id', 'name', 'slug')->orderBy('name')->get();

        return Inertia::render('Admin/Books/Form', [
            'book' => null,
            'categories' => $categories,
            'isEdit' => false,
        ]);
    }

    /**
     * Store a newly created book.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'slug' => 'required|string|max:255|unique:books,slug',
            'author' => 'nullable|string|max:255',
            'category_id' => 'nullable|exists:categories,id',
            'description' => 'nullable|string',
            'is_published' => 'boolean',
            'cover_image' => 'nullable|image|mimes:jpeg,png,jpg,webp,svg|max:4096',
            'pdf_file' => 'required|file|mimes:pdf|max:51200',
        ]);

        // Upload Cover Image
        if ($request->hasFile('cover_image')) {
            $validated['cover_image'] = $request->file('cover_image')->store('books/covers', 'public');
        }

        // Upload PDF File
        if ($request->hasFile('pdf_file')) {
            $validated['pdf_file'] = $request->file('pdf_file')->store('books/pdfs', 'public');
        }

        $validated['is_published'] = $request->boolean('is_published', true);
        $validated['download_count'] = 0;
        $validated['view_count'] = 0;

        $book = Book::create($validated);

        return redirect()->route('admin.books.index')->with('success', "کتاب «{$book->title}» با موفقیت افزوده شد.");
    }

    /**
     * Show the form for editing the specified book.
     */
    public function edit(Book $book): Response
    {
        $categories = Category::select('id', 'name', 'slug')->orderBy('name')->get();

        $bookData = [
            'id' => $book->id,
            'title' => $book->title,
            'slug' => $book->slug,
            'author' => $book->author,
            'category_id' => $book->category_id,
            'description' => $book->description,
            'is_published' => (bool)$book->is_published,
            'download_count' => (int)$book->download_count,
            'view_count' => (int)$book->view_count,
            'cover_url' => $book->cover_image ? (Str::startsWith($book->cover_image, ['http://', 'https://']) ? $book->cover_image : Storage::url($book->cover_image)) : null,
            'pdf_url' => $book->pdf_file ? Storage::url($book->pdf_file) : null,
            'pdf_name' => $book->pdf_file ? basename($book->pdf_file) : null,
            'created_at' => $book->created_at?->format('Y-m-d H:i'),
            'updated_at' => $book->updated_at?->format('Y-m-d H:i'),
        ];

        return Inertia::render('Admin/Books/Form', [
            'book' => $bookData,
            'categories' => $categories,
            'isEdit' => true,
        ]);
    }

    /**
     * Update the specified book.
     */
    public function update(Request $request, Book $book)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'slug' => 'required|string|max:255|unique:books,slug,' . $book->id,
            'author' => 'nullable|string|max:255',
            'category_id' => 'nullable|exists:categories,id',
            'description' => 'nullable|string',
            'is_published' => 'boolean',
            'cover_image' => 'nullable|image|mimes:jpeg,png,jpg,webp,svg|max:4096',
            'pdf_file' => 'nullable|file|mimes:pdf|max:51200',
        ]);

        // Replace Cover Image if provided
        if ($request->hasFile('cover_image')) {
            if ($book->cover_image && Storage::disk('public')->exists($book->cover_image)) {
                Storage::disk('public')->delete($book->cover_image);
            }
            $validated['cover_image'] = $request->file('cover_image')->store('books/covers', 'public');
        } else {
            unset($validated['cover_image']);
        }

        // Replace PDF File if provided
        if ($request->hasFile('pdf_file')) {
            if ($book->pdf_file && Storage::disk('public')->exists($book->pdf_file)) {
                Storage::disk('public')->delete($book->pdf_file);
            }
            $validated['pdf_file'] = $request->file('pdf_file')->store('books/pdfs', 'public');
        } else {
            unset($validated['pdf_file']);
        }

        $validated['is_published'] = $request->boolean('is_published', $book->is_published);

        $book->update($validated);

        return redirect()->route('admin.books.index')->with('success', "اطلاعات کتاب «{$book->title}» با موفقیت به‌روزرسانی شد.");
    }

    /**
     * Remove the specified book.
     */
    public function destroy(Book $book)
    {
        $title = $book->title;

        // Delete files from storage
        if ($book->cover_image && Storage::disk('public')->exists($book->cover_image)) {
            Storage::disk('public')->delete($book->cover_image);
        }
        if ($book->pdf_file && Storage::disk('public')->exists($book->pdf_file)) {
            Storage::disk('public')->delete($book->pdf_file);
        }

        $book->delete();

        return redirect()->back()->with('success', "کتاب «{$title}» با موفقیت حذف شد.");
    }

    /**
     * Quick toggle book publish status.
     */
    public function togglePublish(Book $book)
    {
        $book->update([
            'is_published' => !$book->is_published,
        ]);

        $statusLabel = $book->is_published ? 'منتشر شد' : 'به حالت پیش‌نویس درآمد';

        return redirect()->back()->with('success', "وضعیت کتاب «{$book->title}» تغییر کرد: {$statusLabel}.");
    }

    /**
     * Handle bulk actions for books.
     */
    public function bulkAction(Request $request)
    {
        $request->validate([
            'action' => 'required|in:publish,unpublish,delete',
            'ids' => 'required|array',
            'ids.*' => 'exists:books,id',
        ]);

        $action = $request->input('action');
        $ids = $request->input('ids');

        switch ($action) {
            case 'publish':
                Book::whereIn('id', $ids)->update(['is_published' => true]);
                $msg = count($ids) . ' کتاب با موفقیت منتشر شدند.';
                break;

            case 'unpublish':
                Book::whereIn('id', $ids)->update(['is_published' => false]);
                $msg = count($ids) . ' کتاب به حالت پیش‌نویس تغییر یافتند.';
                break;

            case 'delete':
                $books = Book::whereIn('id', $ids)->get();
                foreach ($books as $book) {
                    if ($book->cover_image && Storage::disk('public')->exists($book->cover_image)) {
                        Storage::disk('public')->delete($book->cover_image);
                    }
                    if ($book->pdf_file && Storage::disk('public')->exists($book->pdf_file)) {
                        Storage::disk('public')->delete($book->pdf_file);
                    }
                    $book->delete();
                }
                $msg = count($ids) . ' کتاب با موفقیت حذف شدند.';
                break;
        }

        return redirect()->back()->with('success', $msg);
    }

    /**
     * Create category on-the-fly.
     */
    public function storeCategory(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string|max:500',
        ]);

        $slug = Str::slug($request->input('name'));
        $count = Category::where('slug', 'like', "{$slug}%")->count();
        if ($count > 0) {
            $slug = "{$slug}-" . ($count + 1);
        }

        $category = Category::create([
            'name' => $request->input('name'),
            'slug' => $slug,
            'description' => $request->input('description'),
        ]);

        if ($request->wantsJson()) {
            return response()->json([
                'success' => true,
                'category' => $category,
            ]);
        }

        return redirect()->back()->with('success', "دسته‌بندی «{$category->name}» با موفقیت اضافه شد.");
    }
}
