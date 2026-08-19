@extends('layouts.app')

@section('title', 'Free Books - Edvora Tech')
@section('meta_description', 'Download and read free PDF books on Edvora Tech. No login required.')

@push('styles')
    <link rel="stylesheet" href="{{ asset('assets/css/books.css') }}">
@endpush

@section('content')
    <section class="books-hero">
        <div class="container">
            <h1 class="books-hero__title"><i class="bi bi-book-half me-2"></i> Free Books Library</h1>
            <p class="books-hero__desc">Explore our collection of free PDF books. Search by title, author, or category, and start reading instantly without any account.</p>
            <div class="books-hero__stats">
                <div class="books-hero__stat"><i class="bi bi-book"></i> <span>{{ $books->total() }} Books Available</span></div>
                <div class="books-hero__stat"><i class="bi bi-download"></i> <span>Download & Read Free</span></div>
            </div>
        </div>
    </section>

    <div class="container">
        <form method="GET" action="{{ route('books.index') }}" class="books-toolbar">
            <div class="books-toolbar__search">
                <i class="bi bi-search"></i>
                <input type="text" name="search" value="{{ request('search') }}" placeholder="Search books, authors, topics..." aria-label="Search books">
            </div>

            <div class="books-toolbar__filter">
                <select name="category" aria-label="Filter by category" onchange="this.form.submit()">
                    <option value="">All Categories</option>
                    @foreach ($categories as $cat)
                        <option value="{{ $cat->id }}" {{ request('category') == $cat->id ? 'selected' : '' }}>
                            {{ $cat->name }}
                        </option>
                    @endforeach
                </select>
            </div>

            <button type="submit" class="books-toolbar__btn btn btn-dark">
                <i class="bi bi-funnel-fill"></i> Filter
            </button>

            @if (request('search') || request('category'))
                <a href="{{ route('books.index') }}" class="books-toolbar__btn btn btn-outline-secondary">
                    <i class="bi bi-x-lg"></i> Clear
                </a>
            @endif
        </form>

        @if ($books->count())
            <div class="books-grid">
                @foreach ($books as $book)
                    <article class="book-card">
                        <a href="{{ route('books.show', $book->slug) }}" class="book-card__media">
                            <img src="{{ $book->coverUrl() }}" alt="{{ $book->title }} cover">
                            <span class="book-card__badge">PDF</span>
                        </a>
                        <div class="book-card__body">
                            @if ($book->category)
                                <div class="book-card__category">{{ $book->category->name }}</div>
                            @endif
                            <h3 class="book-card__title">{{ $book->title }}</h3>
                            @if ($book->author)
                                <div class="book-card__author"><i class="bi bi-person-circle me-1"></i> {{ $book->author }}</div>
                            @endif
                            <p class="book-card__desc">{!! \Illuminate\Support\Str::limit(strip_tags($book->description), 110) !!}</p>
                            <div class="book-card__actions">
                                <a href="{{ route('books.view', $book->slug) }}" target="_blank" class="book-card__btn book-card__btn--secondary">
                                    <i class="bi bi-eye"></i> Read
                                </a>
                                <a href="{{ route('books.download', $book->slug) }}" class="book-card__btn book-card__btn--primary">
                                    <i class="bi bi-download"></i> Download
                                </a>
                            </div>
                        </div>
                    </article>
                @endforeach
            </div>

            <div class="d-flex justify-content-center pb-5">
                {{ $books->links() }}
            </div>
        @else
            <div class="books-empty">
                <i class="bi bi-search"></i>
                <h3>No books found</h3>
                <p>Try adjusting your search or filter, or check back later for new additions.</p>
            </div>
        @endif
    </div>
@endsection
