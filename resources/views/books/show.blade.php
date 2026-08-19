@extends('layouts.app')

@section('title', $book->title . ' - Edvora Books')
@section('meta_description', \Illuminate\Support\Str::limit(strip_tags($book->description), 160))

@push('styles')
    <link rel="stylesheet" href="{{ asset('assets/css/books.css') }}">
@endpush

@section('content')
    <section class="books-hero">
        <div class="container">
            <h1 class="books-hero__title"><i class="bi bi-book-half me-2"></i> Book Details</h1>
            <p class="books-hero__desc">Read or download this free book instantly. No account required.</p>
        </div>
    </section>

    <div class="container book-detail">
        <div class="book-detail__grid">
            <div class="book-detail__cover">
                <img src="{{ $book->coverUrl() }}" alt="{{ $book->title }} cover">
            </div>

            <div class="book-detail__content">
                <div class="book-detail__meta">
                    @if ($book->category)
                        <span><i class="bi bi-tag-fill"></i> {{ $book->category->name }}</span>
                    @endif
                    <span><i class="bi bi-eye"></i> {{ number_format($book->view_count) }} reads</span>
                    <span><i class="bi bi-download"></i> {{ number_format($book->download_count) }} downloads</span>
                </div>

                <h1 class="book-detail__title">{{ $book->title }}</h1>

                @if ($book->author)
                    <div class="book-detail__author"><i class="bi bi-person-circle me-1"></i> By {{ $book->author }}</div>
                @endif

                <div class="book-detail__desc">
                    {!! $book->description ?? '<p>No description available.</p>' !!}
                </div>

                <div class="book-detail__actions">
                    <a href="{{ route('books.view', $book->slug) }}" target="_blank" class="book-detail__btn btn btn-dark">
                        <i class="bi bi-eye"></i> Read Online
                    </a>
                    <a href="{{ route('books.download', $book->slug) }}" class="book-detail__btn btn btn-warning text-dark">
                        <i class="bi bi-download"></i> Download PDF
                    </a>
                    <a href="{{ route('books.index') }}" class="book-detail__btn btn btn-outline-secondary">
                        <i class="bi bi-arrow-left"></i> Back to Library
                    </a>
                </div>

                <div class="book-detail__stats">
                    <span><i class="bi bi-file-earmark-pdf"></i> PDF format</span>
                    <span><i class="bi bi-unlock"></i> Free access</span>
                    <span><i class="bi bi-clock"></i> Updated {{ $book->updated_at->format('M d, Y') }}</span>
                </div>
            </div>
        </div>

        @if ($relatedBooks->count())
            <h2 class="book-detail__related-title">More Books You May Like</h2>
            <div class="books-grid">
                @foreach ($relatedBooks as $related)
                    <article class="book-card">
                        <a href="{{ route('books.show', $related->slug) }}" class="book-card__media">
                            <img src="{{ $related->coverUrl() }}" alt="{{ $related->title }} cover">
                            <span class="book-card__badge">PDF</span>
                        </a>
                        <div class="book-card__body">
                            @if ($related->category)
                                <div class="book-card__category">{{ $related->category->name }}</div>
                            @endif
                            <h3 class="book-card__title">{{ $related->title }}</h3>
                            @if ($related->author)
                                <div class="book-card__author"><i class="bi bi-person-circle me-1"></i> {{ $related->author }}</div>
                            @endif
                            <div class="book-card__actions">
                                <a href="{{ route('books.show', $related->slug) }}" class="book-card__btn book-card__btn--primary">
                                    View Book
                                </a>
                            </div>
                        </div>
                    </article>
                @endforeach
            </div>
        @endif
    </div>
@endsection
