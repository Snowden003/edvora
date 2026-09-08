@extends('layouts.app')

@section('title', 'FAQ - Edvora Tech')

@push('styles')
<link href="{{ asset('assets/css/dashboard.css') }}" rel="stylesheet" />
<link href="{{ asset('assets/css/events-pages.css') }}" rel="stylesheet" />
<link href="{{ asset('assets/css/courses-pages.css') }}" rel="stylesheet" />
<link href="{{ asset('assets/css/auth-pages.css') }}" rel="stylesheet" />
<link href="{{ asset('assets/css/beta-notice.css') }}" rel="stylesheet" />
@endpush

@section('content')
<!-- Header -->
    

    <!-- Hero Section -->
    <x-page-hero
        layout="centered"
        badgeIcon="bi bi-patch-question-fill"
        badgeText="Help Center & Knowledge Base"
        titlePrefix="Frequently Asked"
        highlight="Questions"
        subtitle="Find clear answers to common questions about courses, exams, certificates, and learning on Edvora."
        :floatingIcons="['bi bi-question-circle', 'bi bi-lightbulb', 'bi bi-book', 'bi bi-shield-check', 'bi bi-chat-dots']"
    >
        <div class="input-group mx-auto mb-2" style="max-width: 580px;">
            <input type="text" class="form-control" id="faqSearch" placeholder="Search questions, topics, or keywords...">
            <button class="btn btn-primary px-4" type="button">
                <i class="bi bi-search"></i>
            </button>
        </div>
    </x-page-hero>

    <!-- FAQ Categories -->
    <section class="py-5" style="background-color: #EEEEEE;">
        <div class="container">
            <div class="row g-3">
                <div class="col-lg-2 col-md-4 col-6">
                    <button class="btn btn-outline-primary w-100 active" data-category="all">All</button>
                </div>
                <div class="col-lg-2 col-md-4 col-6">
                    <button class="btn btn-outline-primary w-100" data-category="general">General</button>
                </div>
                <div class="col-lg-2 col-md-4 col-6">
                    <button class="btn btn-outline-primary w-100" data-category="courses">Courses</button>
                </div>
                <div class="col-lg-2 col-md-4 col-6">
                    <button class="btn btn-outline-primary w-100" data-category="payment">Payment</button>
                </div>
                <div class="col-lg-2 col-md-4 col-6">
                    <button class="btn btn-outline-primary w-100" data-category="technical">Technical</button>
                </div>
                <div class="col-lg-2 col-md-4 col-6">
                    <button class="btn btn-outline-primary w-100" data-category="account">Account</button>
                </div>
            </div>
        </div>
    </section>

    <!-- FAQ Content -->
    <section class="py-5">
        <div class="container">
            <div class="row justify-content-center">
                <div class="col-lg-8">
                    <div class="accordion" id="faqAccordion">
                        <!-- General Questions -->
                        <div class="faq-item" data-category="general">
                            <div class="accordion-item">
                                <h2 class="accordion-header">
                                    <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse"
                                        data-bs-target="#faq1">
                                        What is Edvora?
                                    </button>
                                </h2>
                                <div id="faq1" class="accordion-collapse collapse" data-bs-parent="#faqAccordion">
                                    <div class="accordion-body">
                                        Edvora is a comprehensive online learning platform that offers high-quality
                                        courses taught by industry experts. We provide interactive learning experiences,
                                        practical projects, and certificates to help you advance your career and skills.
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="faq-item" data-category="general">
                            <div class="accordion-item">
                                <h2 class="accordion-header">
                                    <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse"
                                        data-bs-target="#faq2">
                                        How does Edvora work?
                                    </button>
                                </h2>
                                <div id="faq2" class="accordion-collapse collapse" data-bs-parent="#faqAccordion">
                                    <div class="accordion-body">
                                        Simply create an account, browse our course catalog, enroll in courses that
                                        interest you, and start learning at your own pace. You can access courses on any
                                        device, track your progress, and earn certificates upon completion.
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Course Questions -->
                        <div class="faq-item" data-category="courses">
                            <div class="accordion-item">
                                <h2 class="accordion-header">
                                    <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse"
                                        data-bs-target="#faq3">
                                        How do I enroll in a course?
                                    </button>
                                </h2>
                                <div id="faq3" class="accordion-collapse collapse" data-bs-parent="#faqAccordion">
                                    <div class="accordion-body">
                                        To enroll in a course: 1) Browse our course catalog, 2) Click on the course
                                        you're interested in, 3) Click "Enroll Now", 4) Complete your profile, 5)
                                        Start learning immediately after enrollment.
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="faq-item" data-category="courses">
                            <div class="accordion-item">
                                <h2 class="accordion-header">
                                    <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse"
                                        data-bs-target="#faq4">
                                        Do I get lifetime access to courses?
                                    </button>
                                </h2>
                                <div id="faq4" class="accordion-collapse collapse" data-bs-parent="#faqAccordion">
                                    <div class="accordion-body">
                                        Yes! Once you enroll in a course, you get lifetime access to all course
                                        materials, including future updates. You can learn at your own pace and revisit
                                        content whenever you need to.
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="faq-item" data-category="courses">
                            <div class="accordion-item">
                                <h2 class="accordion-header">
                                    <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse"
                                        data-bs-target="#faq5">
                                        Do you offer certificates?
                                    </button>
                                </h2>
                                <div id="faq5" class="accordion-collapse collapse" data-bs-parent="#faqAccordion">
                                    <div class="accordion-body">
                                        Yes! Upon successful completion of a course, you'll receive a certificate that
                                        you can download, print, or share on professional networks like LinkedIn. Our
                                        certificates are recognized by many employers.
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Payment Questions -->
                        <div class="faq-item" data-category="payment">
                            <div class="accordion-item">
                                <h2 class="accordion-header">
                                    <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse"
                                        data-bs-target="#faq6">
                                        What payment methods do you accept?
                                    </button>
                                </h2>
                                <div id="faq6" class="accordion-collapse collapse" data-bs-parent="#faqAccordion">
                                    <div class="accordion-body">
                                        We accept all major credit cards (Visa, MasterCard, American Express), PayPal,
                                        and bank transfers. All payments are processed securely through encrypted
                                        connections.
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="faq-item" data-category="payment">
                            <div class="accordion-item">
                                <h2 class="accordion-header">
                                    <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse"
                                        data-bs-target="#faq7">
                                        Are Edvora courses free?
                                    </button>
                                </h2>
                                <div id="faq7" class="accordion-collapse collapse" data-bs-parent="#faqAccordion">
                                    <div class="accordion-body">
                                        Every Edvora course is free. You can learn without paying course fees.
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Technical Questions -->
                        <div class="faq-item" data-category="technical">
                            <div class="accordion-item">
                                <h2 class="accordion-header">
                                    <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse"
                                        data-bs-target="#faq8">
                                        Can I access courses on mobile devices?
                                    </button>
                                </h2>
                                <div id="faq8" class="accordion-collapse collapse" data-bs-parent="#faqAccordion">
                                    <div class="accordion-body">
                                        Absolutely! Our platform is fully responsive and works seamlessly on
                                        smartphones, tablets, laptops, and desktops. You can learn anywhere, anytime.
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="faq-item" data-category="technical">
                            <div class="accordion-item">
                                <h2 class="accordion-header">
                                    <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse"
                                        data-bs-target="#faq9">
                                        What are the system requirements?
                                    </button>
                                </h2>
                                <div id="faq9" class="accordion-collapse collapse" data-bs-parent="#faqAccordion">
                                    <div class="accordion-body">
                                        You need a modern web browser (Chrome, Firefox, Safari, Edge) and a stable
                                        internet connection. For the best experience, we recommend using the latest
                                        version of your browser.
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Account Questions -->
                        <div class="faq-item" data-category="account">
                            <div class="accordion-item">
                                <h2 class="accordion-header">
                                    <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse"
                                        data-bs-target="#faq10">
                                        How do I create an account?
                                    </button>
                                </h2>
                                <div id="faq10" class="accordion-collapse collapse" data-bs-parent="#faqAccordion">
                                    <div class="accordion-body">
                                        Click the "Sign Up" button, fill in your details (name, email, password), choose
                                        your role (student or teacher), and verify your email address. It's that simple!
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="faq-item" data-category="account">
                            <div class="accordion-item">
                                <h2 class="accordion-header">
                                    <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse"
                                        data-bs-target="#faq11">
                                        Can I change my password?
                                    </button>
                                </h2>
                                <div id="faq11" class="accordion-collapse collapse" data-bs-parent="#faqAccordion">
                                    <div class="accordion-body">
                                        Yes, you can change your password anytime from your account settings. We also
                                        provide a "Forgot Password" option on the login page if you need to reset it.
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- More Questions -->
                        <div class="faq-item" data-category="general">
                            <div class="accordion-item">
                                <h2 class="accordion-header">
                                    <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse"
                                        data-bs-target="#faq12">
                                        How can I become an instructor?
                                    </button>
                                </h2>
                                <div id="faq12" class="accordion-collapse collapse" data-bs-parent="#faqAccordion">
                                    <div class="accordion-body">
                                        We're always looking for qualified instructors! Sign up as a teacher, submit
                                        your application with your credentials and course proposal, and our team will
                                        review it. We'll guide you through the course creation process.
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="faq-item" data-category="courses">
                            <div class="accordion-item">
                                <h2 class="accordion-header">
                                    <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse"
                                        data-bs-target="#faq13">
                                        Are there prerequisites for courses?
                                    </button>
                                </h2>
                                <div id="faq13" class="accordion-collapse collapse" data-bs-parent="#faqAccordion">
                                    <div class="accordion-body">
                                        Prerequisites vary by course and are clearly listed on each course page. We
                                        offer courses for all levels - beginner, intermediate, and advanced. Check the
                                        course description for specific requirements.
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="faq-item" data-category="technical">
                            <div class="accordion-item">
                                <h2 class="accordion-header">
                                    <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse"
                                        data-bs-target="#faq14">
                                        Can I download course materials?
                                    </button>
                                </h2>
                                <div id="faq14" class="accordion-collapse collapse" data-bs-parent="#faqAccordion">
                                    <div class="accordion-body">
                                        Yes! Most courses include downloadable resources like PDFs, code files, and
                                        project templates. You can access these materials even offline once downloaded.
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Still Have Questions -->
                    <div class="text-center mt-5">
                        <div class="card border-0 shadow-sm">
                            <div class="card-body p-5">
                                <h3 class="fw-bold mb-3" style="color: #1F8FFF;">Still Have Questions?</h3>
                                <p class="text-muted mb-4">Can't find the answer you're looking for? Our support team is
                                    here to help!</p>
                                <div class="d-flex gap-3 justify-content-center flex-wrap">
                                    <a href="{{ route('contact') }}" class="btn btn-primary"
                                        style="background-color: #1F8FFF; border-color: #1F8FFF;">
                                        <i class="bi bi-envelope me-2"></i>Contact Support
                                    </a>
                                    <button class="btn btn-outline-primary" onclick="startLiveChat()">
                                        <i class="bi bi-chat-dots me-2"></i>Live Chat
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Footer -->
    

    <!-- Bootstrap 5 JS -->
    
    <!-- Custom JS -->
@endsection

@push('scripts')
<script src="{{ asset('assets/js/faq.js') }}"></script>
<script src="{{ asset('assets/js/beta-notice.js') }}"></script>
@endpush
