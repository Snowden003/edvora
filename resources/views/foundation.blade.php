@extends('layouts.app')

@section('title', 'Foundation - Edvora Tech')

@push('styles')
<link href="{{ asset('assets/css/foundation.css') }}" rel="stylesheet" />
<link href="{{ asset('assets/css/dashboard.css') }}" rel="stylesheet" />
<link href="{{ asset('assets/css/events-pages.css') }}" rel="stylesheet" />
<link href="{{ asset('assets/css/courses-pages.css') }}" rel="stylesheet" />
<link href="{{ asset('assets/css/auth-pages.css') }}" rel="stylesheet" />
@endpush

@section('content')
<!-- Hero Section with Donation Form -->
<section class="foundation-hero">
    <div class="foundation-bg">
        <div class="floating-shape shape-1"></div>
        <div class="floating-shape shape-2"></div>
        <div class="floating-shape shape-3"></div>
    </div>
    
    <div class="container">
        <div class="row align-items-center min-vh-100 py-5">
            <!-- Left: Info -->
            <div class="col-lg-5 mb-5 mb-lg-0">
                <div class="foundation-info">
                    <div class="foundation-badge">
                        <i class="fas fa-gem"></i>
                        <span>Edvora Foundation</span>
                    </div>
                    
                    <h1 class="foundation-title">
                        Support the Future of
                        <span class="highlight">Technology</span>
                    </h1>
                    
                    <p class="foundation-desc">
                        Your contribution helps Iranian youth access quality technology education. 
                        Every donation makes a difference.
                    </p>
                    
                    <!-- Stats -->
                    <div class="foundation-stats">
                        <div class="stat-box">
                            <span class="stat-num">{{ number_format($totalStudents) }}</span>
                            <span class="stat-text">Students</span>
                        </div>
                        <div class="stat-box">
                            <span class="stat-num">{{ number_format($totalSupporters) }}</span>
                            <span class="stat-text">Supporters</span>
                        </div>
                        <div class="stat-box">
                            <span class="stat-num">${{ number_format($totalDonations) }}</span>
                            <span class="stat-text">Raised</span>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Right: Donation Form -->
            <div class="col-lg-7">
                <div class="donation-card">
                    <div class="card-header">
                        <div class="heart-icon">
                            <i class="fas fa-heart"></i>
                        </div>
                        <h2>Make a Donation</h2>
                        <p>Choose an amount to support our students</p>
                    </div>
                    
                    <form class="donation-form" id="donationForm">
                        <!-- Quick Amount Buttons -->
                        <div class="amount-grid">
                            <button type="button" class="amount-chip" data-amount="25">$25</button>
                            <button type="button" class="amount-chip" data-amount="50">$50</button>
                            <button type="button" class="amount-chip" data-amount="100">$100</button>
                            <button type="button" class="amount-chip" data-amount="250">$250</button>
                            <button type="button" class="amount-chip" data-amount="500">$500</button>
                            <button type="button" class="amount-chip other">Other</button>
                        </div>
                        
                        <!-- Custom Amount Input -->
                        <div class="custom-amount-wrapper">
                            <span class="currency">$</span>
                            <input type="number" class="custom-input" id="customAmount" placeholder="Enter amount">
                        </div>
                        
                        <!-- Personal Info -->
                        <div class="form-fields">
                            <div class="input-group">
                                <i class="fas fa-user"></i>
                                <input type="text" id="donorName" placeholder="Full Name" required>
                            </div>
                            <div class="input-group">
                                <i class="fas fa-envelope"></i>
                                <input type="email" id="donorEmail" placeholder="Email Address" required>
                            </div>
                            <div class="input-group">
                                <i class="fas fa-phone"></i>
                                <input type="tel" id="donorPhone" placeholder="Phone Number" required>
                            </div>
                            <div class="input-group textarea">
                                <i class="fas fa-comment"></i>
                                <textarea id="donorMessage" rows="2" placeholder="Message (Optional)"></textarea>
                            </div>
                        </div>
                        
                        <!-- Privacy Checkbox -->
                        <label class="privacy-check">
                            <input type="checkbox" id="showName" checked>
                            <span class="checkmark"></span>
                            <span class="label-text">Show my name in supporters list</span>
                        </label>
                        
                        <!-- Submit Button -->
                        <button type="submit" class="btn-donate-submit">
                            <i class="fas fa-heart"></i>
                            <span>Donate Now</span>
                            <div class="btn-loader">
                                <i class="fas fa-spinner fa-spin"></i>
                            </div>
                        </button>
                        
                        <!-- Security Note -->
                        <div class="security-note">
                            <i class="fas fa-lock"></i>
                            <span>Secure payment. Your information is protected.</span>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Impact Section -->
<section class="impact-section">
    <div class="container">
        <div class="section-header text-center">
            <h2>Your Impact</h2>
            <p>See how donations help our students</p>
        </div>
        
        <div class="impact-cards">
            <div class="impact-card">
                <div class="card-icon blue">
                    <i class="fas fa-laptop"></i>
                </div>
                <h3>Equipment</h3>
                <p>Provide laptops and tools for students in need</p>
                <span class="impact-price">$500</span>
            </div>
            <div class="impact-card">
                <div class="card-icon green">
                    <i class="fas fa-graduation-cap"></i>
                </div>
                <h3>Scholarship</h3>
                <p>Fund a student's entire course journey</p>
                <span class="impact-price">$2,000</span>
            </div>
            <div class="impact-card">
                <div class="card-icon purple">
                    <i class="fas fa-users"></i>
                </div>
                <h3>Community</h3>
                <p>Support workshops and community events</p>
                <span class="impact-price">$100</span>
            </div>
        </div>
    </div>
</section>

<!-- Monthly Chart -->
@if(array_sum($chartData) > 0)
<section class="chart-section">
    <div class="container">
        <div class="chart-box">
            <h3><i class="fas fa-chart-line"></i> Donation Progress</h3>
            <div class="chart-wrapper">
                <canvas id="monthlyChart"></canvas>
            </div>
        </div>
    </div>
</section>
@endif

<!-- Top Donor -->
@if($topDonor)
<section class="top-donor">
    <div class="container">
        <div class="donor-spotlight">
            <div class="crown">
                <i class="fas fa-crown"></i>
            </div>
            <img src="https://ui-avatars.com/api/?name={{ urlencode($topDonor->name) }}&size=150&background=ffd700&color=fff" alt="{{ $topDonor->name }}">
            <h3>{{ $topDonor->name }}</h3>
            <p class="donor-tag">Top Supporter</p>
            <p class="donation-amount">${{ number_format($topDonor->amount) }}</p>
            @if($topDonor->message)
            <p class="donor-quote">"{{ $topDonor->message }}"</p>
            @endif
        </div>
    </div>
</section>
@endif

    <!-- Modern Footer -->
    

    <!-- Bootstrap 5 JS -->
    
    <!-- Chart.js -->
    
    <!-- Custom JS -->
@endsection

@push('scripts')
<script>
    // Pass dynamic chart data from Laravel to JavaScript
    window.foundationChartData = {
        labels: @json($chartLabels),
        data: @json($chartData)
    };
</script>
<script src="{{ asset('assets/js/foundation.js') }}"></script>
<script src="{{ asset('assets/js/modern-footer.js') }}"></script>
@endpush
