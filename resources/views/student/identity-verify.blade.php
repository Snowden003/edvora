@extends('layouts.auth')

@section('title', 'Identity Verification - Edvora Tech')

@push('styles')
<link href="{{ asset('assets/css/teacher-onboarding.css') }}" rel="stylesheet" />
<link href="{{ asset('assets/css/student-identity.css') }}" rel="stylesheet" />
@endpush

@section('content')
<section class="onboarding-section d-flex align-items-center justify-content-center">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-lg-6 col-md-8 col-11">

                <div class="logo-wrap">
                    <a href="{{ route('home') }}">
                        <img src="{{ asset('assets/images/logo1.jpg') }}" alt="Edvora Tech">
                    </a>
                </div>

                <div class="identity-card text-center">

                    <div class="id-icon-wrap">
                        <i class="bi bi-person-vcard-fill"></i>
                    </div>

                    <h2 class="mb-2">Identity Verification</h2>
                    <p class="text-muted mb-4">
                        To access your student dashboard, please upload a clear photo of your
                        <strong class="text-primary">National ID (Tazkira)</strong>.<br>
                        An admin will review and approve your account shortly.
                    </p>

                    @if(session('error'))
                        <div class="alert alert-danger text-start py-2 mb-3">
                            <i class="bi bi-exclamation-triangle me-1"></i>{{ session('error') }}
                        </div>
                    @endif
                    @if(session('info'))
                        <div class="alert alert-info text-start py-2 mb-3">
                            <i class="bi bi-info-circle me-1"></i>{{ session('info') }}
                        </div>
                    @endif
                    @if($errors->any())
                        <div class="alert alert-danger text-start py-2 mb-3">
                            <ul class="mb-0 ps-3">
                                @foreach($errors->all() as $error)
                                    <li class="small">{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    @if($user->identity_status === 'rejected' && $user->identity_rejection_reason)
                        <div class="alert alert-warning text-start mb-3">
                            <strong><i class="bi bi-exclamation-circle me-1"></i>Previous submission was rejected:</strong><br>
                            <span class="small">{{ $user->identity_rejection_reason }}</span>
                        </div>
                    @endif

                    <form action="{{ route('student.identity.upload') }}" method="POST" enctype="multipart/form-data" class="text-start">
                        @csrf

                        <div class="drop-zone mb-3" id="dropZone"
                             onclick="document.getElementById('tazkiraInput').click()"
                             ondragover="this.classList.add('dragover');event.preventDefault();"
                             ondragleave="this.classList.remove('dragover');"
                             ondrop="handleDrop(event)">
                            <div id="dropContent">
                                <div class="drop-icon">📷</div>
                                <p class="drop-label">Drag & drop your Tazkira image here, or click to browse</p>
                                <p class="drop-hint">JPG, JPEG, PNG or WebP &nbsp;·&nbsp; Max 5 MB</p>
                            </div>
                            <div id="previewContainer">
                                <img id="previewImg" src="" alt="preview" />
                                <p id="previewName" class="small text-muted mt-2 mb-0"></p>
                            </div>
                        </div>
                        <input type="file" id="tazkiraInput" name="tazkira_image" accept="image/*" class="d-none" onchange="previewFile(this)">

                        <div class="tips-box mb-4">
                            <span class="tips-label">
                                <i class="bi bi-lightbulb me-1"></i>Tips for a good photo
                            </span>
                            <ul>
                                <li>The image must be clear and fully readable</li>
                                <li>All pages of the Tazkira must be visible</li>
                                <li>Avoid glare or direct flash on the document</li>
                                <li>All personal information must be clearly visible</li>
                            </ul>
                        </div>

                        <button type="submit" class="btn btn-primary w-100 btn-identity-submit">
                            <i class="bi bi-upload me-2"></i>Submit Tazkira Image
                        </button>
                    </form>

                    <div class="mt-3">
                        <form method="POST" action="{{ route('logout') }}" class="d-inline">
                            @csrf
                            <button type="submit" class="btn btn-link text-muted small p-0">
                                <i class="bi bi-box-arrow-left me-1"></i>Sign out
                            </button>
                        </form>
                    </div>

                </div>
            </div>
        </div>
    </div>
</section>
@endsection

@push('scripts')
<script src="{{ asset('assets/js/student-identity.js') }}" defer></script>
@endpush
