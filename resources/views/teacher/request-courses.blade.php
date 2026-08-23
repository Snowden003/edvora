@extends('layouts.app')

@section('title', 'Request Courses - Edvora Tech')

@push('styles')
<link href="{{ asset('assets/css/teacher-dashboard.css') }}" rel="stylesheet" />
<link href="{{ asset('assets/css/request-courses.css') }}" rel="stylesheet" />
<link href="{{ asset('assets/css/beta-notice.css') }}" rel="stylesheet" />
<style>
.rc-hero { background: linear-gradient(135deg,#1e40af,#4f46e5); border-radius: 20px; padding: 2.5rem 2rem; color: #fff; margin-bottom: 2rem; }
.rc-hero h1 { font-size: 1.8rem; font-weight: 800; margin: 0; }
.rc-hero p  { color: #fff; margin: .5rem 0 0; font-size: .95rem; }

.rc-tabs { display: flex; gap: 8px; background: #f1f5f9; border-radius: 14px; padding: 6px; margin-bottom: 2rem; width: fit-content; }
.rc-tab-btn { padding: 9px 22px; border-radius: 10px; border: none; background: transparent; font-weight: 600; font-size: .88rem; color: #64748b; cursor: pointer; transition: all .2s; }
.rc-tab-btn.active { background: #fff; color: #1F8FFF; box-shadow: 0 2px 8px rgba(0,0,0,.1); }

.rc-filter-bar { display: flex; gap: 8px; flex-wrap: wrap; margin-bottom: 1.5rem; }
.rc-filter { padding: 6px 16px; border-radius: 20px; border: 1.5px solid #e2e8f0; background: #fff; color: #64748b; cursor: pointer; font-size: .82rem; font-weight: 600; transition: all .15s; }
.rc-filter.active, .rc-filter:hover { background: #eff6ff; color: #1F8FFF; border-color: #bfdbfe; }

.rc-card { background: #fff; border-radius: 16px; box-shadow: 0 2px 14px rgba(0,0,0,.07); overflow: hidden; transition: transform .2s, box-shadow .2s; height: 100%; display: flex; flex-direction: column; }
.rc-card:hover { transform: translateY(-3px); box-shadow: 0 8px 28px rgba(0,0,0,.12); }
.rc-card-img { height: 160px; object-fit: cover; width: 100%; background: linear-gradient(135deg,#1e40af,#4f46e5); display: flex; align-items: center; justify-content: center; }
.rc-card-img i { font-size: 3rem; color: rgba(255,255,255,.6); }
.rc-card-body { padding: 1.25rem; flex-grow: 1; display: flex; flex-direction: column; }
.rc-card-cat { font-size: .72rem; font-weight: 700; text-transform: uppercase; letter-spacing: .5px; color: #1F8FFF; background: #eff6ff; border-radius: 20px; padding: 3px 10px; display: inline-block; margin-bottom: 8px; }
.rc-card-title { font-size: 1rem; font-weight: 700; color: #1e293b; margin-bottom: 6px; }
.rc-card-desc { font-size: .83rem; color: #64748b; flex-grow: 1; margin-bottom: 12px; display: -webkit-box; -webkit-line-clamp: 3; -webkit-box-orient: vertical; overflow: hidden; }
.rc-card-meta { display: flex; gap: 10px; flex-wrap: wrap; margin-bottom: 14px; }
.rc-meta-chip { font-size: .75rem; background: #f8fafc; border-radius: 8px; padding: 4px 10px; color: #475569; font-weight: 600; }
.rc-req-btn { background: linear-gradient(135deg,#1F8FFF,#6366f1); color: #fff; border: none; border-radius: 10px; padding: 9px 0; width: 100%; font-weight: 700; font-size: .88rem; cursor: pointer; transition: opacity .2s; }
.rc-req-btn:hover { opacity: .9; }
.rc-req-btn:disabled { background: #e2e8f0; color: #94a3b8; cursor: not-allowed; }

.rc-empty { text-align: center; padding: 4rem 2rem; color: #94a3b8; }
.rc-empty i { font-size: 3rem; display: block; margin-bottom: 1rem; opacity: .4; }
.rc-empty p { font-size: .95rem; }

</style>
@endpush

@section('content')
    <div class="dashboard-wrapper">
        @if(Auth::user()->role === 'teacher')
            <x-teacher-sidebar />
        @else
            <x-student-sidebar />
        @endif

        <main class="main-content p-4 p-md-5">

            {{-- Hero --}}
            <div class="rc-hero mb-4">
                <h1><i class="bi bi-shop me-2"></i>Course Marketplace</h1>
                <p>Browse unpublished courses and submit a request to teach them — or track your existing requests.</p>
            </div>

            {{-- Alerts --}}
            @if(session('req_success'))
            <div class="alert alert-success alert-dismissible fade show rounded-3 mb-4">
                <i class="bi bi-check-circle-fill me-2"></i>{{ session('req_success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
            @endif
            @if(session('req_error'))
            <div class="alert alert-warning alert-dismissible fade show rounded-3 mb-4">
                <i class="bi bi-exclamation-triangle-fill me-2"></i>{{ session('req_error') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
            @endif

            {{-- Tabs --}}
            <div class="rc-tabs">
                <button class="rc-tab-btn active" onclick="rcSwitchTab('marketplace', this)">
                    <i class="bi bi-grid me-2"></i>Available Courses
                    <span class="badge rounded-pill ms-1" style="background:#dbeafe;color:#1d4ed8;font-size:.7rem;">{{ $availableCourses->count() }}</span>
                </button>
                <button class="rc-tab-btn" onclick="rcSwitchTab('requests', this)">
                    <i class="bi bi-clock-history me-2"></i>My Requests
                    <span class="badge rounded-pill ms-1" style="background:#fef9c3;color:#92400e;font-size:.7rem;">{{ $myRequests->count() }}</span>
                </button>
            </div>

            {{-- ── MARKETPLACE ── --}}
            <div id="rc-tab-marketplace">

                {{-- Category filters dropdown for mobile --}}
                <div class="rc-filter-dropdown mb-3">
                    <label class="rc-filter-label"><i class="bi bi-funnel me-1"></i>Category</label>
                    <select class="rc-filter-select" id="categorySelect" onchange="filterByCategory(this.value)">
                        <option value="all">All Categories</option>
                        @foreach($categories as $cat)
                        <option value="{{ $cat->id }}">{{ $cat->name }}</option>
                        @endforeach
                    </select>
                </div>

                {{-- Category filters bar for desktop --}}
                <div class="rc-filter-bar d-none d-md-flex">
                    <span class="rc-filter active" data-cat="all">All Categories</span>
                    @foreach($categories as $cat)
                    <span class="rc-filter" data-cat="{{ $cat->id }}">{{ $cat->name }}</span>
                    @endforeach
                </div>

                @if($availableCourses->count())
                <div class="row g-4" id="rcGrid">
                    @foreach($availableCourses as $c)
                    <div class="col-md-6 col-lg-4 rc-grid-item" data-cat="{{ $c->category_id }}">
                        <div class="rc-card">
                            <div class="rc-card-img" style="padding: 0; overflow: hidden;">
                                @if($c->thumbnail)
                                    <img src="{{ asset('storage/' . $c->thumbnail) }}" alt="{{ $c->title }}" style="width: 100%; height: 100%; object-fit: cover;">
                                @else
                                    <i class="bi bi-journal-bookmark-fill"></i>
                                @endif
                            </div>
                            <div class="rc-card-body">
                                <span class="rc-card-cat">{{ $c->category_name }}</span>
                                <div class="rc-card-title">{{ $c->title }}</div>
                                <div class="rc-card-desc">{{ strip_tags($c->description) }}</div>
                                <div class="rc-card-meta">
                                    @if($c->level)
                                    <span class="rc-meta-chip"><i class="bi bi-bar-chart me-1"></i>{{ ucfirst($c->level) }}</span>
                                    @endif
                                    @if($c->duration_hours)
                                    <span class="rc-meta-chip"><i class="bi bi-clock me-1"></i>{{ $c->duration_hours }} hrs</span>
                                    @endif
                                </div>
                                @php
                                    $alreadyRequested = $myRequests->where('title', $c->title)->count() > 0;
                                @endphp
                                @if($alreadyRequested)
                                    <button class="rc-req-btn" disabled>
                                        <i class="bi bi-check-circle-fill me-2"></i>Already Requested
                                    </button>
                                @else
                                    <button class="rc-req-btn" onclick="openReqModal({{ $c->id }}, '{{ addslashes($c->title) }}', '{{ addslashes($c->category_name) }}')">
                                        <i class="bi bi-plus-circle me-2"></i>Request This Course
                                    </button>
                                @endif
                            </div>
                        </div>
                    </div>
                    @endforeach
                </div>
                @else
                <div class="rc-empty">
                    <i class="bi bi-shop"></i>
                    <p>No unpublished courses available right now. Check back later or contact the admin.</p>
                </div>
                @endif
            </div>

            {{-- ── MY REQUESTS ── --}}
            <div id="rc-tab-requests" style="display:none;width:100%;">
                @if($myRequests->count())
                <div class="row justify-content-center">
                <div class="col-12 col-lg-10">
                    @foreach($myRequests as $r)
                    <div class="req-card status-{{ $r->status }}">
                        <div class="req-card-header">
                            <div style="min-width:0;flex:1;">
                                <div class="req-card-title">{{ $r->title }}</div>
                                <div class="req-card-meta">
                                    <span><i class="bi bi-tag me-1"></i>{{ $r->category_name }}</span>
                                    <span><i class="bi bi-calendar3 me-1"></i>{{ \Carbon\Carbon::parse($r->created_at)->format('M d, Y') }}</span>
                                </div>
                            </div>
                            <span class="req-status {{ $r->status }}">
                                @if($r->status === 'approved') <i class="bi bi-check-circle-fill"></i>
                                @elseif($r->status === 'rejected') <i class="bi bi-x-circle-fill"></i>
                                @else <i class="bi bi-hourglass-split"></i>
                                @endif
                                {{ ucfirst($r->status) }}
                            </span>
                        </div>

                        @if($r->description)
                        <div class="req-note-box">
                            <p class="req-note-box-label"><i class="bi bi-chat-left-text me-1"></i>Your Note</p>
                            <p class="req-note-box-text">{{ $r->description }}</p>
                        </div>
                        @endif

                        @if($r->admin_notes)
                        <div class="req-admin-box">
                            <p class="req-admin-box-label"><i class="bi bi-person-badge me-1"></i>Admin Response</p>
                            <p class="req-admin-box-text">{{ $r->admin_notes }}</p>
                        </div>
                        @endif

                        @if($r->status === 'pending')
                        <div class="req-pending-hint d-flex justify-content-between align-items-center flex-wrap gap-2">
                            <div><i class="bi bi-info-circle"></i>Responses usually take 2–3 business days.</div>
                            <div class="d-flex gap-2">
                                <button class="btn btn-sm btn-outline-primary rounded-pill px-3" onclick="openEditModal({{ $r->id }}, '{{ addslashes($r->title) }}', '{{ addslashes($r->description ?? '') }}')">
                                    <i class="bi bi-pencil me-1"></i>Edit
                                </button>
                                <button class="btn btn-sm btn-outline-danger rounded-pill px-3" onclick="openCancelModal({{ $r->id }}, '{{ addslashes($r->title) }}')">
                                    <i class="bi bi-x-circle me-1"></i>Cancel
                                </button>
                            </div>
                        </div>
                        @endif
                    </div>
                    @endforeach
                </div>
                </div>
                @else
                <div class="rc-empty">
                    <i class="bi bi-clock-history"></i>
                    <p>You haven't made any requests yet. Browse available courses and request one!</p>
                </div>
                @endif
            </div>

        </main>
    </div>

    {{-- Edit Request Modal --}}
    <div class="modal fade" id="editReqModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content rounded-4 border-0 shadow-lg">
                <div class="modal-header border-0 pb-0 px-4 pt-4">
                    <div>
                        <h5 class="fw-bold mb-0" id="editReqModalTitle">Edit Request</h5>
                        <p class="text-muted small mb-0">Update your request note</p>
                    </div>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body px-4 py-3">
                    <form method="POST" action="" id="editReqForm">
                        @csrf
                        @method('PUT')
                        <input type="hidden" name="request_id" id="editReqId">
                        <div class="mb-3">
                            <label class="form-label fw-semibold" style="font-size:.85rem;color:#475569;">
                                Personal Note <span class="text-muted fw-normal">(optional)</span>
                            </label>
                            <textarea name="description" id="editReqDescription" rows="4" class="form-control rounded-3"
                                placeholder="Briefly explain why you are a good fit for this course...">{{ old('description') }}</textarea>
                        </div>
                        <button type="submit" class="rc-req-btn py-2">
                            <i class="bi bi-check-circle me-2"></i>Save Changes
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    {{-- Cancel Request Modal --}}
    <div class="modal fade" id="cancelReqModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content rounded-4 border-0 shadow-lg">
                <div class="modal-header border-0 pb-0 px-4 pt-4">
                    <div>
                        <h5 class="fw-bold mb-0 text-danger"><i class="bi bi-exclamation-triangle me-2"></i>Cancel Request</h5>
                        <p class="text-muted small mb-0">Are you sure you want to cancel this request?</p>
                    </div>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body px-4 py-3">
                    <div class="alert alert-light rounded-3 mb-3">
                        <strong id="cancelReqTitle" class="text-dark"></strong>
                    </div>
                    <p class="text-muted small mb-4">This action cannot be undone. You will need to submit a new request if you change your mind.</p>
                    <form method="POST" action="" id="cancelReqForm">
                        @csrf
                        @method('DELETE')
                        <div class="d-flex gap-2">
                            <button type="button" class="btn btn-light rounded-pill flex-fill" data-bs-dismiss="modal">
                                <i class="bi bi-arrow-left me-1"></i>Back
                            </button>
                            <button type="submit" class="btn btn-danger rounded-pill flex-fill">
                                <i class="bi bi-x-circle me-1"></i>Yes, Cancel
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    {{-- Request Modal --}}
    <div class="modal fade" id="rcModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content rounded-4 border-0 shadow-lg">
                <div class="modal-header border-0 pb-0 px-4 pt-4">
                    <div>
                        <h5 class="fw-bold mb-0" id="rcModalTitle">Request Course</h5>
                        <p class="text-muted small mb-0" id="rcModalCat"></p>
                    </div>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body px-4 py-3">
                    <form method="POST" action="{{ auth()->user()->role === 'teacher' ? route('teacher.request-courses.store') : route('student.request-courses.store') }}" id="rcForm">
                        @csrf
                        <input type="hidden" name="course_id" id="rcCourseId">
                        <div class="mb-3">
                            <label class="form-label fw-semibold" style="font-size:.85rem;color:#475569;">
                                Personal Note <span class="text-danger">*</span> <span class="text-muted fw-normal">(minimum 20 characters)</span>
                            </label>

                            {{-- Quick Templates --}}
                            <div class="mb-2">
                                <small class="text-muted d-block mb-1">Quick templates:</small>
                                <div class="d-flex flex-wrap gap-1">
                                    <button type="button" class="btn btn-xs btn-outline-secondary btn-sm rounded-pill" onclick="setNoteTemplate('template1')">
                                        <i class="bi bi-stars me-1"></i>Experienced
                                    </button>
                                    <button type="button" class="btn btn-xs btn-outline-secondary btn-sm rounded-pill" onclick="setNoteTemplate('template2')">
                                        <i class="bi bi-briefcase me-1"></i>Professional
                                    </button>
                                    <button type="button" class="btn btn-xs btn-outline-secondary btn-sm rounded-pill" onclick="setNoteTemplate('template3')">
                                        <i class="bi bi-heart me-1"></i>Passionate
                                    </button>
                                    <button type="button" class="btn btn-xs btn-outline-secondary btn-sm rounded-pill" onclick="setNoteTemplate('template4')">
                                        <i class="bi bi-award me-1"></i>Certified
                                    </button>
                                </div>
                            </div>

                            <textarea name="note" id="reqNote" rows="4" class="form-control rounded-3"
                                placeholder="Briefly explain why you are a good fit for this course..." required minlength="20"></textarea>
                            <div class="form-text text-muted mt-1">
                                <i class="bi bi-info-circle me-1"></i>Describe your experience, teaching style, and why you want to teach this course.
                            </div>
                        </div>
                        <button type="submit" class="rc-req-btn py-2">
                            <i class="bi bi-send-fill me-2"></i>Submit Request
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
<script src="{{ asset('assets/js/beta-notice.js') }}"></script>
<script src="{{ asset('assets/js/teacher-dashboard.js') }}"></script>
<script>
function rcSwitchTab(id, btn) {
    document.getElementById('rc-tab-marketplace').style.display = id === 'marketplace' ? 'block' : 'none';
    document.getElementById('rc-tab-requests').style.display    = id === 'requests'    ? 'block' : 'none';
    document.querySelectorAll('.rc-tab-btn').forEach(b => b.classList.remove('active'));
    btn.classList.add('active');
}

document.querySelectorAll('.rc-filter').forEach(function(f) {
    f.addEventListener('click', function() {
        document.querySelectorAll('.rc-filter').forEach(x => x.classList.remove('active'));
        this.classList.add('active');
        var cat = this.dataset.cat;
        document.querySelectorAll('.rc-grid-item').forEach(function(item) {
            item.style.display = (cat === 'all' || item.dataset.cat == cat) ? '' : 'none';
        });
    });
});

// Dropdown filter function for mobile
function filterByCategory(cat) {
    document.querySelectorAll('.rc-grid-item').forEach(function(item) {
        item.style.display = (cat === 'all' || item.dataset.cat == cat) ? '' : 'none';
    });
}

function openReqModal(id, title, cat) {
    document.getElementById('rcCourseId').value = id;
    document.getElementById('rcModalTitle').textContent = title;
    document.getElementById('rcModalCat').textContent = cat;
    new bootstrap.Modal(document.getElementById('rcModal')).show();
}

function openEditModal(id, title, description) {
    const form = document.getElementById('editReqForm');
    const route = "{{ route('teacher.request-courses.update', ':id') }}";
    form.action = route.replace(':id', id);
    document.getElementById('editReqId').value = id;
    document.getElementById('editReqModalTitle').textContent = 'Edit: ' + title;
    document.getElementById('editReqDescription').value = description || '';
    new bootstrap.Modal(document.getElementById('editReqModal')).show();
}

function openCancelModal(id, title) {
    const form = document.getElementById('cancelReqForm');
    const route = "{{ route('teacher.request-courses.destroy', ':id') }}";
    form.action = route.replace(':id', id);
    document.getElementById('cancelReqTitle').textContent = title;
    new bootstrap.Modal(document.getElementById('cancelReqModal')).show();
}

function setNoteTemplate(template) {
    const templates = {
        template1: `I have extensive experience teaching this subject with a proven track record of student success. My teaching approach focuses on practical, hands-on learning that prepares students for real-world challenges. I'm excited to bring my expertise to this course and help students achieve their goals.`,

        template2: `As a professional in this field with several years of industry experience, I bring practical knowledge and current best practices to my teaching. I focus on bridging the gap between theory and practice, ensuring students develop skills that are immediately applicable in their careers.`,

        template3: `I'm passionate about this subject and have dedicated my career to mastering it. My enthusiasm for teaching comes from seeing students grow and succeed. I create an engaging learning environment where students feel motivated to explore and excel.`,

        template4: `I hold relevant certifications and have completed advanced training in this area. My structured approach to teaching ensures comprehensive coverage of all key concepts while adapting to different learning styles. I'm committed to delivering high-quality education that meets professional standards.`
    };

    const textarea = document.getElementById('reqNote');
    if (templates[template]) {
        textarea.value = templates[template];
    }
}
</script>
@endpush
