@extends('layouts.app')

@section('title', 'Global AI Challenge 2024 - Competition Details - Edvora Tech')

@push('styles')
<link href="{{ asset('assets/css/competitions.css') }}" rel="stylesheet" />
<link href="{{ asset('assets/css/beta-notice.css') }}" rel="stylesheet" />
<link href="{{ asset('assets/css/pages/competitions-detail.css') }}" rel="stylesheet" />
@endpush

@section('content')
<!-- Header -->
    

    <!-- Competition Detail Hero -->
    <section class="competition-detail-hero"
        style="background: linear-gradient(135deg, #1F8FFF 0%, #1F8FFF 100%); padding: 100px 0; position: relative; overflow: hidden;">
        <!-- Floating Elements -->
        <div class="floating-elements"
            style="position: absolute; top: 0; left: 0; width: 100%; height: 100%; pointer-events: none;">
            <div class="floating-icon"
                style="position: absolute; top: 15%; left: 10%; animation: floatUpDown 6s ease-in-out infinite;">
                <i class="bi bi-cpu-fill" style="font-size: 2.5rem; color: rgba(31, 143, 255, 0.3);"></i>
            </div>
            <div class="floating-icon"
                style="position: absolute; top: 25%; right: 15%; animation: floatUpDown 8s ease-in-out infinite 2s;">
                <i class="bi bi-robot" style="font-size: 2rem; color: rgba(255, 255, 255, 0.2);"></i>
            </div>
            <div class="floating-icon"
                style="position: absolute; bottom: 20%; left: 20%; animation: floatUpDown 7s ease-in-out infinite 4s;">
                <i class="bi bi-lightning-fill" style="font-size: 2.2rem; color: rgba(31, 143, 255, 0.3);"></i>
            </div>
        </div>

        <div class="container" style="position: relative; z-index: 10;">
            <div class="row align-items-center">
                <div class="col-lg-8">
                    <!-- Breadcrumb -->
                    

                    <!-- Competition Title -->
                    <div class="competition-title" style="animation: slideInLeft 1.5s ease-out;">
                        <div class="d-flex align-items-center mb-3">
                            <span class="badge bg-danger me-2 px-3 py-2">Featured</span>
                            <span class="badge bg-success px-3 py-2">Active</span>
                        </div>
                        <h1 class="display-4 fw-bold text-white mb-3"
                            style="text-shadow: 0 10px 30px rgba(0, 0, 0, 0.5);">
                            Global AI Challenge 2024
                        </h1>
                        <p class="lead text-white mb-4" style="text-shadow: 0 5px 15px rgba(0, 0, 0, 0.3);">
                            Build innovative AI solutions that address real-world problems and compete with the best
                            minds globally.
                        </p>

                        <!-- Quick Stats -->
                        <div class="row g-3">
                            <div class="col-md-3 col-6">
                                <div class="stat-card bg-white bg-opacity-10 p-3 rounded-3"
                                    style="backdrop-filter: blur(10px);">
                                    <i class="bi bi-trophy-fill text-warning fs-3 mb-2"></i>
                                    <div class="text-white">
                                        <div class="h4 mb-0">$25,000</div>
                                        <small>Prize Pool</small>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-3 col-6">
                                <div class="stat-card bg-white bg-opacity-10 p-3 rounded-3"
                                    style="backdrop-filter: blur(10px);">
                                    <i class="bi bi-people-fill text-info fs-3 mb-2"></i>
                                    <div class="text-white">
                                        <div class="h4 mb-0">1,250+</div>
                                        <small>Participants</small>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-3 col-6">
                                <div class="stat-card bg-white bg-opacity-10 p-3 rounded-3"
                                    style="backdrop-filter: blur(10px);">
                                    <i class="bi bi-calendar3 text-success fs-3 mb-2"></i>
                                    <div class="text-white">
                                        <div class="h4 mb-0">45</div>
                                        <small>Days Left</small>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-3 col-6">
                                <div class="stat-card bg-white bg-opacity-10 p-3 rounded-3"
                                    style="backdrop-filter: blur(10px);">
                                    <i class="bi bi-star-fill text-warning fs-3 mb-2"></i>
                                    <div class="text-white">
                                        <div class="h4 mb-0">4.9</div>
                                        <small>Rating</small>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-lg-4 text-center">
                    <!-- AI Brain Visual -->
                    <div class="ai-brain-container" style="position: relative;">
                        <div class="ai-brain"
                            style="width: 200px; height: 200px; margin: 0 auto; background: linear-gradient(45deg, #1F8FFF, #1F8FFF); border-radius: 50%; display: flex; align-items: center; justify-content: center; animation: brainPulse 3s ease-in-out infinite; box-shadow: 0 0 50px rgba(31, 143, 255, 0.5);">
                            <i class="bi bi-cpu-fill"
                                style="font-size: 4rem; color: white; text-shadow: 0 0 20px rgba(255, 255, 255, 0.8);"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Competition Overview -->
    <section class="py-5">
        <div class="container">
            <div class="row">
                <div class="col-lg-8">
                    <!-- About Competition -->
                    <div class="card border-0 shadow-sm mb-4">
                        <div class="card-body p-4">
                            <h3 class="fw-bold mb-3" style="color: #1F8FFF;">
                                <i class="bi bi-info-circle me-2"></i>About This Competition
                            </h3>
                            <p class="text-muted mb-4">
                                The Global AI Challenge 2024 is a premier competition that brings together the brightest
                                minds in artificial intelligence to solve real-world problems. Participants will work on
                                innovative AI solutions across four key categories.
                            </p>

                            <!-- Key Features -->
                            <div class="row g-3">
                                <div class="col-md-6">
                                    <div class="d-flex align-items-center">
                                        <i class="bi bi-check-circle-fill text-success me-2"></i>
                                        <span>Open to all skill levels</span>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="d-flex align-items-center">
                                        <i class="bi bi-check-circle-fill text-success me-2"></i>
                                        <span>Real-world problem solving</span>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="d-flex align-items-center">
                                        <i class="bi bi-check-circle-fill text-success me-2"></i>
                                        <span>Mentorship available</span>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="d-flex align-items-center">
                                        <i class="bi bi-check-circle-fill text-success me-2"></i>
                                        <span>Industry recognition</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Competition Pricing -->
                    <div class="card border-0 shadow-sm mb-4">
                        <div class="card-body p-4">
                            <h3 class="fw-bold mb-4" style="color: #1F8FFF;">
                                <i class="bi bi-currency-dollar me-2"></i>Competition Pricing & Rewards
                            </h3>
                            <div class="row g-4">
                                <div class="col-md-4">
                                    <div class="pricing-card text-center p-4 rounded-3"
                                        style="background: linear-gradient(135deg, #1F8FFF, #FFA500); color: white; position: relative; overflow: hidden;">
                                        <div
                                            style="position: absolute; top: -20px; right: -20px; width: 60px; height: 60px; background: rgba(255,255,255,0.2); border-radius: 50%; animation: float 3s ease-in-out infinite;">
                                        </div>
                                        <i class="bi bi-trophy-fill fs-1 mb-3"></i>
                                        <h4 class="fw-bold mb-2">1st Place</h4>
                                        <div class="display-6 fw-bold mb-2">$15,000</div>
                                        <p class="mb-0">+ Certificate + Mentorship</p>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="pricing-card text-center p-4 rounded-3"
                                        style="background: linear-gradient(135deg, #BFC0C0, #A9A9A9); color: white; position: relative; overflow: hidden;">
                                        <div
                                            style="position: absolute; top: -15px; left: -15px; width: 50px; height: 50px; background: rgba(255,255,255,0.2); border-radius: 50%; animation: float 3s ease-in-out infinite 1s;">
                                        </div>
                                        <i class="bi bi-award-fill fs-1 mb-3"></i>
                                        <h4 class="fw-bold mb-2">2nd Place</h4>
                                        <div class="display-6 fw-bold mb-2">$7,000</div>
                                        <p class="mb-0">+ Certificate + Resources</p>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="pricing-card text-center p-4 rounded-3"
                                        style="background: linear-gradient(135deg, #CD7F32, #B8860B); color: white; position: relative; overflow: hidden;">
                                        <div
                                            style="position: absolute; bottom: -10px; right: -10px; width: 40px; height: 40px; background: rgba(255,255,255,0.2); border-radius: 50%; animation: float 3s ease-in-out infinite 2s;">
                                        </div>
                                        <i class="bi bi-medal-fill fs-1 mb-3"></i>
                                        <h4 class="fw-bold mb-2">3rd Place</h4>
                                        <div class="display-6 fw-bold mb-2">$3,000</div>
                                        <p class="mb-0">+ Certificate + Recognition</p>
                                    </div>
                                </div>
                            </div>
                            <div class="text-center mt-4">
                                <p class="text-muted mb-2"><i class="bi bi-gift me-2"></i>Additional prizes for top 10
                                    participants</p>
                                <div class="d-flex justify-content-center gap-4 flex-wrap">
                                    <span class="badge bg-primary px-3 py-2">Internship Opportunities</span>
                                    <span class="badge bg-success px-3 py-2">Industry Networking</span>
                                    <span class="badge bg-warning px-3 py-2">Portfolio Showcase</span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Competition Gallery -->
                    <div class="card border-0 shadow-sm mb-4">
                        <div class="card-body p-4">
                            <h3 class="fw-bold mb-4" style="color: #1F8FFF;">
                                <i class="bi bi-images me-2"></i>Competition Highlights
                            </h3>
                            <div class="row g-4">
                                <div class="col-md-6">
                                    <div class="image-card position-relative overflow-hidden rounded-3"
                                        style="height: 250px;">
                                        <img src="https://images.unsplash.com/photo-1485827404703-89b55fcc595e?w=600&h=400&fit=crop"
                                            class="img-fluid w-100 h-100 object-fit-cover" alt="AI Innovation"
                                            style="transition: transform 0.3s ease;">
                                        <div class="position-absolute bottom-0 start-0 end-0 p-3"
                                            style="background: linear-gradient(transparent, rgba(0,0,0,0.8));">
                                            <h5 class="text-white fw-bold mb-1">AI Innovation Lab</h5>
                                            <p class="text-white-50 mb-0 small">State-of-the-art facilities for
                                                participants</p>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="image-card position-relative overflow-hidden rounded-3"
                                        style="height: 250px;">
                                        <img src="https://images.unsplash.com/photo-1522202176988-66273c2fd55f?w=600&h=400&fit=crop"
                                            class="img-fluid w-100 h-100 object-fit-cover" alt="Team Collaboration"
                                            style="transition: transform 0.3s ease;">
                                        <div class="position-absolute bottom-0 start-0 end-0 p-3"
                                            style="background: linear-gradient(transparent, rgba(0,0,0,0.8));">
                                            <h5 class="text-white fw-bold mb-1">Global Collaboration</h5>
                                            <p class="text-white-50 mb-0 small">Connect with brilliant minds worldwide
                                            </p>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="image-card position-relative overflow-hidden rounded-3"
                                        style="height: 250px;">
                                        <img src="https://images.unsplash.com/photo-1551434678-e076c223a692?w=600&h=400&fit=crop"
                                            class="img-fluid w-100 h-100 object-fit-cover" alt="Expert Mentorship"
                                            style="transition: transform 0.3s ease;">
                                        <div class="position-absolute bottom-0 start-0 end-0 p-3"
                                            style="background: linear-gradient(transparent, rgba(0,0,0,0.8));">
                                            <h5 class="text-white fw-bold mb-1">Expert Mentorship</h5>
                                            <p class="text-white-50 mb-0 small">Learn from industry leaders and AI
                                                experts</p>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="image-card position-relative overflow-hidden rounded-3"
                                        style="height: 250px;">
                                        <img src="https://images.unsplash.com/photo-1559136555-9303baea8ebd?w=600&h=400&fit=crop"
                                            class="img-fluid w-100 h-100 object-fit-cover" alt="Award Ceremony"
                                            style="transition: transform 0.3s ease;">
                                        <div class="position-absolute bottom-0 start-0 end-0 p-3"
                                            style="background: linear-gradient(transparent, rgba(0,0,0,0.8));">
                                            <h5 class="text-white fw-bold mb-1">Grand Award Ceremony</h5>
                                            <p class="text-white-50 mb-0 small">Celebrate achievements with the
                                                community</p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Sidebar -->
                <div class="col-lg-4">
                    <!-- Registration Card -->
                    <div class="card border-0 shadow-sm mb-4">
                        <div class="card-body p-4 text-center">
                            <div class="mb-3">
                                <i class="bi bi-rocket-takeoff" style="font-size: 3rem; color: #1F8FFF;"></i>
                            </div>
                            <h4 class="fw-bold mb-3">Ready to Compete?</h4>
                            <p class="text-muted mb-4">Join 1,250+ participants in this exciting AI challenge</p>
                            <button class="btn btn-primary btn-lg w-100 mb-3"
                                style="background-color: #1F8FFF; border-color: #1F8FFF;">
                                <i class="bi bi-person-plus me-2"></i>Join Competition
                            </button>
                            <div class="d-flex justify-content-between text-center">
                                <div>
                                    <div class="h5 mb-0" style="color: #1F8FFF;">45</div>
                                    <small class="text-muted">Days Left</small>
                                </div>
                                <div>
                                    <div class="h5 mb-0" style="color: #1F8FFF;">$25K</div>
                                    <small class="text-muted">Prize Pool</small>
                                </div>
                                <div>
                                    <div class="h5 mb-0" style="color: #1F8FFF;">1,250+</div>
                                    <small class="text-muted">Participants</small>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Competition Benefits -->
                    <div class="card border-0 shadow-sm">
                        <div class="card-body p-4">
                            <h5 class="fw-bold mb-3">
                                <i class="bi bi-star-fill me-2" style="color: #1F8FFF;"></i>Why Join?
                            </h5>
                            <div class="d-flex align-items-center mb-3 p-2 rounded"
                                style="background-color: rgba(31, 143, 255, 0.1);">
                                <i class="bi bi-lightning-charge-fill text-primary me-3 fs-4"></i>
                                <div>
                                    <h6 class="mb-1 fw-bold">Skill Enhancement</h6>
                                    <small class="text-muted">Advance your AI expertise</small>
                                </div>
                            </div>
                            <div class="d-flex align-items-center mb-3 p-2 rounded"
                                style="background-color: rgba(31, 143, 255, 0.1);">
                                <i class="bi bi-people-fill me-3 fs-4" style="color: #1F8FFF;"></i>
                                <div>
                                    <h6 class="mb-1 fw-bold">Global Network</h6>
                                    <small class="text-muted">Connect with AI professionals</small>
                                </div>
                            </div>
                            <div class="d-flex align-items-center mb-3 p-2 rounded"
                                style="background-color: rgba(31, 143, 255, 0.1);">
                                <i class="bi bi-briefcase-fill me-3 fs-4" style="color: #1F8FFF;"></i>
                                <div>
                                    <h6 class="mb-1 fw-bold">Career Boost</h6>
                                    <small class="text-muted">Open doors to new opportunities</small>
                                </div>
                            </div>
                            <div class="d-flex align-items-center p-2 rounded"
                                style="background-color: rgba(40, 167, 69, 0.1);">
                                <i class="bi bi-trophy-fill text-success me-3 fs-4"></i>
                                <div>
                                    <h6 class="mb-1 fw-bold">Recognition</h6>
                                    <small class="text-muted">Gain industry recognition</small>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Footer -->
    

    <!-- Custom Styles -->
    <link rel="stylesheet" href="{{ asset('assets/css/pages/competitions-detail.css') }}">

    <!-- Bootstrap 5 JS -->
@endsection

@push('scripts')
<script src="{{ asset('assets/js/beta-notice.js') }}"></script>
@endpush
