@extends('layouts.app')

@section('title', 'Competitions - Edvora Tech')

@section('body-class', 'competitions-page')

@push('styles')
<link href="{{ asset('assets/css/competitions.css') }}" rel="stylesheet" />
<link href="{{ asset('assets/css/events-pages.css') }}" rel="stylesheet" />
<link href="{{ asset('assets/css/courses-pages.css') }}" rel="stylesheet" />
<link href="{{ asset('assets/css/beta-notice.css') }}" rel="stylesheet" />
@endpush

@section('content')
<section class="competitions-hero-section">
    <div class="container text-center text-white position-relative" style="z-index: 10;">
        <div class="row justify-content-center">
            <div class="col-lg-10">
                <div class="premium-badge-competitions">
                    <i class="bi bi-trophy-fill"></i>
                    <span>Elite Competitions</span>
                </div>

                <h1 class="competitions-hero-title">
                    <span>Elite Coding</span>
                    <span>Competitions</span>
                </h1>

                <p class="competitions-hero-subtitle">
                    Join the world's most innovative developer community. Test your skills, compete with peers, and win amazing prizes in our global coding challenges.
                </p>

                <div class="competitions-cta-group">
                    <button class="btn-comp-primary">
                        <i class="bi bi-lightning-charge-fill me-2"></i>Start Competing
                    </button>
                    <button class="btn-comp-outline">
                        <i class="bi bi-play-circle me-2"></i>How It Works
                    </button>
                </div>
            </div>
        </div>
    </div>
</section>

<section class="status-tabs-section">
    <div class="container">
        <div class="row g-3">
            <div class="col-lg-3 col-6">
                <button class="status-tab-btn active" data-status="active">
                    <i class="bi bi-broadcast"></i> Active
                </button>
            </div>
            <div class="col-lg-3 col-6">
                <button class="status-tab-btn" data-status="upcoming">
                    <i class="bi bi-calendar-check"></i> Upcoming
                </button>
            </div>
            <div class="col-lg-3 col-6">
                <button class="status-tab-btn" data-status="completed">
                    <i class="bi bi-trophy"></i> Past Results
                </button>
            </div>
            <div class="col-lg-3 col-6">
                <button class="status-tab-btn" data-status="leaderboard">
                    <i class="bi bi-bar-chart-fill"></i> Leaderboard
                </button>
            </div>
        </div>
    </div>
</section>

@if($featuredCompetition)
<section class="py-5">
    <div class="container">
        <div class="row justify-content-center mb-5">
            <div class="col-lg-10">
                <div class="card featured-competition-card border-0 shadow-lg overflow-hidden">
                    <div class="row g-0">
                        <div class="col-md-6">
                            <img src="{{ $featuredCompetition->thumbnail ?? 'https://images.unsplash.com/photo-1517077304055-6e89abbf09b0?w=600&h=400&fit=crop' }}" class="img-fluid h-100 object-fit-cover" alt="Featured Competition">
                        </div>
                        <div class="col-md-6">
                            <div class="card-body p-5">
                                <div class="d-flex align-items-center mb-3">
                                    <span class="badge me-2" style="background: var(--primary-blue);">Featured</span>
                                    <span class="badge" style="background: rgba(40, 167, 69, 0.15); color: #28a745; border: 1px solid rgba(40, 167, 69, 0.3);">{{ ucfirst($featuredCompetition->status) }}</span>
                                </div>
                                <h3 class="fw-bold mb-3" style="color: var(--primary-blue);">{{ $featuredCompetition->title }}</h3>
                                <p class="text-muted mb-4">{{ Str::limit($featuredCompetition->description, 150) }}</p>

                                <div class="row g-3 mb-4">
                                    <div class="col-6">
                                        <div class="d-flex align-items-center">
                                            <i class="bi bi-trophy me-2 text-warning"></i>
                                            <div>
                                                <small class="text-muted d-block">Prize Pool</small>
                                                <strong style="color: #333;">
                                                    @if($featuredCompetition->prizes)
                                                        @php
                                                            $total = 0;
                                                            foreach($featuredCompetition->prizes as $prize) {
                                                                if(is_string($prize)) {
                                                                    preg_match('/\$([0-9,]+)/', $prize, $matches);
                                                                    if(isset($matches[1])) {
                                                                        $total += (int) str_replace(',', '', $matches[1]);
                                                                    }
                                                                }
                                                            }
                                                            echo '$' . number_format($total);
                                                        @endphp
                                                    @else
                                                        $0
                                                    @endif
                                                </strong>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-6">
                                        <div class="d-flex align-items-center">
                                            <i class="bi bi-calendar3 me-2" style="color: var(--primary-blue);"></i>
                                            <div>
                                                <small class="text-muted d-block">Deadline</small>
                                                <strong style="color: #333;">{{ $featuredCompetition->end_date?->format('M d, Y') ?? 'TBD' }}</strong>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-6">
                                        <div class="d-flex align-items-center">
                                            <i class="bi bi-people me-2" style="color: var(--primary-blue);"></i>
                                            <div>
                                                <small class="text-muted d-block">Participants</small>
                                                <strong style="color: #333;">{{ number_format($featuredCompetition->participants_count) }}+</strong>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-6">
                                        <div class="d-flex align-items-center">
                                            <i class="bi bi-clock me-2" style="color: var(--primary-blue);"></i>
                                            <div>
                                                <small class="text-muted d-block">Time Left</small>
                                                <strong id="featuredCountdown" style="color: #333;" data-end-date="{{ $featuredCompetition->end_date?->format('Y-m-d') }}">Calculating...</strong>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="d-flex gap-2">
                                    <a href="{{ route('competitions.detail', $featuredCompetition->id) }}" class="btn btn-primary flex-fill">
                                        Join Competition
                                    </a>
                                    <a href="{{ route('competitions.detail', $featuredCompetition->id) }}" class="btn btn-outline-primary">
                                        <i class="bi bi-info-circle me-1"></i>View Details
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
@endif

<section class="py-5" style="background-color: #EEEEEE;">
    <div class="container">
        <div class="row mb-4">
            <div class="col-lg-8">
                <h2 class="fw-bold" style="color: #1F8FFF;">All Competitions</h2>
                <p class="text-muted">Find competitions that match your skills and interests</p>
            </div>
            <div class="col-lg-4">
                <div class="input-group">
                    <span class="input-group-text"><i class="bi bi-search"></i></span>
                    <input type="text" class="form-control" id="competitionSearch" placeholder="Search competitions...">
                </div>
            </div>
        </div>

        <div id="competitionsGrid" class="row g-4">
        </div>

        <div class="text-center mt-5">
            <button id="loadMoreCompetitions" class="btn btn-primary btn-lg" style="background-color: #1F8FFF; border-color: #1F8FFF;">
                <i class="bi bi-plus-circle me-2"></i>Load More Competitions
            </button>
        </div>
    </div>
</section>

<div class="modal fade" id="competitionModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Competition Details</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body" id="competitionModalBody">
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                <button type="button" class="btn btn-primary" style="background-color: #1F8FFF; border-color: #1F8FFF;">
                    Join Competition
                </button>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script src="{{ asset('assets/js/competitions.js') }}"></script>
@endpush
