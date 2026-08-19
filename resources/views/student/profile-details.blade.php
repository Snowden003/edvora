@extends('layouts.app')

@section('title', 'My Details - Edvora Tech')

@push('styles')
<link href="{{ asset('assets/css/student-dashboard.css') }}" rel="stylesheet" />
<link href="{{ asset('assets/css/student-profile-details.css') }}" rel="stylesheet" />
@endpush

@section('content')
<div class="dashboard-wrapper">
    <x-student-sidebar />

    <main class="main-content" id="mainContent">
        <div class="container-fluid py-5">
            <div class="row">
                <div class="col-lg-10 col-12 mx-auto">
                    <!-- Header -->
                    <div class="d-flex justify-content-between align-items-center mb-4">
                        <div>
                            <h2 class="fw-bold primary-blue-text mb-1">
                                <i class="bi bi-person-vcard me-2"></i>My Personal Details
                            </h2>
                            <p class="text-muted mb-0">Complete your profile with accurate information. Fields marked with <span class="text-danger">*</span> are required.</p>
                        </div>
                        @if($profile && $profile->is_complete)
                        <span class="badge bg-success px-3 py-2 fs-6">
                            <i class="bi bi-check-circle me-1"></i>Profile Complete
                        </span>
                        @else
                        <span class="badge bg-warning text-dark px-3 py-2 fs-6">
                            <i class="bi bi-exclamation-triangle me-1"></i>Incomplete
                        </span>
                        @endif
                    </div>

                    @if(session('success'))
                    <div class="alert alert-success alert-dismissible fade show" role="alert">
                        <i class="bi bi-check-circle me-2"></i>{{ session('success') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                    @endif

                    @if($errors->any())
                    <div class="alert alert-danger alert-dismissible fade show" role="alert">
                        <i class="bi bi-exclamation-triangle me-2"></i>Please fix the errors below.
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                    @endif

                    <form action="{{ route('student.profile-details.store') }}" method="POST" enctype="multipart/form-data">
                        @csrf

                        <!-- Section 1: Personal Information -->
                        <div class="profile-section-card mb-4">
                            <div class="section-header">
                                <i class="bi bi-person-fill"></i>
                                <h5 class="mb-0">Personal Information</h5>
                            </div>
                            <div class="section-body">
                                <div class="row g-3">
                                    <div class="col-md-4">
                                        <label class="form-label">First Name <span class="text-danger">*</span></label>
                                        <input type="text" name="first_name" class="form-control @error('first_name') is-invalid @enderror"
                                               value="{{ old('first_name', $profile->first_name ?? '') }}" required>
                                        @error('first_name')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                    </div>
                                    <div class="col-md-4">
                                        <label class="form-label">Last Name <span class="text-danger">*</span></label>
                                        <input type="text" name="last_name" class="form-control @error('last_name') is-invalid @enderror"
                                               value="{{ old('last_name', $profile->last_name ?? '') }}" required>
                                        @error('last_name')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                    </div>
                                    <div class="col-md-4">
                                        <label class="form-label">Father's Name <span class="text-danger">*</span></label>
                                        <input type="text" name="father_name" class="form-control @error('father_name') is-invalid @enderror"
                                               value="{{ old('father_name', $profile->father_name ?? '') }}" required>
                                        @error('father_name')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                    </div>
                                    <!-- Hidden Gender - Female Only System -->
                                    <input type="hidden" name="gender" value="female">
                                    <div class="col-md-4">
                                        <label class="form-label">Date of Birth <span class="text-danger">*</span></label>
                                        <input type="date" name="date_of_birth" class="form-control @error('date_of_birth') is-invalid @enderror"
                                               value="{{ old('date_of_birth', isset($profile->date_of_birth) ? $profile->date_of_birth->format('Y-m-d') : '') }}" required>
                                        @error('date_of_birth')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                    </div>
                                    <div class="col-md-4">
                                        <label class="form-label">National ID (Tazkira) <span class="text-danger">*</span></label>
                                        <input type="text" name="national_id" class="form-control @error('national_id') is-invalid @enderror"
                                               value="{{ old('national_id', $profile->national_id ?? '') }}" required
                                               placeholder="e.g. 1401-0123-45678">
                                        @error('national_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                    </div>
                                    <div class="col-md-4">
                                        <label class="form-label">Passport Number</label>
                                        <input type="text" name="passport_number" class="form-control"
                                               value="{{ old('passport_number', $profile->passport_number ?? '') }}"
                                               placeholder="Optional">
                                    </div>
                                    <div class="col-md-4">
                                        <label class="form-label">Phone Number <span class="text-danger">*</span></label>
                                        <input type="text" name="phone_number" class="form-control @error('phone_number') is-invalid @enderror"
                                               value="{{ old('phone_number', $profile->phone_number ?? '') }}" required
                                               placeholder="e.g. 0770123456">
                                        @error('phone_number')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                    </div>
                                    <div class="col-md-4">
                                        <label class="form-label">WhatsApp Number</label>
                                        <input type="text" name="whatsapp_number" class="form-control @error('whatsapp_number') is-invalid @enderror"
                                               value="{{ old('whatsapp_number', $profile->whatsapp_number ?? '') }}"
                                               placeholder="e.g. 0770123456">
                                        @error('whatsapp_number')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Section 2: Address Information -->
                        <div class="profile-section-card mb-4">
                            <div class="section-header">
                                <i class="bi bi-geo-alt-fill"></i>
                                <h5 class="mb-0">Address Information</h5>
                            </div>
                            <div class="section-body">
                                <div class="row g-3">
                                    <div class="col-md-4">
                                        <label class="form-label">Province <span class="text-danger">*</span></label>
                                        <input type="text" name="province" class="form-control @error('province') is-invalid @enderror"
                                               value="{{ old('province', $profile->province ?? '') }}" required
                                               placeholder="e.g. Kabul">
                                        @error('province')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                    </div>
                                    <div class="col-md-4">
                                        <label class="form-label">District <span class="text-danger">*</span></label>
                                        <input type="text" name="district" class="form-control @error('district') is-invalid @enderror"
                                               value="{{ old('district', $profile->district ?? '') }}" required>
                                        @error('district')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                    </div>
                                    <div class="col-md-4">
                                        <label class="form-label">Postal Code</label>
                                        <input type="text" name="postal_code" class="form-control"
                                               value="{{ old('postal_code', $profile->postal_code ?? '') }}">
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Section 3: Education Background -->
                        <div class="profile-section-card mb-4">
                            <div class="section-header">
                                <i class="bi bi-mortarboard-fill"></i>
                                <h5 class="mb-0">Education Background</h5>
                            </div>
                            <div class="section-body">
                                <div class="row g-3">
                                    <div class="col-md-6">
                                        <label class="form-label">Last Education Level <span class="text-danger">*</span></label>
                                        <select name="last_education_level" class="form-select @error('last_education_level') is-invalid @enderror" required>
                                            <option value="">Select Level</option>
                                            @foreach(['Primary School', 'Middle School', '9th Grade', '10th Grade', '11th Grade', '12th Grade', 'Diploma', 'Associate Degree', 'Bachelor', 'Master', 'PhD'] as $level)
                                            <option value="{{ $level }}" {{ old('last_education_level', $profile->last_education_level ?? '') == $level ? 'selected' : '' }}>{{ $level }}</option>
                                            @endforeach
                                        </select>
                                        @error('last_education_level')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label">Last School/Institute Name <span class="text-danger">*</span></label>
                                        <input type="text" name="last_school_name" class="form-control @error('last_school_name') is-invalid @enderror"
                                               value="{{ old('last_school_name', $profile->last_school_name ?? '') }}" required>
                                        @error('last_school_name')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Section 4: Emergency Contact -->
                        <div class="profile-section-card mb-4">
                            <div class="section-header">
                                <i class="bi bi-telephone-fill"></i>
                                <h5 class="mb-0">Emergency Contact</h5>
                            </div>
                            <div class="section-body">
                                <div class="row g-3">
                                    <div class="col-md-4">
                                        <label class="form-label">Contact Name <span class="text-danger">*</span></label>
                                        <input type="text" name="emergency_contact_name" class="form-control @error('emergency_contact_name') is-invalid @enderror"
                                               value="{{ old('emergency_contact_name', $profile->emergency_contact_name ?? '') }}" required>
                                        @error('emergency_contact_name')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                    </div>
                                    <div class="col-md-4">
                                        <label class="form-label">Contact Phone <span class="text-danger">*</span></label>
                                        <input type="text" name="emergency_contact_phone" class="form-control @error('emergency_contact_phone') is-invalid @enderror"
                                               value="{{ old('emergency_contact_phone', $profile->emergency_contact_phone ?? '') }}" required>
                                        @error('emergency_contact_phone')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                    </div>
                                    <div class="col-md-4">
                                        <label class="form-label">Relationship <span class="text-danger">*</span></label>
                                        <select name="emergency_contact_relation" class="form-select @error('emergency_contact_relation') is-invalid @enderror" required>
                                            <option value="">Select</option>
                                            @foreach(['Father', 'Mother', 'Brother', 'Sister', 'Spouse', 'Uncle', 'Aunt', 'Friend', 'Other'] as $rel)
                                            <option value="{{ $rel }}" {{ old('emergency_contact_relation', $profile->emergency_contact_relation ?? '') == $rel ? 'selected' : '' }}>{{ $rel }}</option>
                                            @endforeach
                                        </select>
                                        @error('emergency_contact_relation')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Section 5: Additional Information -->
                        <div class="profile-section-card mb-4">
                            <div class="section-header">
                                <i class="bi bi-info-circle-fill"></i>
                                <h5 class="mb-0">Additional Information</h5>
                            </div>
                            <div class="section-body">
                                <div class="row g-3">
                                    <div class="col-md-6">
                                        <label class="form-label">Skills</label>
                                        <textarea name="skills" class="form-control" rows="3"
                                                  placeholder="e.g. HTML, CSS, JavaScript, Python, Networking...">{{ old('skills', $profile->skills ?? '') }}</textarea>
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label">Languages</label>
                                        <textarea name="languages" class="form-control" rows="3"
                                                  placeholder="e.g. Dari (Native), Pashto (Fluent), English (Intermediate)...">{{ old('languages', $profile->languages ?? '') }}</textarea>
                                    </div>
                                    <div class="col-12">
                                        <label class="form-label">About Me</label>
                                        <textarea name="about_me" class="form-control" rows="4"
                                                  placeholder="Tell us about yourself, your goals, interests...">{{ old('about_me', $profile->about_me ?? '') }}</textarea>
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label">Profile Photo</label>
                                        <input type="file" name="profile_photo" class="form-control @error('profile_photo') is-invalid @enderror" accept="image/*">
                                        @error('profile_photo')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                        @if($profile && $profile->profile_photo)
                                        <div class="mt-2">
                                            <img src="{{ asset('storage/' . $profile->profile_photo) }}" alt="Profile Photo"
                                                 class="rounded" style="width:80px; height:80px; object-fit:cover;">
                                            <small class="text-muted ms-2">Current photo</small>
                                        </div>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Submit Button -->
                        <div class="text-end">
                            <button type="submit" class="btn btn-primary btn-lg px-5">
                                <i class="bi bi-check-lg me-2"></i>Save Profile
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
<script src="{{ asset('assets/js/student-dashboard.js') }}"></script>
@endpush
