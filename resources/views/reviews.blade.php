@extends('layouts.app')

@section('title', 'Reviews - Edvora Tech')

@push('styles')
<link href="{{ asset('assets/css/teacher-dashboard.css') }}" rel="stylesheet" />
<link href="{{ asset('assets/css/beta-notice.css') }}" rel="stylesheet" />
@endpush

@section('content')
<div class="page-placeholder text-center">
        <h1 class="display-4 fw-bold text-primary mb-4">Reviews</h1>
        <p class="lead text-muted">This page is currently under development.</p>
        <a href="{{ auth()->user()->dashboardRoute() }}" class="btn btn-primary rounded-pill px-4 mt-3">Back to Dashboard</a>
    </div>

    <!-- Footer placeholder -->
@endsection

@push('scripts')
<script src="{{ asset('assets/js/beta-notice.js') }}"></script>
@endpush
