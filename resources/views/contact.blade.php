@extends('layouts.app')

@section('title', 'Contact Us - Edvora Tech')

@push('styles')
<link href="{{ asset('assets/css/contact-minimal.css') }}" rel="stylesheet" />
<link href="{{ asset('assets/css/contact-categories.css') }}" rel="stylesheet" />
<link href="{{ asset('assets/css/beta-notice.css') }}" rel="stylesheet" />
@endpush

@section('content')
<!-- Header -->
    

    <!-- Hero Section -->
    <x-page-hero
        layout="centered"
        badgeIcon="bi bi-headset"
        badgeText="Edvora Support"
        titlePrefix="Get In"
        highlight="Touch"
        subtitle="Send us your question or feedback through the form below. Our dedicated support team reviews every message and responds promptly."
        :stats="[
            ['icon' => 'bi bi-chat-heart-fill', 'value' => '24/7', 'label' => 'Support Available'],
            ['icon' => 'bi bi-lightning-charge-fill text-warning', 'value' => 'Fast Response', 'label' => 'Direct Assistance'],
        ]"
        :floatingIcons="['bi bi-envelope', 'bi bi-chat-dots', 'bi bi-headset', 'bi bi-send', 'bi bi-shield-check']"
    />

    <!-- Premium 3D Contact Section -->
    <section class="py-5"
        style="background: linear-gradient(180deg, rgba(31, 143, 255, 0.02) 0%, rgba(31, 143, 255, 0.02) 100%); position: relative;">
        <!-- Section Background Elements -->
        <div class="section-bg-elements">
            <div class="bg-shape shape-1"></div>
            <div class="bg-shape shape-2"></div>
            <div class="bg-shape shape-3"></div>
        </div>

        <div class="container" style="position: relative; z-index: 10;">
            <div class="row g-5">
                <!-- Premium 3D Contact Form -->
                <div class="col-lg-10 mx-auto">
                    <div class="premium-contact-form" style="animation: formSlideUp 1s ease-out forwards;">
                        <div class="form-header">
                            <div class="form-icon">
                                <i class="bi bi-send-fill"></i>
                            </div>
                            <h3 class="form-title">Send us a Message</h3>
                            <div class="form-subtitle">Choose the topic of your message so we can assist you better.</div>
                        </div>

                        <!-- Step 1: Category Selection -->
                        <div id="categoryStep">
                            <p class="premium-label mb-3">What is your message about?</p>
                            <div class="row g-3" id="categoryCards">
                                <div class="col-md-4 col-6">
                                    <div class="category-card" data-category="bug_report" data-formal="false">
                                        <div class="category-icon"><i class="bi bi-bug"></i></div>
                                        <div class="category-name">Bug Report</div>
                                        <div class="category-desc">Report a problem or error</div>
                                    </div>
                                </div>
                                <div class="col-md-4 col-6">
                                    <div class="category-card" data-category="system_issue" data-formal="false">
                                        <div class="category-icon"><i class="bi bi-exclamation-triangle"></i></div>
                                        <div class="category-name">System Issue</div>
                                        <div class="category-desc">Platform or technical problems</div>
                                    </div>
                                </div>
                                <div class="col-md-4 col-6">
                                    <div class="category-card" data-category="partnership" data-formal="true">
                                        <div class="category-icon"><i class="bi bi-handshake"></i></div>
                                        <div class="category-name">Partnership</div>
                                        <div class="category-desc">Business collaboration</div>
                                        <span class="formal-badge">Formal</span>
                                    </div>
                                </div>
                                <div class="col-md-4 col-6">
                                    <div class="category-card" data-category="course_inquiry" data-formal="false">
                                        <div class="category-icon"><i class="bi bi-book"></i></div>
                                        <div class="category-name">Course Inquiry</div>
                                        <div class="category-desc">Questions about courses</div>
                                    </div>
                                </div>
                                <div class="col-md-4 col-6">
                                    <div class="category-card" data-category="account_help" data-formal="false">
                                        <div class="category-icon"><i class="bi bi-person-gear"></i></div>
                                        <div class="category-name">Account Help</div>
                                        <div class="category-desc">Account or profile support</div>
                                    </div>
                                </div>
                                <div class="col-md-4 col-6">
                                    <div class="category-card" data-category="general" data-formal="false">
                                        <div class="category-icon"><i class="bi bi-chat-dots"></i></div>
                                        <div class="category-name">General</div>
                                        <div class="category-desc">General questions or feedback</div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Step 2: Contact Form (hidden by default) -->
                        <div id="formStep" class="d-none">
                            <div class="selected-category-bar mb-4" id="selectedCategoryBar">
                                <button type="button" class="back-btn" id="backToCategories">
                                    <i class="bi bi-arrow-left"></i>
                                </button>
                                <div class="selected-category-info">
                                    <i class="bi" id="selectedCategoryIcon"></i>
                                    <span id="selectedCategoryName"></span>
                                </div>
                            </div>

                            <form id="contactForm" class="premium-form" data-url="{{ parse_url(route('contact.store'), PHP_URL_PATH) }}">
                                @csrf
                                <input type="hidden" name="category" id="categoryInput">
                                <div class="form-floating-bg"></div>
                                <div id="contactAlert" class="alert d-none mb-3"></div>

                                <div class="row g-4">
                                    <div class="col-md-6">
                                        <div class="premium-input-group">
                                            <label for="firstName" class="premium-label">First Name</label>
                                            <div class="input-container">
                                                <input type="text" class="premium-input" id="firstName" name="first_name" value="{{ Auth::check() ? explode(' ', Auth::user()->name)[0] : '' }}" required>
                                                <div class="input-border"></div>
                                                <div class="input-focus-effect"></div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="premium-input-group">
                                            <label for="lastName" class="premium-label">Last Name</label>
                                            <div class="input-container">
                                                <input type="text" class="premium-input" id="lastName" name="last_name" value="{{ Auth::check() && count(explode(' ', Auth::user()->name)) > 1 ? explode(' ', Auth::user()->name, 2)[1] : '' }}" required>
                                                <div class="input-border"></div>
                                                <div class="input-focus-effect"></div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Formal-only: Company Name -->
                                <div class="premium-input-group formal-field d-none">
                                    <label for="companyName" class="premium-label">Company / Organization Name</label>
                                    <div class="input-container">
                                        <input type="text" class="premium-input" id="companyName" name="company_name">
                                        <div class="input-border"></div>
                                        <div class="input-focus-effect"></div>
                                        <div class="input-icon">
                                            <i class="bi bi-building"></i>
                                        </div>
                                    </div>
                                </div>

                                <div class="premium-input-group">
                                    <label for="contactEmail" class="premium-label">Email Address <span class="formal-required-note formal-field d-none">(Official email preferred)</span></label>
                                    <div class="input-container">
                                        <input type="email" class="premium-input" id="contactEmail" name="email" value="{{ Auth::check() ? Auth::user()->email : '' }}" required>
                                        <div class="input-border"></div>
                                        <div class="input-focus-effect"></div>
                                        <div class="input-icon">
                                            <i class="bi bi-envelope-fill"></i>
                                        </div>
                                    </div>
                                </div>

                                <div class="premium-input-group">
                                    <label for="phone" class="premium-label">Phone Number <span class="optional normal-field">(Optional)</span><span class="formal-field d-none">(Recommended)</span></label>
                                    <div class="input-container">
                                        <input type="tel" class="premium-input" id="phone" name="phone">
                                        <div class="input-border"></div>
                                        <div class="input-focus-effect"></div>
                                        <div class="input-icon">
                                            <i class="bi bi-telephone-fill"></i>
                                        </div>
                                    </div>
                                </div>

                                <div class="premium-input-group">
                                    <label for="contactSubject" class="premium-label">Subject</label>
                                    <div class="input-container">
                                        <input type="text" class="premium-input" id="contactSubject" name="subject" placeholder="Brief description of your message" required>
                                        <div class="input-border"></div>
                                        <div class="input-focus-effect"></div>
                                    </div>
                                </div>

                                <div class="premium-input-group">
                                    <label for="contactMessage" class="premium-label">Message</label>
                                    <div class="textarea-container">
                                        <textarea class="premium-textarea" id="contactMessage" name="message" rows="6"
                                            placeholder="Tell us how we can help you..." required></textarea>
                                        <div class="textarea-border"></div>
                                        <div class="textarea-focus-effect"></div>
                                    </div>
                                </div>

                                <div class="form-submit-container">
                                    <button type="submit" class="premium-submit-btn">
                                        <div class="btn-bg-layers">
                                            <div class="btn-layer layer-1"></div>
                                            <div class="btn-layer layer-2"></div>
                                            <div class="btn-layer layer-3"></div>
                                        </div>
                                        <div class="btn-content">
                                            <div class="btn-icon">
                                                <i class="bi bi-send-fill"></i>
                                            </div>
                                            <span class="btn-text">Send Message</span>
                                        </div>
                                        <div class="btn-ripple"></div>
                                        <div class="btn-glow"></div>
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>

                <!-- Contact Information -->

            </div>
        </div>
    </section>

    <!-- FAQ Section -->
    <section class="py-5 bg-white">
        <div class="container">
            <div class="text-center mb-5">
                <h2 class="fw-bold" style="color: #1F8FFF;">Frequently Asked Questions</h2>
                <p class="text-muted">Find quick answers to common questions</p>
            </div>

            <div class="row justify-content-center">
                <div class="col-lg-8">
                    <div class="accordion" id="faqAccordion">
                        <div class="accordion-item">
                            <h2 class="accordion-header">
                                <button class="accordion-button" type="button" data-bs-toggle="collapse"
                                    data-bs-target="#faq1">
                                    How do I enroll in a course?
                                </button>
                            </h2>
                            <div id="faq1" class="accordion-collapse collapse show" data-bs-parent="#faqAccordion">
                                <div class="accordion-body">
                                    To enroll in a course, simply browse our course catalog, select the course you're
                                    interested in, and click "Enroll Now". You'll need to create an account and complete
                                    your profile before enrolling.
                                </div>
                            </div>
                        </div>

                        <div class="accordion-item">
                            <h2 class="accordion-header">
                                <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse"
                                    data-bs-target="#faq2">
                                    Do you offer certificates?
                                </button>
                            </h2>
                            <div id="faq2" class="accordion-collapse collapse" data-bs-parent="#faqAccordion">
                                <div class="accordion-body">
                                    Yes! Upon successful completion of a course, you'll receive a certificate that you
                                    can share on your LinkedIn profile or include in your resume.
                                </div>
                            </div>
                        </div>

                        <div class="accordion-item">
                            <h2 class="accordion-header">
                                <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse"
                                    data-bs-target="#faq3">
                                    Can I access courses on mobile devices?
                                </button>
                            </h2>
                            <div id="faq3" class="accordion-collapse collapse" data-bs-parent="#faqAccordion">
                                <div class="accordion-body">
                                    Absolutely! Our platform is fully responsive and works seamlessly on all devices
                                    including smartphones, tablets, and desktops.
                                </div>
                            </div>
                        </div>

                        <div class="accordion-item">
                            <h2 class="accordion-header">
                                <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse"
                                    data-bs-target="#faq4">
                                    Are Edvora courses free?
                                </button>
                            </h2>
                            <div id="faq4" class="accordion-collapse collapse" data-bs-parent="#faqAccordion">
                                <div class="accordion-body">
                                    Every Edvora course is free. You can learn without paying course fees.
                                </div>
                            </div>
                        </div>

                        <div class="accordion-item">
                            <h2 class="accordion-header">
                                <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse"
                                    data-bs-target="#faq5">
                                    How can I become an instructor?
                                </button>
                            </h2>
                            <div id="faq5" class="accordion-collapse collapse" data-bs-parent="#faqAccordion">
                                <div class="accordion-body">
                                    We're always looking for qualified instructors! Please fill out our instructor
                                    application form or contact us directly to learn about our requirements and
                                    application process.
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>


    <!-- Modern Footer -->
    

    <!-- Bootstrap 5 JS -->
    
    <!-- Custom JS -->
@endsection

@push('scripts')
<script src="{{ asset('assets/js/contact.js') }}"></script>
<script src="{{ asset('assets/js/contact-categories.js') }}"></script>
<script src="{{ asset('assets/js/contact-form.js') }}"></script>
<script src="{{ asset('assets/js/modern-footer.js') }}"></script>
<script src="{{ asset('assets/js/beta-notice.js') }}"></script>
@endpush
