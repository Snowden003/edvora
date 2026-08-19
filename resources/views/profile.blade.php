@extends('layouts.app')

@section('title', 'Profile - Edvora Tech')

@push('styles')
<link href="{{ asset('assets/css/teacher-dashboard.css') }}" rel="stylesheet" />
<link href="{{ asset('assets/css/profile.css') }}" rel="stylesheet" />
<link href="{{ asset('assets/css/dashboard.css') }}" rel="stylesheet" />
<link href="{{ asset('assets/css/events-pages.css') }}" rel="stylesheet" />
<link href="{{ asset('assets/css/courses-pages.css') }}" rel="stylesheet" />
<link href="{{ asset('assets/css/auth-pages.css') }}" rel="stylesheet" />
<link href="{{ asset('assets/css/beta-notice.css') }}" rel="stylesheet" />
@endpush

@section('content')
<!-- Header -->
    

    <!-- Dashboard Layout Wrapper -->
    <div class="dashboard-wrapper">
        <!-- Sidebar Navigation -->
        @if(Auth::user()->role === 'teacher')
            <x-teacher-sidebar />
        @else
            <x-student-sidebar />
        @endif

        <!-- Main Dashboard Content -->
        <main class="main-content">
            <div class="container-fluid py-5">

                <div class="row justify-content-center">
                    <div class="col-xl-10">

                        @if(session('success'))
                        <div class="alert alert-success alert-dismissible fade show mb-4" role="alert">
                            <i class="bi bi-check-circle me-2"></i>{{ session('success') }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                        @endif

                        @if($errors->any())
                        <div class="alert alert-danger alert-dismissible fade show mb-4" role="alert">
                            <i class="bi bi-exclamation-triangle me-2"></i><strong>Please fix the following errors:</strong>
                            <ul class="mb-0 mt-1">
                                @foreach($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                        @endif

                        <!-- Main Profile Card -->
                        <div class="glass-card-premium mb-5">
                            <!-- Cover Image -->
                            @php
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
                            @endphp
                            <div class="profile-cover" style="{{ $coverUrl ? 'background: linear-gradient(135deg, rgba(37, 99, 235, 0.3), rgba(147, 51, 234, 0.3)), url(\'' . $coverUrl . '\') center/cover;' : '' }}">
                                <label for="cover_image_input" class="profile-cover-edit" title="Update Cover Photo (1200×400px, max 5MB)">
                                    <i class="bi bi-camera me-1"></i> Change Cover
                                </label>
                            </div>

                            <!-- Avatar and Title -->
                            @php
                                $avatarUrl = null;
                                if ($user->avatar) {
                                    if (str_starts_with($user->avatar, 'http')) {
                                        $avatarUrl = $user->avatar;
                                    } elseif (str_starts_with($user->avatar, 'storage/') || str_starts_with($user->avatar, '/storage/')) {
                                        $avatarUrl = asset(ltrim($user->avatar, '/'));
                                    } else {
                                        $avatarUrl = asset('storage/' . $user->avatar);
                                    }
                                } else {
                                    $avatarUrl = 'https://ui-avatars.com/api/?name=' . urlencode($user->name) . '&size=150&background=1f8fff&color=fff';
                                }
                            @endphp
                            <div class="profile-avatar-container">
                                <div class="profile-avatar-wrapper">
                                    <img src="{{ $avatarUrl }}" id="avatarPreview"
                                        alt="{{ $user->name }}" class="profile-avatar"
                                        onerror="this.src='https://ui-avatars.com/api/?name={{ urlencode($user->name) }}&size=150&background=1f8fff&color=fff'">
                                    <label for="avatar_input" class="avatar-edit-btn" title="Upload Photo (300×300px, max 2MB)" style="cursor:pointer;">
                                        <i class="bi bi-pencil-fill"></i>
                                    </label>
                                </div>
                                <div class="profile-user-info">
                                    <h3 class="profile-user-name">{{ $user->name }}</h3>
                                    <div class="profile-user-role"><i class="bi bi-award me-1"></i> {{ $user->department ?? ($user->role === 'teacher' ? 'Instructor' : 'Student') }}</div>
                                </div>
                            </div>

                            <!-- Header for Profile -->
                            <div class="d-flex justify-content-between align-items-center mb-4 px-4">
                                <div>
                                    <h2 class="fw-bold mb-1 text-premium">{{ $user->role === 'teacher' ? 'Instructor Profile' : 'Student Profile' }}</h2>
                                    <p class="text-muted">Manage your personal information and professional details.</p>
                                </div>
                                <div>
                                    <button type="submit" form="profile-form" class="btn btn-premium-solid">
                                        <i class="bi bi-check2-circle me-2"></i>Save Changes
                                    </button>
                                </div>
                            </div>

                            <form id="profile-form" class="p-4 pt-1" method="POST" action="{{ route('profile.save') }}" enctype="multipart/form-data">
                                @csrf
                                <!-- Personal Information Section -->
                                <div class="mb-5">
                                    <h4 class="section-title">Personal Information</h4>
                                    <div class="row g-4">
                                        <div class="col-md-6">
                                            <label class="form-label-premium">Full Name <span class="required-asterisk" title="Required">*</span></label>
                                            <input type="text" name="name" class="form-control form-control-premium" value="{{ old('name', $user->name) }}" placeholder="Enter your full name">
                                        </div>
                                        <div class="col-md-6">
                                            <label class="form-label-premium">Department / Title</label>
                                            <select name="department" class="form-select form-control-premium">
                                                <option value="">Select your department/title</option>
                                                <optgroup label="Technology & IT">
                                                    <option value="Computer Science" {{ old('department', $user->department) == 'Computer Science' ? 'selected' : '' }}>Computer Science</option>
                                                    <option value="Software Engineering" {{ old('department', $user->department) == 'Software Engineering' ? 'selected' : '' }}>Software Engineering</option>
                                                    <option value="Information Technology" {{ old('department', $user->department) == 'Information Technology' ? 'selected' : '' }}>Information Technology</option>
                                                    <option value="Cybersecurity" {{ old('department', $user->department) == 'Cybersecurity' ? 'selected' : '' }}>Cybersecurity</option>
                                                    <option value="Data Science" {{ old('department', $user->department) == 'Data Science' ? 'selected' : '' }}>Data Science</option>
                                                    <option value="Artificial Intelligence" {{ old('department', $user->department) == 'Artificial Intelligence' ? 'selected' : '' }}>Artificial Intelligence</option>
                                                    <option value="Machine Learning" {{ old('department', $user->department) == 'Machine Learning' ? 'selected' : '' }}>Machine Learning</option>
                                                    <option value="Cloud Computing" {{ old('department', $user->department) == 'Cloud Computing' ? 'selected' : '' }}>Cloud Computing</option>
                                                    <option value="DevOps" {{ old('department', $user->department) == 'DevOps' ? 'selected' : '' }}>DevOps</option>
                                                    <option value="Network Engineering" {{ old('department', $user->department) == 'Network Engineering' ? 'selected' : '' }}>Network Engineering</option>
                                                    <option value="Database Administration" {{ old('department', $user->department) == 'Database Administration' ? 'selected' : '' }}>Database Administration</option>
                                                    <option value="Web Development" {{ old('department', $user->department) == 'Web Development' ? 'selected' : '' }}>Web Development</option>
                                                    <option value="Mobile Development" {{ old('department', $user->department) == 'Mobile Development' ? 'selected' : '' }}>Mobile Development</option>
                                                    <option value="Game Development" {{ old('department', $user->department) == 'Game Development' ? 'selected' : '' }}>Game Development</option>
                                                    <option value="Blockchain" {{ old('department', $user->department) == 'Blockchain' ? 'selected' : '' }}>Blockchain</option>
                                                </optgroup>
                                                <optgroup label="Engineering">
                                                    <option value="Electrical Engineering" {{ old('department', $user->department) == 'Electrical Engineering' ? 'selected' : '' }}>Electrical Engineering</option>
                                                    <option value="Mechanical Engineering" {{ old('department', $user->department) == 'Mechanical Engineering' ? 'selected' : '' }}>Mechanical Engineering</option>
                                                    <option value="Civil Engineering" {{ old('department', $user->department) == 'Civil Engineering' ? 'selected' : '' }}>Civil Engineering</option>
                                                    <option value="Chemical Engineering" {{ old('department', $user->department) == 'Chemical Engineering' ? 'selected' : '' }}>Chemical Engineering</option>
                                                    <option value="Industrial Engineering" {{ old('department', $user->department) == 'Industrial Engineering' ? 'selected' : '' }}>Industrial Engineering</option>
                                                    <option value="Aerospace Engineering" {{ old('department', $user->department) == 'Aerospace Engineering' ? 'selected' : '' }}>Aerospace Engineering</option>
                                                    <option value="Biomedical Engineering" {{ old('department', $user->department) == 'Biomedical Engineering' ? 'selected' : '' }}>Biomedical Engineering</option>
                                                    <option value="Environmental Engineering" {{ old('department', $user->department) == 'Environmental Engineering' ? 'selected' : '' }}>Environmental Engineering</option>
                                                    <option value="Robotics Engineering" {{ old('department', $user->department) == 'Robotics Engineering' ? 'selected' : '' }}>Robotics Engineering</option>
                                                </optgroup>
                                                <optgroup label="Business & Management">
                                                    <option value="Business Administration" {{ old('department', $user->department) == 'Business Administration' ? 'selected' : '' }}>Business Administration</option>
                                                    <option value="Marketing" {{ old('department', $user->department) == 'Marketing' ? 'selected' : '' }}>Marketing</option>
                                                    <option value="Finance" {{ old('department', $user->department) == 'Finance' ? 'selected' : '' }}>Finance</option>
                                                    <option value="Accounting" {{ old('department', $user->department) == 'Accounting' ? 'selected' : '' }}>Accounting</option>
                                                    <option value="Human Resources" {{ old('department', $user->department) == 'Human Resources' ? 'selected' : '' }}>Human Resources</option>
                                                    <option value="Project Management" {{ old('department', $user->department) == 'Project Management' ? 'selected' : '' }}>Project Management</option>
                                                    <option value="Entrepreneurship" {{ old('department', $user->department) == 'Entrepreneurship' ? 'selected' : '' }}>Entrepreneurship</option>
                                                    <option value="International Business" {{ old('department', $user->department) == 'International Business' ? 'selected' : '' }}>International Business</option>
                                                    <option value="Supply Chain" {{ old('department', $user->department) == 'Supply Chain' ? 'selected' : '' }}>Supply Chain</option>
                                                </optgroup>
                                                <optgroup label="Healthcare & Medicine">
                                                    <option value="Medicine" {{ old('department', $user->department) == 'Medicine' ? 'selected' : '' }}>Medicine</option>
                                                    <option value="Nursing" {{ old('department', $user->department) == 'Nursing' ? 'selected' : '' }}>Nursing</option>
                                                    <option value="Pharmacy" {{ old('department', $user->department) == 'Pharmacy' ? 'selected' : '' }}>Pharmacy</option>
                                                    <option value="Dentistry" {{ old('department', $user->department) == 'Dentistry' ? 'selected' : '' }}>Dentistry</option>
                                                    <option value="Public Health" {{ old('department', $user->department) == 'Public Health' ? 'selected' : '' }}>Public Health</option>
                                                    <option value="Physical Therapy" {{ old('department', $user->department) == 'Physical Therapy' ? 'selected' : '' }}>Physical Therapy</option>
                                                    <option value="Nutrition" {{ old('department', $user->department) == 'Nutrition' ? 'selected' : '' }}>Nutrition</option>
                                                </optgroup>
                                                <optgroup label="Sciences">
                                                    <option value="Physics" {{ old('department', $user->department) == 'Physics' ? 'selected' : '' }}>Physics</option>
                                                    <option value="Chemistry" {{ old('department', $user->department) == 'Chemistry' ? 'selected' : '' }}>Chemistry</option>
                                                    <option value="Biology" {{ old('department', $user->department) == 'Biology' ? 'selected' : '' }}>Biology</option>
                                                    <option value="Mathematics" {{ old('department', $user->department) == 'Mathematics' ? 'selected' : '' }}>Mathematics</option>
                                                    <option value="Statistics" {{ old('department', $user->department) == 'Statistics' ? 'selected' : '' }}>Statistics</option>
                                                    <option value="Environmental Science" {{ old('department', $user->department) == 'Environmental Science' ? 'selected' : '' }}>Environmental Science</option>
                                                </optgroup>
                                                <optgroup label="Arts & Humanities">
                                                    <option value="Graphic Design" {{ old('department', $user->department) == 'Graphic Design' ? 'selected' : '' }}>Graphic Design</option>
                                                    <option value="UI/UX Design" {{ old('department', $user->department) == 'UI/UX Design' ? 'selected' : '' }}>UI/UX Design</option>
                                                    <option value="Fine Arts" {{ old('department', $user->department) == 'Fine Arts' ? 'selected' : '' }}>Fine Arts</option>
                                                    <option value="Photography" {{ old('department', $user->department) == 'Photography' ? 'selected' : '' }}>Photography</option>
                                                    <option value="Film & Media" {{ old('department', $user->department) == 'Film & Media' ? 'selected' : '' }}>Film & Media</option>
                                                    <option value="Journalism" {{ old('department', $user->department) == 'Journalism' ? 'selected' : '' }}>Journalism</option>
                                                    <option value="Languages" {{ old('department', $user->department) == 'Languages' ? 'selected' : '' }}>Languages</option>
                                                    <option value="Literature" {{ old('department', $user->department) == 'Literature' ? 'selected' : '' }}>Literature</option>
                                                    <option value="History" {{ old('department', $user->department) == 'History' ? 'selected' : '' }}>History</option>
                                                </optgroup>
                                                <optgroup label="Education">
                                                    <option value="Education" {{ old('department', $user->department) == 'Education' ? 'selected' : '' }}>Education</option>
                                                    <option value="Early Childhood Education" {{ old('department', $user->department) == 'Early Childhood Education' ? 'selected' : '' }}>Early Childhood Education</option>
                                                    <option value="Special Education" {{ old('department', $user->department) == 'Special Education' ? 'selected' : '' }}>Special Education</option>
                                                    <option value="Educational Technology" {{ old('department', $user->department) == 'Educational Technology' ? 'selected' : '' }}>Educational Technology</option>
                                                </optgroup>
                                                <optgroup label="Other">
                                                    <option value="Law" {{ old('department', $user->department) == 'Law' ? 'selected' : '' }}>Law</option>
                                                    <option value="Psychology" {{ old('department', $user->department) == 'Psychology' ? 'selected' : '' }}>Psychology</option>
                                                    <option value="Sociology" {{ old('department', $user->department) == 'Sociology' ? 'selected' : '' }}>Sociology</option>
                                                    <option value="Political Science" {{ old('department', $user->department) == 'Political Science' ? 'selected' : '' }}>Political Science</option>
                                                    <option value="Economics" {{ old('department', $user->department) == 'Economics' ? 'selected' : '' }}>Economics</option>
                                                    <option value="Architecture" {{ old('department', $user->department) == 'Architecture' ? 'selected' : '' }}>Architecture</option>
                                                    <option value="Interior Design" {{ old('department', $user->department) == 'Interior Design' ? 'selected' : '' }}>Interior Design</option>
                                                    <option value="Hospitality" {{ old('department', $user->department) == 'Hospitality' ? 'selected' : '' }}>Hospitality</option>
                                                    <option value="Tourism" {{ old('department', $user->department) == 'Tourism' ? 'selected' : '' }}>Tourism</option>
                                                    <option value="Agriculture" {{ old('department', $user->department) == 'Agriculture' ? 'selected' : '' }}>Agriculture</option>
                                                </optgroup>
                                            </select>
                                        </div>
                                        <div class="col-md-6">
                                            <label class="form-label-premium">Email Address <span class="required-asterisk" title="Required">*</span></label>
                                            <input type="email" name="email" class="form-control form-control-premium" value="{{ old('email', $user->email) }}" placeholder="Email address">
                                        </div>
                                        <div class="col-md-6">
                                            <label class="form-label-premium">Phone Number</label>
                                            <input type="tel" name="phone" class="form-control form-control-premium" value="{{ old('phone', $user->phone) }}" placeholder="Optional phone number">
                                        </div>
                                        <div class="col-md-6">
                                            <label class="form-label-premium">Profile Photo</label>
                                            <input type="file" name="avatar" id="avatar_input" class="form-control form-control-premium" accept="image/jpeg,image/png,image/webp" onchange="previewAvatar(this)">
                                            <small class="text-muted">Max: 2MB | Recommended: 300×300px (JPG, PNG, WebP)</small>
                                        </div>
                                        <div class="col-md-6">
                                            <label class="form-label-premium">Cover Image</label>
                                            <input type="file" name="cover_image" id="cover_image_input" class="form-control form-control-premium" accept="image/jpeg,image/png,image/webp" onchange="previewCoverImage(this)">
                                            <small class="text-muted">Max: 5MB | Recommended: 1200×400px (JPG, PNG, WebP)</small>
                                        </div>
                                    </div>
                                </div>

                                <!-- Professional Details Section -->
                                <div class="mb-5">
                                    <h4 class="section-title">Professional Details</h4>
                                    <div class="row g-4">
                                        <div class="col-12">
                                            <label class="form-label-premium">About Me (Bio) <small class="text-muted">(<span id="bio-char-count">0</span> / 1000 characters)</small></label>
                                            <textarea name="bio" id="bio-textarea" class="form-control form-control-premium" rows="5" maxlength="1000" placeholder="Write a short biography...">{{ old('bio', $user->bio) }}</textarea>
                                            <div class="d-flex justify-content-between mt-1">
                                                <small class="text-muted">Maximum 1000 characters allowed</small>
                                                <small id="bio-warning" class="text-warning d-none">You are approaching the character limit</small>
                                            </div>
                                        </div>

                                        @if($user->role === 'teacher')
                                        <div class="col-md-6">
                                            <label class="form-label-premium">Specialization</label>
                                            <input type="text" class="form-control form-control-premium" value="{{ $user->teacher->specialization ?? '' }}" placeholder="e.g. Python, Data Science" disabled>
                                            <small class="text-muted">Managed via onboarding form.</small>
                                        </div>
                                        <div class="col-md-6">
                                            <label class="form-label-premium">Years of Experience</label>
                                            <select name="experience_years" class="form-select form-control-premium">
                                                <option value="">Select experience</option>
                                                @foreach(['1-3','3-5','5-10','10+'] as $exp)
                                                <option value="{{ $exp }}" {{ old('experience_years', $user->experience_years) == $exp ? 'selected' : '' }}>{{ $exp }} Years</option>
                                                @endforeach
                                            </select>
                                        </div>
                                        @endif
                                    </div>
                                </div>

                                <!-- Student Personal Details Section -->
                                @if($user->role === 'student')
                                <div class="mb-5">
                                    <h4 class="section-title">Personal Details</h4>
                                    @if($user->studentProfile && $user->studentProfile->is_complete)
                                    <div class="row g-4">
                                        <div class="col-md-4">
                                            <label class="form-label-premium">First Name</label>
                                            <input type="text" class="form-control form-control-premium" value="{{ $user->studentProfile->first_name }}" disabled>
                                        </div>
                                        <div class="col-md-4">
                                            <label class="form-label-premium">Last Name</label>
                                            <input type="text" class="form-control form-control-premium" value="{{ $user->studentProfile->last_name }}" disabled>
                                        </div>
                                        <div class="col-md-4">
                                            <label class="form-label-premium">Father's Name</label>
                                            <input type="text" class="form-control form-control-premium" value="{{ $user->studentProfile->father_name }}" disabled>
                                        </div>
                                        <div class="col-md-4">
                                            <label class="form-label-premium">Gender</label>
                                            <input type="text" class="form-control form-control-premium" value="{{ ucfirst($user->studentProfile->gender) }}" disabled>
                                        </div>
                                        <div class="col-md-4">
                                            <label class="form-label-premium">Date of Birth</label>
                                            <input type="text" class="form-control form-control-premium" value="{{ $user->studentProfile->date_of_birth->format('Y-m-d') }}" disabled>
                                        </div>
                                        <div class="col-md-4">
                                            <label class="form-label-premium">National ID (Tazkira)</label>
                                            <input type="text" class="form-control form-control-premium" value="{{ $user->studentProfile->national_id }}" disabled>
                                        </div>
                                        <div class="col-md-4">
                                            <label class="form-label-premium">Phone Number</label>
                                            <input type="text" class="form-control form-control-premium" value="{{ $user->studentProfile->phone_number }}" disabled>
                                        </div>
                                        @if($user->studentProfile->passport_number)
                                        <div class="col-md-4">
                                            <label class="form-label-premium">Passport Number</label>
                                            <input type="text" class="form-control form-control-premium" value="{{ $user->studentProfile->passport_number }}" disabled>
                                        </div>
                                        @endif
                                        <div class="col-md-4">
                                            <label class="form-label-premium">Province</label>
                                            <input type="text" class="form-control form-control-premium" value="{{ $user->studentProfile->province }}" disabled>
                                        </div>
                                        <div class="col-md-6">
                                            <label class="form-label-premium">Current Address</label>
                                            <input type="text" class="form-control form-control-premium" value="{{ $user->studentProfile->current_address }}" disabled>
                                        </div>
                                        <div class="col-md-6">
                                            <label class="form-label-premium">Education Level</label>
                                            <input type="text" class="form-control form-control-premium" value="{{ $user->studentProfile->last_education_level }} - {{ $user->studentProfile->last_school_name }}" disabled>
                                        </div>
                                    </div>
                                    <div class="mt-3">
                                        <a href="{{ route('student.profile-details') }}" class="btn btn-outline-primary btn-sm">
                                            <i class="bi bi-pencil me-1"></i>Edit Full Details
                                        </a>
                                    </div>
                                    @else
                                    <div class="text-center py-4 bg-light rounded-3">
                                        <i class="bi bi-person-vcard fs-1 text-muted d-block mb-2"></i>
                                        <p class="text-muted mb-3">You haven't completed your personal details yet.</p>
                                        <a href="{{ route('student.profile-details') }}" class="btn btn-primary">
                                            <i class="bi bi-pencil-square me-2"></i>Complete My Details
                                        </a>
                                    </div>
                                    @endif
                                </div>
                                @endif

                                <!-- Social Links Section -->
                                <div class="mb-5">
                                    <h4 class="section-title">Social Links</h4>
                                    <p class="text-muted mb-4 small">Add your social media to help students connect with
                                        you better.</p>

                                    <div class="row g-4">
                                        <div class="col-md-6">
                                            <div class="social-input-group">
                                                <div class="social-icon-wrapper text-primary">
                                                    <i class="bi bi-linkedin"></i>
                                                </div>
                                                <input type="url" name="linkedin" placeholder="LinkedIn Profile URL" value="{{ old('linkedin', $user->teacher->linkedin ?? '') }}">
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="social-input-group">
                                                <div class="social-icon-wrapper text-dark">
                                                    <i class="bi bi-github"></i>
                                                </div>
                                                <input type="url" name="github" placeholder="GitHub Profile URL" value="{{ old('github', $user->teacher->github ?? '') }}">
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="social-input-group">
                                                <div class="social-icon-wrapper text-danger">
                                                    <i class="bi bi-globe"></i>
                                                </div>
                                                <input type="url" name="website" placeholder="Personal Website or Portfolio" value="{{ old('website', $user->teacher->website ?? '') }}">
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <hr class="my-5 opacity-10">

                                <!-- Security / Password Section -->
                                <div class="mb-4">
                                    <div class="d-flex align-items-center mb-4">
                                        <div class="bg-light rounded-circle p-3 me-3 text-secondary">
                                            <i class="bi bi-shield-lock fs-4"></i>
                                        </div>
                                        <div>
                                            <h4 class="mb-1 text-premium" style="font-size: 1.1rem; font-weight: 700;">
                                                Password & Security</h4>
                                            <p class="text-muted small mb-0">Update your password to keep your account
                                                secure.</p>
                                        </div>
                                    </div>

                                    <div class="row g-4">
                                        <div class="col-md-4">
                                            <label class="form-label-premium">Current Password</label>
                                            <input type="password" class="form-control form-control-premium"
                                                placeholder="Enter current password">
                                        </div>
                                        <div class="col-md-4">
                                            <label class="form-label-premium">New Password</label>
                                            <input type="password" class="form-control form-control-premium"
                                                placeholder="Enter new password">
                                        </div>
                                        <div class="col-md-4">
                                            <label class="form-label-premium">Confirm New Password</label>
                                            <input type="password" class="form-control form-control-premium"
                                                placeholder="Confirm new password">
                                        </div>
                                    </div>
                                </div>

                                <div class="d-flex justify-content-end mt-5 gap-3">
                                    <button type="button" class="btn btn-premium-outline-alt" onclick="window.location.reload()">Cancel</button>
                                    <button type="submit" form="profile-form" class="btn btn-premium-solid">Save Profile Settings</button>
                                </div>
                            </form>
                        </div>

                    </div>
                </div>

            </div>
        </main>
    </div>

    <!-- Modern Footer -->
    

    <!-- Bootstrap 5 JS -->
    
    <!-- Custom JS -->
@endsection

@push('scripts')
<script src="{{ asset('assets/js/teacher-dashboard.js') }}"></script>
<script src="{{ asset('assets/js/modern-footer.js') }}"></script>
<script src="{{ asset('assets/js/pages/profile.js') }}"></script>
<script src="{{ asset('assets/js/beta-notice.js') }}"></script>
<script>
    // Preview & validate cover image
    function previewCoverImage(input) {
        if (!input.files || !input.files[0]) return;
        var file = input.files[0];

        // Validate size (max 5MB)
        if (file.size > 5 * 1024 * 1024) {
            alert('Cover image must be less than 5MB. Your file is ' + (file.size / 1024 / 1024).toFixed(1) + 'MB.');
            input.value = '';
            return;
        }

        var reader = new FileReader();
        reader.onload = function(e) {
            var coverDiv = document.querySelector('.profile-cover');
            coverDiv.style.backgroundImage = 'linear-gradient(135deg, rgba(37, 99, 235, 0.3), rgba(147, 51, 234, 0.3)), url(' + e.target.result + ')';
            coverDiv.style.backgroundSize = 'cover';
            coverDiv.style.backgroundPosition = 'center';
        };
        reader.readAsDataURL(file);
    }

    // Preview & validate avatar
    function previewAvatar(input) {
        if (!input.files || !input.files[0]) return;
        var file = input.files[0];

        // Validate size (max 2MB)
        if (file.size > 2 * 1024 * 1024) {
            alert('Profile photo must be less than 2MB. Your file is ' + (file.size / 1024 / 1024).toFixed(1) + 'MB.');
            input.value = '';
            return;
        }

        var reader = new FileReader();
        reader.onload = function(e) {
            var avatarImg = document.getElementById('avatarPreview');
            if (avatarImg) avatarImg.src = e.target.result;
        };
        reader.readAsDataURL(file);
    }

    // Bio character counter
    (function() {
        const bioTextarea = document.getElementById('bio-textarea');
        const charCount = document.getElementById('bio-char-count');
        const bioWarning = document.getElementById('bio-warning');
        const maxLength = 1000;

        function updateCharCount() {
            const currentLength = bioTextarea.value.length;
            charCount.textContent = currentLength;

            // Show warning when approaching limit (900+ characters)
            if (currentLength >= 900) {
                bioWarning.classList.remove('d-none');
                charCount.parentElement.classList.add('text-warning');
            } else {
                bioWarning.classList.add('d-none');
                charCount.parentElement.classList.remove('text-warning');
            }

            // Show danger color when at limit
            if (currentLength >= maxLength) {
                charCount.parentElement.classList.remove('text-warning');
                charCount.parentElement.classList.add('text-danger');
            } else {
                charCount.parentElement.classList.remove('text-danger');
            }
        }

        if (bioTextarea) {
            // Initialize with current value
            updateCharCount();

            // Update on input
            bioTextarea.addEventListener('input', updateCharCount);

            // Prevent typing beyond limit (extra safety)
            bioTextarea.addEventListener('keydown', function(e) {
                if (this.value.length >= maxLength &&
                    !['Backspace', 'Delete', 'ArrowLeft', 'ArrowRight', 'ArrowUp', 'ArrowDown', 'Tab', 'Escape', 'Enter'].includes(e.key) &&
                    !(e.ctrlKey || e.metaKey)) {
                    e.preventDefault();
                }
            });
        }
    })();
</script>
@endpush
