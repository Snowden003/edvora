@extends('layouts.app')

@section('title', 'Admin Dashboard - Edvora Tech')

@push('styles')
<link href="{{ asset('assets/css/dashboard.css') }}" rel="stylesheet" />
<link href="{{ asset('assets/css/events-pages.css') }}" rel="stylesheet" />
<link href="{{ asset('assets/css/courses-pages.css') }}" rel="stylesheet" />
<link href="{{ asset('assets/css/auth-pages.css') }}" rel="stylesheet" />
<link href="{{ asset('assets/css/beta-notice.css') }}" rel="stylesheet" />
@endpush

@section('content')
<div class="d-flex">
    <!-- Sidebar -->
    <div class="admin-sidebar" style="width: 260px; min-height: calc(100vh - 60px); background: #1a1a2e; color: white;">
        <div class="p-4 border-bottom border-secondary">
            <h5 class="mb-0"><i class="bi bi-shield-lock me-2"></i>Admin Panel</h5>
            <small class="text-white-50">Secure Access Only</small>
        </div>
        <nav class="nav flex-column py-3">
            <a href="{{ route('admin.dashboard') }}" class="nav-link active" style="color: rgba(255,255,255,0.7); padding: 12px 20px; border-radius: 0; transition: all 0.3s;">
                <i class="bi bi-speedometer2" style="width: 24px; margin-right: 10px;"></i>Dashboard
            </a>
            <a href="{{ route('filament.admin.resources.courses.index') }}" class="nav-link" style="color: rgba(255,255,255,0.7); padding: 12px 20px; border-radius: 0; transition: all 0.3s;">
                <i class="bi bi-book" style="width: 24px; margin-right: 10px;"></i>Courses
            </a>
            <a href="{{ route('filament.admin.resources.teachers.index') }}" class="nav-link" style="color: rgba(255,255,255,0.7); padding: 12px 20px; border-radius: 0; transition: all 0.3s;">
                <i class="bi bi-person-video3" style="width: 24px; margin-right: 10px;"></i>Teachers
            </a>
            <a href="{{ route('admin.events.index') }}" class="nav-link" style="color: rgba(255,255,255,0.7); padding: 12px 20px; border-radius: 0; transition: all 0.3s;">
                <i class="bi bi-calendar-event" style="width: 24px; margin-right: 10px;"></i>Events
            </a>
            <a href="{{ route('filament.admin.resources.contact-messages.index') }}" class="nav-link" style="color: rgba(255,255,255,0.7); padding: 12px 20px; border-radius: 0; transition: all 0.3s;">
                <i class="bi bi-envelope" style="width: 24px; margin-right: 10px;"></i>Messages
            </a>
        </nav>
    </div>

    <!-- Main Content -->
    <main class="dashboard-content flex-grow-1">
            <!-- Header -->
            <div class="d-flex justify-content-between align-items-center mb-4">
                <div>
                    <h2 class="fw-bold" style="color: #1F8FFF;">Admin Dashboard</h2>
                    <p class="text-muted mb-0">Welcome back, Administrator</p>
                </div>
                <div class="d-flex gap-2">
                    <button class="btn btn-outline-primary">
                        <i class="bi bi-bell"></i>
                    </button>
                    <div class="dropdown">
                        <button class="btn btn-primary dropdown-toggle" data-bs-toggle="dropdown">
                            <i class="bi bi-person-circle me-1"></i>
                        </button>
                        <ul class="dropdown-menu">
                            <li><a class="dropdown-item" href="#"><i class="bi bi-person me-2"></i>Profile</a></li>
                            <li><a class="dropdown-item" href="#"><i class="bi bi-gear me-2"></i>Settings</a></li>
                            <li><a class="dropdown-item" href="{{ route('terms') }}"><i class="bi bi-file-text me-2"></i>Terms</a>
                            </li>
                            <li>
                                <hr class="dropdown-divider">
                            </li>
                            <li><a class="dropdown-item" href="{{ route('login') }}"><i
                                        class="bi bi-box-arrow-right me-2"></i>Logout</a></li>
                        </ul>
                    </div>
                </div>
            </div>

            <!-- Stats Cards -->
            <div class="row g-4 mb-4">
                <div class="col-lg-3 col-md-6">
                    <div class="stats-card">
                        <div class="stats-number">2,847</div>
                        <div class="stats-label">Total Students</div>
                        <small class="text-white-50">
                            <i class="bi bi-arrow-up"></i> +12% from last month
                        </small>
                    </div>
                </div>
                <div class="col-lg-3 col-md-6">
                    <div class="stats-card">
                        <div class="stats-number">156</div>
                        <div class="stats-label">Active Teachers</div>
                        <small class="text-white-50">
                            <i class="bi bi-arrow-up"></i> +5% from last month
                        </small>
                    </div>
                </div>
                <div class="col-lg-3 col-md-6">
                    <div class="stats-card">
                        <div class="stats-number">89</div>
                        <div class="stats-label">Total Courses</div>
                        <small class="text-white-50">
                            <i class="bi bi-arrow-up"></i> +8% from last month
                        </small>
                    </div>
                </div>
                <div class="col-lg-3 col-md-6">
                    <div class="stats-card">
                        <div class="stats-number">$45,672</div>
                        <div class="stats-label">Monthly Revenue</div>
                        <small class="text-white-50">
                            <i class="bi bi-arrow-up"></i> +18% from last month
                        </small>
                    </div>
                </div>
            </div>

            <!-- Charts Row -->
            <div class="row g-4 mb-4">
                <div class="col-lg-8">
                    <div class="card">
                        <div class="card-header d-flex justify-content-between align-items-center">
                            <h5 class="mb-0">Revenue Analytics</h5>
                            <div class="btn-group btn-group-sm">
                                <button class="btn btn-outline-primary active">Monthly</button>
                                <button class="btn btn-outline-primary">Weekly</button>
                                <button class="btn btn-outline-primary">Daily</button>
                            </div>
                        </div>
                        <div class="card-body">
                            <canvas id="revenueChart" height="100"></canvas>
                        </div>
                    </div>
                </div>
                <div class="col-lg-4">
                    <div class="card">
                        <div class="card-header">
                            <h5 class="mb-0">Course Categories</h5>
                        </div>
                        <div class="card-body">
                            <canvas id="categoryChart"></canvas>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Data Tables -->
            <div class="row g-4">
                <!-- Recent Students -->
                <div class="col-lg-6">
                    <div class="card">
                        <div class="card-header d-flex justify-content-between align-items-center">
                            <h5 class="mb-0">Recent Students</h5>
                            <button class="btn btn-primary btn-sm" data-bs-toggle="modal"
                                data-bs-target="#addStudentModal">
                                <i class="bi bi-plus"></i> Add Student
                            </button>
                        </div>
                        <div class="card-body p-0">
                            <div class="table-responsive">
                                <table class="table table-hover mb-0">
                                    <thead>
                                        <tr>
                                            <th>Name</th>
                                            <th>Email</th>
                                            <th>Courses</th>
                                            <th>Status</th>
                                            <th>Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody id="studentsTable">
                                        <!-- Students will be populated by JavaScript -->
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Recent Teachers -->
                <div class="col-lg-6">
                    <div class="card">
                        <div class="card-header d-flex justify-content-between align-items-center">
                            <h5 class="mb-0">Recent Teachers</h5>
                            <button class="btn btn-primary btn-sm" data-bs-toggle="modal"
                                data-bs-target="#addTeacherModal">
                                <i class="bi bi-plus"></i> Add Teacher
                            </button>
                        </div>
                        <div class="card-body p-0">
                            <div class="table-responsive">
                                <table class="table table-hover mb-0">
                                    <thead>
                                        <tr>
                                            <th>Name</th>
                                            <th>Specialization</th>
                                            <th>Students</th>
                                            <th>Rating</th>
                                            <th>Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody id="teachersTable">
                                        <!-- Teachers will be populated by JavaScript -->
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Recent Activity -->
            <div class="row g-4 mt-2">
                <div class="col-12">
                    <div class="card">
                        <div class="card-header">
                            <h5 class="mb-0">Recent Activity</h5>
                        </div>
                        <div class="card-body">
                            <div id="recentActivity">
                                <!-- Activity will be populated by JavaScript -->
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </main>
    </div>

    <!-- Add Student Modal -->
    <div class="modal fade" id="addStudentModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Add New Student</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <form id="addStudentForm">
                        <div class="mb-3">
                            <label for="studentName" class="form-label">Full Name</label>
                            <input type="text" class="form-control" id="studentName" required>
                        </div>
                        <div class="mb-3">
                            <label for="studentEmail" class="form-label">Email</label>
                            <input type="email" class="form-control" id="studentEmail" required>
                        </div>
                        <div class="mb-3">
                            <label for="studentPhone" class="form-label">Phone</label>
                            <input type="tel" class="form-control" id="studentPhone">
                        </div>
                        <div class="mb-3">
                            <label for="studentCourse" class="form-label">Initial Course</label>
                            <select class="form-select" id="studentCourse">
                                <option value="">Select a course</option>
                                <option value="web-dev">Web Development</option>
                                <option value="data-science">Data Science</option>
                                <option value="digital-marketing">Digital Marketing</option>
                            </select>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="button" class="btn btn-primary" onclick="addStudent()">Add Student</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Add Teacher Modal -->
    <div class="modal fade" id="addTeacherModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Add New Teacher</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <form id="addTeacherForm">
                        <div class="mb-3">
                            <label for="teacherName" class="form-label">Full Name</label>
                            <input type="text" class="form-control" id="teacherName" required>
                        </div>
                        <div class="mb-3">
                            <label for="teacherEmail" class="form-label">Email</label>
                            <input type="email" class="form-control" id="teacherEmail" required>
                        </div>
                        <div class="mb-3">
                            <label for="teacherSpecialization" class="form-label">Specialization</label>
                            <input type="text" class="form-control" id="teacherSpecialization" required>
                        </div>
                        <div class="mb-3">
                            <label for="teacherBio" class="form-label">Bio</label>
                            <textarea class="form-control" id="teacherBio" rows="3"></textarea>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="button" class="btn btn-primary" onclick="addTeacher()">Add Teacher</button>
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

@push('styles')
<style>
    .admin-sidebar .nav-link:hover,
    .admin-sidebar .nav-link.active {
        color: white !important;
        background: rgba(31, 143, 255, 0.2) !important;
        border-left: 3px solid #1F8FFF !important;
    }
    .admin-sidebar .nav-link:hover {
        text-decoration: none;
    }
</style>
@endpush

@push('scripts')
<script src="{{ asset('assets/js/admin-dashboard.js') }}"></script>
<script src="{{ asset('assets/js/modern-footer.js') }}"></script>
<script src="{{ asset('assets/js/beta-notice.js') }}"></script>
@endpush
