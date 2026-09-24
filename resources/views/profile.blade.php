@extends('layouts.app')

@section('title', ($user->name ?? 'User') . ' Profile - Edvora Tech')

@push('styles')
<link href="{{ asset('assets/css/student-dashboard.css') }}" rel="stylesheet" />
<link href="{{ asset('assets/css/profile.css') }}?v={{ file_exists(public_path('assets/css/profile.css')) ? filemtime(public_path('assets/css/profile.css')) : time() }}" rel="stylesheet" />
@endpush

@section('hide_header', true)
@section('hide_footer', true)

@section('content')
<div class="dashboard-wrapper">
    <!-- Sidebar Navigation -->
    @if(Auth::user()->role === 'teacher')
        <x-teacher-sidebar />
    @else
        <x-student-sidebar />
    @endif

    <!-- Main Dashboard Content -->
    <main class="main-content" id="mainContent">
        <div class="profile-page-container">

            {{-- Flash Messages --}}
            @if(session('success'))
            <div class="alert alert-success alert-dismissible fade show mb-4 border-0 rounded-4 shadow-sm d-flex align-items-center" role="alert" style="background: rgba(16, 185, 129, 0.12); border: 1px solid rgba(16, 185, 129, 0.3) !important;">
                <i class="bi bi-check-circle-fill text-success fs-5 me-3"></i>
                <div class="text-success-emphasis fw-medium">{{ session('success') }}</div>
                <button type="button" class="btn-close ms-auto" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
            @endif

            @if($errors->any())
            <div class="alert alert-danger alert-dismissible fade show mb-4 border-0 rounded-4 shadow-sm" role="alert" style="background: rgba(239, 68, 68, 0.1); border: 1px solid rgba(239, 68, 68, 0.3) !important;">
                <div class="d-flex align-items-center mb-2">
                    <i class="bi bi-exclamation-triangle-fill text-danger fs-5 me-2"></i>
                    <strong class="text-danger">Please resolve the following issues:</strong>
                </div>
                <ul class="mb-0 ps-3 text-danger-emphasis small">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
            @endif

            @php
                // Resolve cover image url
                $coverUrl = null;
                if ($user->cover_image) {
                    if (str_starts_with($user->cover_image, 'http')) {
                        $coverUrl = $user->cover_image;
                    } elseif (str_starts_with($user->cover_image, 'storage/') || str_starts_with($user->cover_image, '/storage/')) {
                        $coverUrl = asset(ltrim($user->cover_image, '/'));
                    } else {
                        $coverUrl = asset('storage/' . $user->cover_image);
                    }
                }

                // Resolve avatar url
                $avatarUrl = $user->publicAvatarUrl();

                // Level & Stats
                $level = method_exists($user, 'level') ? $user->level() : ['level' => 1, 'title' => 'Student'];
                $enrolledCount = method_exists($user, 'enrollments') ? $user->enrollments()->where('status', '!=', 'banned')->count() : 0;
            @endphp

            <!-- 1. Top Hero Profile Banner -->
            <div class="profile-hero-card">
                <!-- Cover Image Area -->
                <div class="profile-cover-area" id="profileCoverArea" style="{{ $coverUrl ? 'background-image: url(\'' . $coverUrl . '\');' : 'background: linear-gradient(135deg, #090e1a 0%, #0f1c3f 50%, #1a3578 100%);' }}">
                    <label for="cover_image_input" class="profile-cover-overlay-btn" title="Upload cover photo (max 5MB)">
                        <i class="bi bi-camera-fill"></i>
                        <span>Change Cover</span>
                    </label>
                </div>

                <!-- Profile Identity Bar -->
                <div class="profile-hero-content">
                    <div class="profile-identity-group">
                        <!-- Avatar Container -->
                        <div class="profile-avatar-box">
                            <img src="{{ $avatarUrl }}" 
                                 id="avatarPreview" 
                                 alt="{{ $user->name }}" 
                                 class="profile-avatar-img"
                                 onerror="this.src='https://ui-avatars.com/api/?name={{ urlencode($user->name) }}&size=160&background=1f8fff&color=fff'">
                            <label for="avatar_input" class="profile-avatar-badge-btn" title="Change profile photo (max 2MB)">
                                <i class="bi bi-pencil-fill"></i>
                            </label>
                        </div>

                        <!-- User Meta Details -->
                        <div class="profile-user-meta">
                            <h1 class="profile-name-title">
                                <span>{{ $user->name }}</span>
                                <i class="bi bi-patch-check-fill text-primary" style="font-size: 1.25rem;" title="Verified Account"></i>
                            </h1>
                            <div class="profile-chips-row">
                                <span class="profile-pill-chip role-badge">
                                    <i class="bi bi-mortarboard-fill"></i>
                                    {{ $user->role === 'teacher' ? 'Instructor' : 'Student Learner' }}
                                </span>
                                @if($user->role === 'student' && isset($level['level']))
                                <span class="profile-pill-chip level-badge">
                                    <i class="bi bi-award-fill"></i>
                                    Level {{ $level['level'] }} · {{ $level['title'] }}
                                </span>
                                @endif
                                @if($user->department)
                                <span class="profile-pill-chip status-badge">
                                    <i class="bi bi-book"></i>
                                    {{ $user->department }}
                                </span>
                                @endif
                            </div>
                        </div>
                    </div>

                    <!-- Action Buttons -->
                    <div class="profile-hero-actions">
                        <button type="submit" form="profile-form" class="btn-save-glow" id="headerSaveBtn">
                            <i class="bi bi-check2-circle fs-5"></i>
                            <span>Save Changes</span>
                        </button>
                    </div>
                </div>
            </div>

            <!-- 2. Two-Column Dashboard Layout -->
            <div class="profile-grid-wrapper">

                <!-- Left Sidebar Column: Identity, Status & Quick Overview -->
                <div class="profile-sidebar-column">

                    <!-- Student Verification Status Card -->
                    @if($user->role === 'student')
                    <div class="profile-panel-card">
                        <div class="profile-card-header">
                            <h3 class="profile-card-title">
                                <i class="bi bi-shield-check"></i>
                                <span>Official Student Verification</span>
                            </h3>
                        </div>
                        <div class="profile-card-body">
                            @if($user->studentProfile && $user->studentProfile->is_complete)
                                <div class="verification-badge-card status-verified">
                                    <div class="verif-header">
                                        <div class="verif-icon-circle">
                                            <i class="bi bi-check-circle-fill"></i>
                                        </div>
                                        <div>
                                            <h4 class="verif-title">Identity Verified</h4>
                                            <p class="verif-subtitle">Official details & Tazkira confirmed</p>
                                        </div>
                                    </div>
                                    <a href="{{ route('student.profile-details') }}" class="btn-complete-details">
                                        <i class="bi bi-person-vcard me-1"></i> View / Update Details
                                    </a>
                                </div>
                            @else
                                <div class="verification-badge-card status-incomplete">
                                    <div class="verif-header">
                                        <div class="verif-icon-circle">
                                            <i class="bi bi-exclamation-circle-fill"></i>
                                        </div>
                                        <div>
                                            <h4 class="verif-title">Incomplete Details</h4>
                                            <p class="verif-subtitle">Tazkira & personal info required</p>
                                        </div>
                                    </div>
                                    <p class="small text-muted mb-3" style="line-height: 1.4;">
                                        Complete your official student registration to qualify for course certificates and exams.
                                    </p>
                                    <a href="{{ route('student.profile-details') }}" class="btn-complete-details">
                                        <i class="bi bi-pencil-square me-1"></i> Complete My Details
                                    </a>
                                </div>
                            @endif
                        </div>
                    </div>
                    @endif

                    <!-- Learning Journey Stats -->
                    <div class="profile-panel-card">
                        <div class="profile-card-header">
                            <h3 class="profile-card-title">
                                <i class="bi bi-graph-up-arrow"></i>
                                <span>Learning Overview</span>
                            </h3>
                        </div>
                        <div class="profile-card-body">
                            <div class="student-quick-stat-item">
                                <span class="quick-stat-label">
                                    <i class="bi bi-journal-bookmark-fill"></i> Enrolled Courses
                                </span>
                                <span class="quick-stat-value">{{ $enrolledCount }}</span>
                            </div>
                            @if($user->role === 'student' && isset($level['level']))
                            <div class="student-quick-stat-item">
                                <span class="quick-stat-label">
                                    <i class="bi bi-stars"></i> Student Level
                                </span>
                                <span class="quick-stat-value text-primary fw-bold">Lvl {{ $level['level'] }}</span>
                            </div>
                            @endif
                            <div class="student-quick-stat-item">
                                <span class="quick-stat-label">
                                    <i class="bi bi-calendar-check"></i> Member Since
                                </span>
                                <span class="quick-stat-value">{{ $user->created_at->format('M Y') }}</span>
                            </div>
                            <div class="student-quick-stat-item">
                                <span class="quick-stat-label">
                                    <i class="bi bi-shield-lock"></i> Account Status
                                </span>
                                <span class="quick-stat-value text-success">Active</span>
                            </div>
                        </div>
                    </div>

                </div>

                <!-- Right Main Column: Profile Edit Form -->
                <div class="profile-content-column">
                    <form id="profile-form" method="POST" action="{{ route('profile.save') }}" enctype="multipart/form-data">
                        @csrf

                        <!-- Hidden File Inputs -->
                        <input type="file" name="avatar" id="avatar_input" class="hidden-file-input" accept="image/jpeg,image/png,image/webp" onchange="handleAvatarSelect(this)">
                        <input type="file" name="cover_image" id="cover_image_input" class="hidden-file-input" accept="image/jpeg,image/png,image/webp" onchange="handleCoverSelect(this)">

                        <!-- 1. Personal Information Panel -->
                        <div class="profile-panel-card">
                            <div class="profile-card-header">
                                <h3 class="profile-card-title">
                                    <i class="bi bi-person-lines-fill"></i>
                                    <span>Personal Information</span>
                                </h3>
                            </div>
                            <div class="profile-card-body">
                                <div class="row g-3">
                                    <div class="col-md-6">
                                        <div class="form-group-modern">
                                            <label class="form-label-modern">Full Name <span class="req-star">*</span></label>
                                            <div class="input-icon-wrapper">
                                                <i class="bi bi-person input-icon"></i>
                                                <input type="text" name="name" class="form-control-modern" value="{{ old('name', $user->name) }}" required placeholder="e.g. Zahra Ahmadi">
                                            </div>
                                        </div>
                                    </div>

                                    <div class="col-md-6">
                                        <div class="form-group-modern">
                                            <label class="form-label-modern">Email Address <span class="req-star">*</span></label>
                                            <div class="input-icon-wrapper">
                                                <i class="bi bi-envelope input-icon"></i>
                                                <input type="email" name="email" class="form-control-modern" value="{{ old('email', $user->email) }}" required placeholder="student@example.com">
                                            </div>
                                        </div>
                                    </div>

                                    <div class="col-md-6">
                                        <div class="form-group-modern">
                                            <label class="form-label-modern">Phone Number</label>
                                            <div class="input-icon-wrapper">
                                                <i class="bi bi-telephone input-icon"></i>
                                                <input type="tel" name="phone" class="form-control-modern" value="{{ old('phone', $user->phone) }}" placeholder="+93 700 000 000">
                                            </div>
                                        </div>
                                    </div>

                                    <div class="col-md-6">
                                        <div class="form-group-modern">
                                            <label class="form-label-modern">Field of Study / Discipline</label>
                                            <div class="input-icon-wrapper">
                                                <i class="bi bi-journal-code input-icon"></i>
                                                <select name="department" class="form-control-modern form-select">
                                                    <option value="">Select your study field...</option>
                                                    <optgroup label="Technology & Programming">
                                                        <option value="Computer Science" {{ old('department', $user->department) == 'Computer Science' ? 'selected' : '' }}>Computer Science</option>
                                                        <option value="Software Engineering" {{ old('department', $user->department) == 'Software Engineering' ? 'selected' : '' }}>Software Engineering</option>
                                                        <option value="Web Development" {{ old('department', $user->department) == 'Web Development' ? 'selected' : '' }}>Web Development</option>
                                                        <option value="Mobile Development" {{ old('department', $user->department) == 'Mobile Development' ? 'selected' : '' }}>Mobile Development</option>
                                                        <option value="Artificial Intelligence" {{ old('department', $user->department) == 'Artificial Intelligence' ? 'selected' : '' }}>Artificial Intelligence & Data</option>
                                                        <option value="Cybersecurity" {{ old('department', $user->department) == 'Cybersecurity' ? 'selected' : '' }}>Cybersecurity</option>
                                                    </optgroup>
                                                    <optgroup label="Design & Creative">
                                                        <option value="UI/UX Design" {{ old('department', $user->department) == 'UI/UX Design' ? 'selected' : '' }}>UI/UX Design</option>
                                                        <option value="Graphic Design" {{ old('department', $user->department) == 'Graphic Design' ? 'selected' : '' }}>Graphic Design</option>
                                                        <option value="Digital Media" {{ old('department', $user->department) == 'Digital Media' ? 'selected' : '' }}>Digital Media</option>
                                                    </optgroup>
                                                    <optgroup label="Business & Career">
                                                        <option value="Business Administration" {{ old('department', $user->department) == 'Business Administration' ? 'selected' : '' }}>Business Administration</option>
                                                        <option value="Marketing" {{ old('department', $user->department) == 'Marketing' ? 'selected' : '' }}>Digital Marketing</option>
                                                        <option value="Project Management" {{ old('department', $user->department) == 'Project Management' ? 'selected' : '' }}>Project Management</option>
                                                    </optgroup>
                                                    <optgroup label="Languages & Sciences">
                                                        <option value="English Language" {{ old('department', $user->department) == 'English Language' ? 'selected' : '' }}>English Language</option>
                                                        <option value="Medicine" {{ old('department', $user->department) == 'Medicine' ? 'selected' : '' }}>Medicine & Health</option>
                                                        <option value="Other" {{ old('department', $user->department) == 'Other' ? 'selected' : '' }}>Other Fields</option>
                                                    </optgroup>
                                                </select>
                                            </div>
                                        </div>
                                    </div>

                                    @if($user->role === 'teacher')
                                    <div class="col-md-6">
                                        <div class="form-group-modern">
                                            <label class="form-label-modern">Years of Experience</label>
                                            <div class="input-icon-wrapper">
                                                <i class="bi bi-briefcase input-icon"></i>
                                                <input type="text" name="experience_years" class="form-control-modern" value="{{ old('experience_years', $user->experience_years) }}" placeholder="e.g. 4 Years">
                                            </div>
                                        </div>
                                    </div>
                                    @endif
                                </div>
                            </div>
                        </div>

                        <!-- 2. About Me (Bio) Panel -->
                        <div class="profile-panel-card">
                            <div class="profile-card-header">
                                <h3 class="profile-card-title">
                                    <i class="bi bi-card-text"></i>
                                    <span>About Me (Bio)</span>
                                </h3>
                            </div>
                            <div class="profile-card-body">
                                <div class="form-group-modern mb-0">
                                    <label class="form-label-modern">Short Introduction & Goals</label>
                                    <div class="input-icon-wrapper is-textarea">
                                        <i class="bi bi-chat-quote input-icon"></i>
                                        <textarea name="bio" 
                                                  id="bio-textarea" 
                                                  class="form-control-modern" 
                                                  rows="4" 
                                                  maxlength="1000" 
                                                  placeholder="Tell the community about your interests, aspirations, and what you are learning...">{{ old('bio', $user->bio) }}</textarea>
                                    </div>
                                    <div class="bio-progress-wrapper">
                                        <span>Share a few lines about yourself</span>
                                        <div class="d-flex align-items-center gap-2">
                                            <div class="bio-progress-bar">
                                                <div class="bio-progress-fill" id="bioProgressBar"></div>
                                            </div>
                                            <span id="bioCharCount">0 / 1000</span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- 3. Social & Portfolio Links Panel -->
                        <div class="profile-panel-card">
                            <div class="profile-card-header">
                                <h3 class="profile-card-title">
                                    <i class="bi bi-globe2"></i>
                                    <span>Social Profiles & Portfolio</span>
                                </h3>
                            </div>
                            <div class="profile-card-body">
                                <p class="text-muted small mb-3">
                                    Connect your digital profiles to showcase your work and network with teachers and fellow students.
                                </p>
                                <div class="row g-3">
                                    <div class="col-md-6">
                                        <div class="social-channel-item">
                                            <div class="social-brand-icon icon-linkedin">
                                                <i class="bi bi-linkedin"></i>
                                            </div>
                                            <input type="url" name="linkedin" class="social-channel-input" 
                                                   value="{{ old('linkedin', optional($user->teacher)->linkedin) }}" 
                                                   placeholder="https://linkedin.com/in/username">
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="social-channel-item">
                                            <div class="social-brand-icon icon-github">
                                                <i class="bi bi-github"></i>
                                            </div>
                                            <input type="url" name="github" class="social-channel-input" 
                                                   value="{{ old('github', optional($user->teacher)->github) }}" 
                                                   placeholder="https://github.com/username">
                                        </div>
                                    </div>
                                    <div class="col-12">
                                        <div class="social-channel-item">
                                            <div class="social-brand-icon icon-web">
                                                <i class="bi bi-globe"></i>
                                            </div>
                                            <input type="url" name="website" class="social-channel-input" 
                                                   value="{{ old('website', optional($user->teacher)->website) }}" 
                                                   placeholder="https://yourportfolio.com">
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Submit Footer Bar -->
                        <div class="d-flex align-items-center justify-content-end gap-3 pt-2 pb-4">
                            <button type="button" class="btn btn-outline-secondary rounded-4 px-4 py-2" onclick="window.location.reload()">
                                Cancel
                            </button>
                            <button type="submit" class="btn-save-glow">
                                <i class="bi bi-check2-circle fs-5"></i>
                                <span>Save Profile Settings</span>
                            </button>
                        </div>

                    </form>
                </div>

            </div>

        </div>
    </main>
</div>
@endsection

@push('scripts')
<script>
    // Live avatar preview with validation
    function handleAvatarSelect(input) {
        if (!input.files || !input.files[0]) return;
        const file = input.files[0];

        // Max 2MB validation
        if (file.size > 2 * 1024 * 1024) {
            alert('Profile photo must be less than 2MB. Your file is ' + (file.size / 1024 / 1024).toFixed(1) + 'MB.');
            input.value = '';
            return;
        }

        const reader = new FileReader();
        reader.onload = function(e) {
            const avatarImg = document.getElementById('avatarPreview');
            if (avatarImg) {
                avatarImg.src = e.target.result;
                avatarImg.classList.add('avatar-preview-flash');
                setTimeout(() => avatarImg.classList.remove('avatar-preview-flash'), 500);
            }
        };
        reader.readAsDataURL(file);
    }

    // Live cover image preview with validation
    function handleCoverSelect(input) {
        if (!input.files || !input.files[0]) return;
        const file = input.files[0];

        // Max 5MB validation
        if (file.size > 5 * 1024 * 1024) {
            alert('Cover image must be less than 5MB. Your file is ' + (file.size / 1024 / 1024).toFixed(1) + 'MB.');
            input.value = '';
            return;
        }

        const reader = new FileReader();
        reader.onload = function(e) {
            const coverArea = document.getElementById('profileCoverArea');
            if (coverArea) {
                coverArea.style.backgroundImage = 'url(' + e.target.result + ')';
            }
        };
        reader.readAsDataURL(file);
    }

    // Dynamic bio character counter & progress bar
    (function() {
        const bioTextarea = document.getElementById('bio-textarea');
        const charCount = document.getElementById('bioCharCount');
        const progressBar = document.getElementById('bioProgressBar');
        const maxLength = 1000;

        function updateBioCount() {
            if (!bioTextarea || !charCount || !progressBar) return;
            const currentLength = bioTextarea.value.length;
            charCount.textContent = currentLength + ' / ' + maxLength;
            
            const pct = Math.min(100, Math.round((currentLength / maxLength) * 100));
            progressBar.style.width = pct + '%';

            if (currentLength >= 900) {
                progressBar.style.background = 'linear-gradient(90deg, #f59e0b, #ef4444)';
                charCount.style.color = '#ef4444';
            } else {
                progressBar.style.background = 'linear-gradient(90deg, #1f8fff, #00f0ff)';
                charCount.style.color = '#64748b';
            }
        }

        if (bioTextarea) {
            updateBioCount();
            bioTextarea.addEventListener('input', updateBioCount);
        }
    })();
</script>
@endpush
