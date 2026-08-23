@extends('layouts.app')

@section('title', 'Complete Your Profile - Edvora Tech')

@push('styles')
<link href="{{ asset('assets/css/teacher-onboarding.css') }}" rel="stylesheet" />
@endpush

@section('content')
<section class="onboarding-section">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-lg-8 col-md-10">

                {{-- ===== ONBOARDING FORM ===== --}}

                <div class="onboarding-header text-center mb-4">
                    <h1 class="onboarding-title">Complete Your Teacher Profile</h1>
                    <p class="onboarding-subtitle">Fill in your details so we can verify and activate your teacher account.</p>

                    <div class="onboarding-steps">
                        <div class="ob-step active" data-step="1">
                            <div class="ob-step-circle">1</div>
                            <span>Personal Info</span>
                        </div>
                        <div class="ob-step-line"></div>
                        <div class="ob-step" data-step="2">
                            <div class="ob-step-circle">2</div>
                            <span>Expertise</span>
                        </div>
                        <div class="ob-step-line"></div>
                        <div class="ob-step" data-step="3">
                            <div class="ob-step-circle">3</div>
                            <span>Documents</span>
                        </div>
                    </div>
                </div>

                @if ($errors->any())
                <div class="alert alert-danger py-2 mb-4">
                    <ul class="mb-0 ps-3">
                        @foreach ($errors->all() as $error)
                        <li class="small">{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
                @endif

                <form method="POST" action="{{ route('teacher.onboarding.submit') }}" enctype="multipart/form-data" id="onboardingForm" novalidate>
                    @csrf

                    {{-- ===== STEP 1: Personal Info ===== --}}
                    <div class="ob-card ob-step-content active" id="step1">
                        <div class="ob-card-header">
                            <i class="bi bi-person-circle"></i>
                            <h3>Personal Information</h3>
                        </div>
                        <div class="ob-card-body">
                            <div class="avatar-upload-wrap mb-4">
                                <div class="avatar-preview" id="avatarPreview">
                                    <i class="bi bi-person" id="avatarIcon"></i>
                                    <img src="" id="avatarImg" style="display:none;">
                                </div>
                                <div class="avatar-upload-info">
                                    <label for="avatar" class="btn btn-outline-primary btn-sm">
                                        <i class="bi bi-camera me-1"></i>Upload Photo
                                    </label>
                                    <input type="file" id="avatar" name="avatar" accept="image/*" class="d-none" onchange="previewAvatar(this)">
                                    <p class="small text-muted mt-1 mb-0">JPG, PNG — max 2MB</p>
                                </div>
                            </div>

                            <div class="row g-3">
                                <div class="col-md-6">
                                    <label class="form-label fw-semibold">Full Name <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control @error('name') is-invalid @enderror" name="name" value="{{ old('name', Auth::user()->name) }}" placeholder="Dr. John Smith" required>
                                    @error('name')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label fw-semibold">Phone Number</label>
                                    <input type="text" class="form-control @error('phone') is-invalid @enderror" name="phone" value="{{ old('phone', Auth::user()->phone) }}" placeholder="+1 234 567 8900">
                                    @error('phone')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                </div>
                                <div class="col-12">
                                    <label class="form-label fw-semibold">Bio / About You <span class="text-danger">*</span></label>
                                    <textarea class="form-control @error('bio') is-invalid laravel-invalid @enderror" name="bio" rows="4" placeholder="Tell students about yourself, your background, and what makes you a great teacher... (min 50 characters)" required>{{ old('bio', Auth::user()->bio) }}</textarea>
                                    @error('bio')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                    <div class="invalid-feedback" id="bioMinError" style="display:none;"></div>
                                    <div class="text-end small text-muted mt-1"><span id="bioCount">0</span>/1000</div>
                                </div>
                            </div>
                        </div>
                        <div class="ob-card-footer">
                            <div></div>
                            <button type="button" class="btn btn-primary ob-next-btn" onclick="goToStep(2)">
                                Next <i class="bi bi-arrow-right ms-1"></i>
                            </button>
                        </div>
                    </div>

                    {{-- ===== STEP 2: Expertise ===== --}}
                    <div class="ob-card ob-step-content" id="step2">
                        <div class="ob-card-header">
                            <i class="bi bi-mortarboard"></i>
                            <h3>Teaching Expertise</h3>
                        </div>
                        <div class="ob-card-body">
                            <div class="row g-3">
                                <div class="col-md-6">
                                    <label class="form-label fw-semibold">Teaching Department <span class="text-danger">*</span></label>
                                    <select class="form-select expertise-select @error('department') is-invalid @enderror" name="department" required>
                                        <option value="">Select department...</option>
                                        @foreach(['Programming','Web Development','Cyber Security','Data Science','Mobile Development','Design','DevOps','Database','AI & Machine Learning','Other'] as $dept)
                                        <option value="{{ $dept }}" {{ old('department', Auth::user()->department) === $dept ? 'selected' : '' }}>{{ $dept }}</option>
                                        @endforeach
                                    </select>
                                    @error('department')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label fw-semibold">Years of Experience <span class="text-danger">*</span></label>
                                    <select class="form-select expertise-select @error('experience_years') is-invalid @enderror" name="experience_years" required>
                                        <option value="">Select...</option>
                                        @foreach(['1-2','3-5','6-10','11-15','15+'] as $exp)
                                        <option value="{{ $exp }}" {{ old('experience_years', Auth::user()->experience_years) === $exp ? 'selected' : '' }}>{{ $exp }} years</option>
                                        @endforeach
                                    </select>
                                    @error('experience_years')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                </div>
                                <div class="col-12">
                                    <label class="form-label fw-semibold">Specialization <span class="text-danger">*</span></label>
                                    <select class="form-select expertise-select @error('specialization') is-invalid @enderror" name="specialization" required>
                                        <option value="" disabled {{ old('specialization') ? '' : 'selected' }}>Choose your specialization...</option>
                                        <optgroup label="Web Development">
                                            <option value="Full Stack Web Development" {{ old('specialization') === 'Full Stack Web Development' ? 'selected' : '' }}>Full Stack Web Development</option>
                                            <option value="Frontend Development" {{ old('specialization') === 'Frontend Development' ? 'selected' : '' }}>Frontend Development</option>
                                            <option value="Backend Development" {{ old('specialization') === 'Backend Development' ? 'selected' : '' }}>Backend Development</option>
                                        </optgroup>
                                        <optgroup label="Mobile & Software">
                                            <option value="Mobile App Development" {{ old('specialization') === 'Mobile App Development' ? 'selected' : '' }}>Mobile App Development</option>
                                            <option value="Game Development" {{ old('specialization') === 'Game Development' ? 'selected' : '' }}>Game Development</option>
                                            <option value="Embedded Systems" {{ old('specialization') === 'Embedded Systems' ? 'selected' : '' }}>Embedded Systems</option>
                                        </optgroup>
                                        <optgroup label="Data & AI">
                                            <option value="Machine Learning" {{ old('specialization') === 'Machine Learning' ? 'selected' : '' }}>Machine Learning</option>
                                            <option value="Data Science" {{ old('specialization') === 'Data Science' ? 'selected' : '' }}>Data Science</option>
                                        </optgroup>
                                        <optgroup label="Infrastructure & Security">
                                            <option value="Cyber Security" {{ old('specialization') === 'Cyber Security' ? 'selected' : '' }}>Cyber Security</option>
                                            <option value="Cloud Computing" {{ old('specialization') === 'Cloud Computing' ? 'selected' : '' }}>Cloud Computing</option>
                                            <option value="DevOps & SRE" {{ old('specialization') === 'DevOps & SRE' ? 'selected' : '' }}>DevOps & SRE</option>
                                            <option value="Database Administration" {{ old('specialization') === 'Database Administration' ? 'selected' : '' }}>Database Administration</option>
                                        </optgroup>
                                        <optgroup label="Design & Architecture">
                                            <option value="UI/UX Design" {{ old('specialization') === 'UI/UX Design' ? 'selected' : '' }}>UI/UX Design</option>
                                            <option value="Software Architecture" {{ old('specialization') === 'Software Architecture' ? 'selected' : '' }}>Software Architecture</option>
                                        </optgroup>
                                        <optgroup label="Emerging Tech">
                                            <option value="Blockchain Development" {{ old('specialization') === 'Blockchain Development' ? 'selected' : '' }}>Blockchain Development</option>
                                            <option value="Other" {{ old('specialization') === 'Other' ? 'selected' : '' }}>Other</option>
                                        </optgroup>
                                    </select>
                                    @error('specialization')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                </div>
                                <div class="col-12">
                                    <label class="form-label fw-semibold">Skills & Expertise <span class="text-danger">*</span> <small class="text-muted">(Select up to 10)</small></label>
                                    <div class="multi-select-wrapper @error('expertise') is-invalid-wrapper @enderror" id="expertiseWrapper">
                                        <div class="multi-select-input-area">
                                            <div class="multi-select-tags" id="expertiseTags"></div>
                                            <input type="text" class="multi-select-search" id="expertiseSearch" placeholder="Search and add skills..." autocomplete="off">
                                            <input type="hidden" name="expertise" id="expertiseInput" value="{{ old('expertise') }}">
                                        </div>
                                        <i class="bi bi-chevron-down multi-select-icon"></i>
                                        <div class="multi-select-dropdown" id="expertiseDropdown">
                                            @foreach(['Python','JavaScript','Java','PHP','C/C++','C#','Go','Ruby','Swift','Kotlin','React','Vue.js','Angular','Node.js','Django','Laravel','Spring Boot','TensorFlow','PyTorch','AWS','Docker','Kubernetes','PostgreSQL','MongoDB','Redis','Linux','Ethical Hacking','Network Security','UI/UX Design','Figma','Adobe XD','Other'] as $skill)
                                            <div class="multi-select-option" data-value="{{ $skill }}">{{ $skill }}</div>
                                            @endforeach
                                        </div>
                                    </div>
                                    <div class="form-text text-muted"><span id="expertiseCount">0</span>/10 skills selected</div>
                                    @error('expertise')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                </div>

                                <div class="col-md-6">
                                    <label class="form-label fw-semibold">LinkedIn Profile</label>
                                    <div class="input-group">
                                        <span class="input-group-text"><i class="bi bi-linkedin"></i></span>
                                        <input type="url" class="form-control @error('linkedin') is-invalid @enderror" name="linkedin" value="{{ old('linkedin') }}" placeholder="https://linkedin.com/in/yourprofile">
                                    </div>
                                    @error('linkedin')<div class="text-danger small mt-1">{{ $message }}</div>@enderror
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label fw-semibold">GitHub Profile</label>
                                    <div class="input-group">
                                        <span class="input-group-text"><i class="bi bi-github"></i></span>
                                        <input type="url" class="form-control @error('github') is-invalid @enderror" name="github" value="{{ old('github') }}" placeholder="https://github.com/yourprofile">
                                    </div>
                                    @error('github')<div class="text-danger small mt-1">{{ $message }}</div>@enderror
                                </div>
                                <div class="col-12">
                                    <label class="form-label fw-semibold">Personal Website / Portfolio</label>
                                    <div class="input-group">
                                        <span class="input-group-text"><i class="bi bi-globe2"></i></span>
                                        <input
                                            type="url"
                                            class="form-control @error('website') is-invalid @enderror"
                                            name="website"
                                            value="{{ old('website', Auth::user()->teacher->website ?? '') }}"
                                            placeholder="https://yourportfolio.com"
                                        >
                                    </div>
                                    <div class="form-text text-muted">Add a public portfolio, website, or profile link to make your teacher page richer.</div>
                                    @error('website')<div class="text-danger small mt-1">{{ $message }}</div>@enderror
                                </div>
                            </div>
                        </div>
                        <div class="ob-card-footer">
                            <button type="button" class="btn btn-outline-secondary" onclick="goToStep(1)">
                                <i class="bi bi-arrow-left me-1"></i> Back
                            </button>
                            <button type="button" class="btn btn-primary ob-next-btn" onclick="goToStep(3)">
                                Next <i class="bi bi-arrow-right ms-1"></i>
                            </button>
                        </div>
                    </div>

                    {{-- ===== STEP 3: Documents ===== --}}
                    <div class="ob-card ob-step-content" id="step3">
                        <div class="ob-card-header">
                            <i class="bi bi-file-earmark-text"></i>
                            <h3>Documents & Verification</h3>
                        </div>
                        <div class="ob-card-body">
                            <div class="cv-upload-area" id="cvDropArea">
                                <i class="bi bi-file-earmark-pdf"></i>
                                <h5>Upload Your CV / Resume</h5>
                                <p class="text-muted small mb-3">PDF, DOC or DOCX — max 5MB</p>
                                <label for="cv" class="btn btn-outline-primary btn-sm">
                                    <i class="bi bi-upload me-1"></i>Choose File
                                </label>
                                <input type="file" id="cv" name="cv" accept=".pdf,.doc,.docx" class="d-none" onchange="showCvName(this)">
                                <p class="small text-muted mt-2 mb-0" id="cvFileName">No file selected</p>
                            </div>

                            <div class="review-box mt-4">
                                <h6 class="fw-semibold mb-3"><i class="bi bi-list-check me-2 text-primary"></i>Review Checklist</h6>
                                <ul class="review-list">
                                    <li><i class="bi bi-check2-circle text-success me-2"></i>Your profile will be reviewed by our admin team</li>
                                    <li><i class="bi bi-check2-circle text-success me-2"></i>You'll receive an email notification within 1–2 business days</li>
                                    <li><i class="bi bi-check2-circle text-success me-2"></i>Once approved, you can create and publish courses</li>
                                    <li><i class="bi bi-info-circle text-warning me-2"></i>Make sure all information is accurate and complete</li>
                                </ul>
                            </div>
                        </div>
                        <div class="ob-card-footer">
                            <button type="button" class="btn btn-outline-secondary" onclick="goToStep(2)">
                                <i class="bi bi-arrow-left me-1"></i> Back
                            </button>
                            <button type="submit" class="btn btn-primary ob-submit-btn" id="submitBtn">
                                <i class="bi bi-send me-2"></i>Submit Application
                            </button>
                        </div>
                    </div>

                </form>

                <div class="onboarding-loading-overlay" id="onboardingLoadingOverlay" aria-live="polite" aria-busy="true" hidden>
                    <div class="onboarding-loading-card" role="status">
                        <span class="onboarding-loading-spinner" aria-hidden="true"></span>
                        <h4>Submitting your application</h4>
                        <p>Please wait while we securely submit your documents and details.</p>
                    </div>
                </div>

            </div>
        </div>
    </div>
</section>
@endsection

@push('scripts')
<script src="{{ asset('assets/js/teacher-onboarding.js') }}"></script>
@endpush
