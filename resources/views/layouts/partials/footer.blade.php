<footer class="modern-footer">
    <div class="footer-bg-effects">
        <div class="footer-particle particle-1"></div>
        <div class="footer-particle particle-2"></div>
        <div class="footer-particle particle-3"></div>
        <div class="footer-particle particle-4"></div>
        <div class="footer-floating-icon icon-1">
            <i class="bi bi-mortarboard"></i>
        </div>
        <div class="footer-floating-icon icon-2">
            <i class="bi bi-book"></i>
        </div>
        <div class="footer-floating-icon icon-3">
            <i class="bi bi-lightbulb"></i>
        </div>
    </div>

    <div class="container">
        <div class="row g-4 footer-main">
            <div class="col-lg-4">
                <div class="footer-brand">
                    <div class="brand-logo">
                        <img src="{{ asset('assets/images/logo1.jpg') }}" alt="Edvora Tech Logo" class="footer-logo" />
                        <h4>Edvora Tech</h4>
                    </div>
                    <p class="brand-description">
                        Empowering learners worldwide with quality education and innovative teaching methods.
                    </p>
                    <div class="social-links">
                        <a href="#" class="social-link facebook">
                            <i class="bi bi-facebook"></i>
                        </a>
                        <a href="#" class="social-link twitter">
                            <i class="bi bi-twitter"></i>
                        </a>
                        <a href="#" class="social-link instagram">
                            <i class="bi bi-instagram"></i>
                        </a>
                        <a href="#" class="social-link linkedin">
                            <i class="bi bi-linkedin"></i>
                        </a>
                        <a href="#" class="social-link youtube">
                            <i class="bi bi-youtube"></i>
                        </a>
                    </div>
                </div>
            </div>

            <div class="col-lg-2 col-md-6">
                <div class="footer-section">
                    <h6 class="footer-title">Quick Links</h6>
                    <ul class="footer-links">
                        <li><a href="{{ route('about') }}">About Us</a></li>
                        <li><a href="{{ route('courses.index') }}">Courses</a></li>
                        <li><a href="{{ route('teachers.index') }}">Teachers</a></li>
                        <li><a href="{{ route('events.index') }}">Events</a></li>
                        <li><a href="{{ route('leaderboard') }}">Leaderboard</a></li>
                        <li><a href="{{ route('contact') }}">Contact</a></li>
                    </ul>
                </div>
            </div>

            <div class="col-lg-2 col-md-6">
                <div class="footer-section">
                    <h6 class="footer-title">Support</h6>
                    <ul class="footer-links">
                        <li><a href="{{ route('faq') }}">FAQ</a></li>
                        <li><a href="{{ route('terms') }}">Terms of Service</a></li>
                        <li><a href="{{ route('privacy') }}">Privacy Policy</a></li>
                        <li><a href="#">Help Center</a></li>
                        <li><a href="{{ route('foundation') }}">Foundation</a></li>
                    </ul>
                </div>
            </div>

            <div class="col-lg-4">
                <div class="footer-section">
                    <h6 class="footer-title">Stay Connected</h6>
                    <p class="newsletter-description">
                        Get the latest updates on courses, events, and educational content.
                    </p>
                    <form class="newsletter-form">
                        <div class="input-group">
                            <input type="email" class="newsletter-input" placeholder="Enter your email address" required />
                            <button class="newsletter-btn" type="submit">
                                <i class="bi bi-send"></i>
                            </button>
                        </div>
                    </form>
                    <div class="footer-stats">
                        <div class="stat-item">
                            <span class="stat-number">{{ $footerStudents ?? 0 }}</span>
                            <span class="stat-label">Students</span>
                        </div>
                        <div class="stat-item">
                            <span class="stat-number">{{ $footerCourses ?? 0 }}</span>
                            <span class="stat-label">Courses</span>
                        </div>
                        <div class="stat-item">
                            <span class="stat-number">{{ $footerTeachers ?? 0 }}</span>
                            <span class="stat-label">Teachers</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="footer-bottom">
            <div class="row align-items-center">
                <div class="col-md-6">
                    <p class="copyright">
                        &copy; {{ date('Y') }} Edvora Tech. All rights reserved.
                    </p>
                </div>
                <div class="col-md-6">
                    <div class="footer-bottom-links">
                        <a href="{{ route('privacy') }}">Privacy</a>
                        <a href="{{ route('terms') }}">Terms</a>
                        <a href="#">Cookies</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</footer>
