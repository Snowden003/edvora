<?php

namespace App\Http\Controllers;

use App\Models\Book;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\StreamedResponse;

class BookController extends Controller
{
    public function index(Request $request)
    {
        $categories = Category::query()
            ->whereHas('books', fn ($q) => $q->published())
            ->orderBy('name')
            ->get();

        $books = Book::query()
            ->published()
            ->with('category')
            ->when($request->filled('search'), fn ($q) => $q->search($request->input('search')))
            ->when($request->filled('category'), fn ($q) => $q->where('category_id', $request->input('category')))
            ->orderBy('created_at', 'desc')
            ->paginate(12)
            ->withQueryString();

        return view('books.index', compact('books', 'categories'));
    }

    public function show(string $slug)
    {
        $book = Book::query()
            ->published()
            ->with('category')
            ->where('slug', $slug)
            ->firstOrFail();

        $book->increment('view_count');

        $relatedBooks = Book::query()
            ->published()
            ->where('id', '!=', $book->id)
            ->where(fn ($q) => $q->where('category_id', $book->category_id)->orWhereNull('category_id'))
            ->limit(4)
            ->get();

        return view('books.show', compact('book', 'relatedBooks'));
    }

    public function download(string $slug)
    {
        $book = Book::query()
            ->published()
            ->where('slug', $slug)
            ->firstOrFail();

        if (! $book->pdf_file || ! Storage::disk('public')->exists($book->pdf_file)) {
            abort(404, 'PDF file not found.');
        }

        $book->increment('download_count');

        return Storage::disk('public')->download($book->pdf_file, $book->downloadFileName());
    }

    public function view(string $slug)
    {
        $book = Book::query()
            ->published()
            ->where('slug', $slug)
            ->firstOrFail();

        if (! $book->pdf_file || ! Storage::disk('public')->exists($book->pdf_file)) {
            abort(404, 'PDF file not found.');
        }

        $book->increment('view_count');

        return response()->file(Storage::disk('public')->path($book->pdf_file), [
            'Content-Type' => 'application/pdf',
            'Content-Disposition' => 'inline; filename="' . $book->downloadFileName() . '"',
        ]);
    }
}
