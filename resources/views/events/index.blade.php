@extends('layouts.app')

@section('title', 'Events - Edvora Tech')

@section('body-class', 'events-page')

@push('styles')
<link href="{{ asset('assets/css/events.css') }}" rel="stylesheet" />
<link href="{{ asset('assets/css/events-pages.css') }}" rel="stylesheet" />
<link href="{{ asset('assets/css/courses-pages.css') }}" rel="stylesheet" />
<link href="{{ asset('assets/css/beta-notice.css') }}" rel="stylesheet" />
@endpush

@section('content')
@php
    $formatStat = function (int $num): string {
        if ($num >= 1000000) {
            return round($num / 1000000, 1) . 'M+';
        }
        if ($num >= 1000) {
            return round($num / 1000, 1) . 'K+';
        }
        if ($num >= 10) {
            return $num . '+';
        }
        return (string) $num;
    };
@endphp

<x-page-hero
    layout="centered"
    badgeIcon="bi bi-gem"
    badgeText="Premium Events"
    badgeClass="badge-gold"
    titlePrefix="Discover Our Exclusive"
    highlight="Events"
    subtitle="Join interactive workshops, webinars, and conferences led by global technology leaders and mentors."
    :stats="[
        [
            'icon' => 'bi bi-calendar-check-fill',
            'value' => $formatStat($displayEventsCount ?? 0),
            'label' => ($activeEvents ?? 0) > 0 ? 'Active Events' : 'Total Events',
        ],
        [
            'icon' => 'bi bi-person-video3',
            'value' => $formatStat($totalSpeakers ?? 0),
            'label' => 'Expert Speakers',
        ],
        [
            'icon' => 'bi bi-globe-americas',
            'value' => $formatStat($totalAttendees ?? 0),
            'label' => 'Event Attendees',
        ],
    ]"
    :floatingIcons="['bi bi-calendar-event-fill', 'bi bi-people-fill', 'bi bi-globe-americas', 'bi bi-award-fill', 'bi bi-mic-fill']"
/>

<section class="py-4 gray">
    <div class="container">
        <div class="row g-3">
            <div class="col-lg-2 col-md-4 col-6">
                <button class="btn btn-outline-primary w-100 active" data-category="all">All Events</button>
            </div>
            <div class="col-lg-2 col-md-4 col-6">
                <button class="btn btn-outline-primary w-100" data-category="workshop">Workshops</button>
            </div>
            <div class="col-lg-2 col-md-4 col-6">
                <button class="btn btn-outline-primary w-100" data-category="webinar">Webinars</button>
            </div>
            <div class="col-lg-2 col-md-4 col-6">
                <button class="btn btn-outline-primary w-100" data-category="conference">Conferences</button>
            </div>
            <div class="col-lg-2 col-md-4 col-6">
                <button class="btn btn-outline-primary w-100" data-category="networking">Networking</button>
            </div>
            <div class="col-lg-2 col-md-4 col-6">
                <button class="btn btn-outline-primary w-100" data-category="bootcamp">Bootcamps</button>
            </div>
        </div>
    </div>
</section>

@if($featuredEvent)
<section class="py-5 light">
    <div class="container">
        <div class="row justify-content-center mb-5">
            <div class="col-lg-10">
                <div class="card border-0 shadow-lg overflow-hidden">
                    <div class="row g-0">
                        <div class="col-md-6">
                            <img src="{{ $featuredEvent->thumbnail ? asset('storage/' . $featuredEvent->thumbnail) : 'https://images.unsplash.com/photo-1540575467063-178a50c2df87?w=600&h=400&fit=crop' }}" class="img-fluid h-100 object-fit-cover" alt="{{ $featuredEvent->title }}">
                        </div>
                        <div class="col-md-6">
                            <div class="card-body p-5">
                                <div class="d-flex align-items-center mb-3">
                                    <span class="badge bg-danger me-2">Featured</span>
                                    <span class="badge bg-primary">{{ ucfirst($featuredEvent->type) }}</span>
                                </div>
                                <h3 class="fw-bold mb-3" style="color: #1F8FFF;">{{ $featuredEvent->title }}</h3>
                                <p class="text-muted mb-4">{{ Str::limit($featuredEvent->description, 150) }}</p>

                                <div class="row g-3 mb-4">
                                    <div class="col-6">
                                        <div class="d-flex align-items-center">
                                            <i class="bi bi-calendar3 me-2 text-primary"></i>
                                            <div>
                                                <small class="text-muted d-block">Date</small>
                                                <strong>{{ $featuredEvent->start_date?->format('M d') }}{{ $featuredEvent->end_date ? ' - ' . $featuredEvent->end_date->format('d, Y') : ', ' . $featuredEvent->start_date?->format('Y') }}</strong>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-6">
                                        <div class="d-flex align-items-center">
                                            <i class="bi bi-geo-alt me-2 text-primary"></i>
                                            <div>
                                                <small class="text-muted d-block">Location</small>
                                                <strong>{{ $featuredEvent->location ?? 'Online' }}</strong>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-6">
                                        <div class="d-flex align-items-center">
                                            <i class="bi bi-clock me-2 text-primary"></i>
                                            <div>
                                                <small class="text-muted d-block">Duration</small>
                                                <strong>{{ $featuredEvent->end_date ? $featuredEvent->start_date->diffInDays($featuredEvent->end_date) + 1 . ' Days' : '1 Day' }}</strong>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-6">
                                        <div class="d-flex align-items-center">
                                            <i class="bi bi-people me-2 text-primary"></i>
                                            <div>
                                                <small class="text-muted d-block">Attendees</small>
                                                <strong>{{ $featuredEvent->registered_count }}+</strong>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="d-flex gap-2">
                                    <a href="{{ route('events.detail', $featuredEvent->slug) }}" class="btn btn-primary flex-fill">
                                        Register Now
                                    </a>
                                    <a href="{{ route('events.detail', $featuredEvent->slug) }}" class="btn btn-outline-primary">
                                        <i class="bi bi-info-circle"></i>
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

<section class="py-5 gray">
    <div class="container">
        <div class="row mb-4">
            <div class="col-lg-8">
                <h2 class="fw-bold" style="color: #1F8FFF;">Upcoming Events</h2>
                <p class="text-muted">Discover events that match your interests and career goals</p>
            </div>
            <div class="col-lg-4">
                <div class="input-group">
                    <span class="input-group-text"><i class="bi bi-search"></i></span>
                    <input type="text" class="form-control" id="eventSearch" placeholder="Search events...">
                </div>
            </div>
        </div>

        <div id="eventsGrid" class="row g-4" data-events-url="{{ parse_url(route('api.events.index'), PHP_URL_PATH) }}">
        </div>

        <!-- Empty State -->
        <div id="emptyState" class="text-center py-5" style="display: none;">
            <div class="mb-4">
                <i class="bi bi-calendar-x fs-1" style="color: #1F8FFF; opacity: 0.5;"></i>
            </div>
            <h4 class="fw-bold text-muted mb-3">No Events Available</h4>
            <p class="text-muted mb-4">There are no upcoming events at the moment. Check back soon for exciting workshops, webinars, and conferences!</p>
            <a href="{{ route('home') }}" class="btn btn-primary" style="background-color: #1F8FFF; border-color: #1F8FFF;">
                <i class="bi bi-house me-2"></i>Go to Homepage
            </a>
        </div>

        <div class="text-center mt-5">
            <button id="loadMoreEvents" class="btn btn-primary btn-lg" style="background-color: #1F8FFF; border-color: #1F8FFF;">
                <i class="bi bi-plus-circle me-2"></i>Load More Events
            </button>
        </div>
    </div>
</section>

<div class="modal fade" id="eventModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Event Details</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body" id="eventModalBody">
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                <button type="button" class="btn btn-primary" style="background-color: #1F8FFF; border-color: #1F8FFF;">
                    Register Now
                </button>
            </div>
        </div>
    </div>
</div>

<section class="py-5 light">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-lg-8 text-center">
                <div class="card border-0 shadow-sm event-newsletter">
                    <div class="card-body p-5">
                        <i class="bi bi-envelope-fill fs-1 mb-3" style="color: #1F8FFF;"></i>
                        <h3 class="fw-bold mb-3" style="color: #1F8FFF;">Never Miss an Event</h3>
                        <p class="text-muted mb-4">Subscribe to our newsletter and get notified about upcoming events, workshops, and exclusive opportunities.</p>
                        <form class="row g-3 justify-content-center">
                            <div class="col-md-6">
                                <input type="email" class="form-control" placeholder="Enter your email address" required>
                            </div>
                            <div class="col-auto">
                                <button type="submit" class="btn btn-primary">
                                    Subscribe
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
@endsection

@push('scripts')
<script src="{{ asset('assets/js/events.js') }}"></script>
@endpush
