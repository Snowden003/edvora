// ==========================================================================
// Edvora Tech - Enrolled Students & Gamification Client Script
// Clean, Modern, 100% English Language Interface
// ==========================================================================

function getCsrfToken() {
    var meta = document.querySelector('meta[name="csrf-token"]');
    return meta ? meta.getAttribute('content') : '';
}

function escapeHtml(text) {
    if (!text && text !== 0) return '';
    return String(text)
        .replace(/&/g, '&amp;')
        .replace(/</g, '&lt;')
        .replace(/>/g, '&gt;')
        .replace(/"/g, '&quot;')
        .replace(/'/g, '&#039;');
}

function showToast(message, type) {
    var container = document.querySelector('.toast-container');
    if (!container) {
        container = document.createElement('div');
        container.className = 'toast-container position-fixed bottom-0 end-0 p-3';
        document.body.appendChild(container);
    }

    var bgClass = type === 'success' ? 'bg-success' : (type === 'danger' ? 'bg-danger' : 'bg-primary');
    var toast = document.createElement('div');
    toast.className = 'toast align-items-center text-white ' + bgClass + ' border-0 shadow-lg';
    toast.setAttribute('role', 'alert');
    toast.style.borderRadius = '12px';
    toast.innerHTML = '<div class="d-flex p-1"><div class="toast-body fw-semibold">' + message + '</div>' +
        '<button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast"></button></div>';

    container.appendChild(toast);
    new bootstrap.Toast(toast, { delay: 4000 }).show();

    toast.addEventListener('hidden.bs.toast', function () { toast.remove(); });
}

/**
 * Open Comprehensive Student Profile Modal
 */
function viewStudentProfile(userId) {
    var modalEl = document.getElementById('studentProfileModal');
    var modal = bootstrap.Modal.getInstance(modalEl) || new bootstrap.Modal(modalEl);
    var content = document.getElementById('studentProfileContent');
    
    content.innerHTML = '<div class="text-center py-5 my-4">' +
        '<div class="spinner-border text-primary" style="width: 3rem; height: 3rem;" role="status"></div>' +
        '<h6 class="fw-bold text-dark mt-3 mb-1">Loading Student Profile</h6>' +
        '<p class="text-muted small">Fetching comprehensive academic and personal records...</p>' +
    '</div>';
    
    modal.show();

    fetch('/teacher/students/' + userId + '/profile', {
        headers: {
            'Accept': 'application/json',
            'X-CSRF-TOKEN': getCsrfToken(),
        },
    })
    .then(function (res) {
        if (!res.ok) throw new Error('Failed to load profile');
        return res.json();
    })
    .then(function (d) {
        renderStudentProfileModal(d);
    })
    .catch(function (err) {
        console.error(err);
        content.innerHTML = '<div class="text-center py-5 my-4">' +
            '<i class="bi bi-exclamation-circle text-danger fs-1 d-block mb-2"></i>' +
            '<h5 class="fw-bold text-dark mb-1">Unable to Load Profile</h5>' +
            '<p class="text-muted small">Could not retrieve details for this student. Please check permissions.</p>' +
            '<button type="button" class="btn btn-sm btn-outline-primary rounded-pill px-3 mt-2" onclick="viewStudentProfile(' + userId + ')">' +
                '<i class="bi bi-arrow-clockwise me-1"></i>Try Again' +
            '</button>' +
        '</div>';
    });
}

/**
 * Render Full Student Profile Inside Modal
 */
function renderStudentProfileModal(d) {
    var content = document.getElementById('studentProfileContent');

    var valOrEmpty = function(val, suffix) {
        if (val === null || val === undefined || val === '') {
            return '<div class="profile-detail-val empty">Not Provided</div>';
        }
        return '<div class="profile-detail-val">' + escapeHtml(val) + (suffix || '') + '</div>';
    };

    // Skills Badges
    var skillsHtml = '';
    if (d.skills) {
        var skillsArr = Array.isArray(d.skills) ? d.skills : d.skills.split(',');
        skillsHtml = skillsArr.map(function(s) {
            return '<span class="badge bg-primary bg-opacity-10 text-primary border border-primary border-opacity-20 px-2 py-1 me-1 mb-1 rounded-pill">' + escapeHtml(s.trim()) + '</span>';
        }).join('');
    } else {
        skillsHtml = '<span class="text-muted small fst-italic">No skills recorded</span>';
    }

    // Languages Badges
    var langsHtml = '';
    if (d.languages) {
        var langsArr = Array.isArray(d.languages) ? d.languages : d.languages.split(',');
        langsHtml = langsArr.map(function(l) {
            return '<span class="badge bg-secondary bg-opacity-10 text-secondary border border-secondary border-opacity-20 px-2 py-1 me-1 mb-1 rounded-pill">' + escapeHtml(l.trim()) + '</span>';
        }).join('');
    } else {
        langsHtml = '<span class="text-muted small fst-italic">No languages recorded</span>';
    }

    // Courses List
    var coursesHtml = '';
    if (d.enrollments && d.enrollments.length > 0) {
        coursesHtml = '<div class="table-responsive rounded-3 border"><table class="table table-hover align-middle mb-0">' +
            '<thead class="table-light"><tr><th class="ps-3">Course Title</th><th>Enrolled On</th><th>Progress</th><th>Status</th><th class="text-end pe-3">Actions</th></tr></thead>' +
            '<tbody>';
        d.enrollments.forEach(function(c) {
            var prog = c.progress || 0;
            var progColor = c.status === 'banned' ? '#ef4444' : (prog >= 80 ? '#10b981' : (prog >= 40 ? '#f59e0b' : '#1F8FFF'));
            var statusBadge = c.status === 'active' ? '<span class="student-status-pill active"><i class="bi bi-circle-fill" style="font-size: 6px;"></i> Active</span>'
                : (c.status === 'completed' ? '<span class="student-status-pill completed"><i class="bi bi-circle-fill" style="font-size: 6px;"></i> Completed</span>'
                : '<span class="student-status-pill banned"><i class="bi bi-circle-fill" style="font-size: 6px;"></i> Banned</span>');

            var actionBtn = c.is_teacher_course
                ? '<a href="/teacher/courses/' + c.course_id + '" class="btn-student-action course" style="height: 32px; padding: 0 0.75rem; font-size: 0.76rem;" title="View Course Details"><i class="bi bi-journal-bookmark-fill me-1"></i>View Course Detail</a>'
                : '<span class="text-muted small fst-italic">Other Instructor</span>';

            coursesHtml += '<tr>' +
                '<td class="ps-3 fw-bold text-dark">' +
                    (c.is_teacher_course ? '<span class="badge bg-primary bg-opacity-15 text-primary me-2 px-2 py-1 rounded-pill">Your Course</span>' : '') +
                    escapeHtml(c.course_title) +
                '</td>' +
                '<td class="text-muted small">' + escapeHtml(c.enrolled_at) + '</td>' +
                '<td style="min-width: 140px;">' +
                    '<div class="d-flex justify-content-between mb-1"><small class="fw-bold" style="color:' + progColor + ';">' + prog + '%</small></div>' +
                    '<div class="clean-progress-track"><div class="clean-progress-fill" style="width:' + prog + '%; background:' + progColor + ';"></div></div>' +
                '</td>' +
                '<td>' + statusBadge + '</td>' +
                '<td class="text-end pe-3">' + actionBtn + '</td>' +
            '</tr>';
        });
        coursesHtml += '</tbody></table></div>';
    } else {
        coursesHtml = '<div class="text-center py-4 text-muted"><i class="bi bi-journal-x fs-2 d-block mb-1"></i>No enrolled courses found.</div>';
    }

    // Points History Rows
    var pointsRowsHtml = '';
    if (d.points_history && d.points_history.length > 0) {
        d.points_history.forEach(function(pt) {
            var isPos = pt.amount > 0;
            pointsRowsHtml += '<tr>' +
                '<td class="ps-3"><span class="badge ' + (isPos ? 'bg-success' : 'bg-danger') + ' px-2 py-1 rounded-pill fw-bold">' + (isPos ? '+' : '') + pt.amount + ' XP</span></td>' +
                '<td class="fw-semibold text-dark">' + escapeHtml(pt.reason) + '</td>' +
                '<td class="text-muted small">' + escapeHtml(pt.created_by) + '</td>' +
                '<td class="text-muted small text-end pe-3">' + escapeHtml(pt.date || pt.relative_date) + '</td>' +
            '</tr>';
        });
    } else {
        pointsRowsHtml = '<tr><td colspan="4" class="text-center text-muted py-4">No points activity recorded yet.</td></tr>';
    }

    // Teacher course options
    var courseOptionsHtml = '<option value="">General (Course Independent)</option>';
    if (d.teacher_courses && d.teacher_courses.length > 0) {
        d.teacher_courses.forEach(function(tc) {
            courseOptionsHtml += '<option value="' + tc.id + '">' + escapeHtml(tc.title) + '</option>';
        });
    }

    var html = '' +
    '<div>' +
        // Hero Header
        '<div class="modal-profile-hero">' +
            '<div class="row align-items-center g-3">' +
                '<div class="col-auto">' +
                    '<img src="' + d.avatar + '" alt="' + escapeHtml(d.name) + '" class="modal-hero-avatar">' +
                '</div>' +
                '<div class="col">' +
                    '<div class="d-flex align-items-center gap-2 flex-wrap mb-1">' +
                        '<h3 class="modal-hero-name mb-0">' + escapeHtml(d.name) + '</h3>' +
                        (d.nickname ? '<span class="badge bg-white bg-opacity-20 text-white rounded-pill px-2">"' + escapeHtml(d.nickname) + '"</span>' : '') +
                        (d.profile_complete ? '<span class="modal-hero-badge verified"><i class="bi bi-check-circle-fill me-1"></i>Verified Profile</span>' : '<span class="modal-hero-badge incomplete"><i class="bi bi-clock-history me-1"></i>Profile Incomplete</span>') +
                    '</div>' +
                    '<div class="d-flex gap-3 flex-wrap text-white-50 small mt-1">' +
                        '<span><i class="bi bi-envelope me-1"></i>' + escapeHtml(d.email || 'No email') + '</span>' +
                        (d.phone ? '<span><i class="bi bi-telephone me-1"></i>' + escapeHtml(d.phone) + '</span>' : '') +
                        '<span><i class="bi bi-calendar3 me-1"></i>Joined ' + escapeHtml(d.joined_at) + '</span>' +
                        '<span><i class="bi bi-mortarboard me-1"></i>' + d.enrolled_courses_count + ' Courses Enrolled</span>' +
                    '</div>' +
                '</div>' +
                '<div class="col-auto text-end">' +
                    '<div class="modal-hero-score-card">' +
                        '<div class="modal-hero-score-val" id="modal-score-val">' + d.total_score + '</div>' +
                        '<div class="modal-hero-score-lbl">Total Score (XP)</div>' +
                    '</div>' +
                '</div>' +
            '</div>' +
        '</div>' +

        // Modal Body Content with Padding
        '<div class="p-4">' +
            // Segmented Navigation Tab Bar
            '<div class="modal-nav-pill-bar" id="profileModalTabs">' +
                '<button type="button" class="modal-nav-tab-btn active" onclick="switchProfileModalTab(\'tab-score\')">' +
                    '<i class="bi bi-star-fill text-warning"></i> <span>Score & Points</span>' +
                '</button>' +
                '<button type="button" class="modal-nav-tab-btn" onclick="switchProfileModalTab(\'tab-personal\')">' +
                    '<i class="bi bi-person-badge"></i> <span>Personal Details</span>' +
                '</button>' +
                '<button type="button" class="modal-nav-tab-btn" onclick="switchProfileModalTab(\'tab-contact\')">' +
                    '<i class="bi bi-geo-alt"></i> <span>Contact & Address</span>' +
                '</button>' +
                '<button type="button" class="modal-nav-tab-btn" onclick="switchProfileModalTab(\'tab-academic\')">' +
                    '<i class="bi bi-mortarboard"></i> <span>Education & Skills</span>' +
                '</button>' +
                '<button type="button" class="modal-nav-tab-btn" onclick="switchProfileModalTab(\'tab-courses\')">' +
                    '<i class="bi bi-journal-bookmark"></i> <span>Enrolled Courses (' + d.enrolled_courses_count + ')</span>' +
                '</button>' +
                '<button type="button" class="modal-nav-tab-btn" onclick="switchProfileModalTab(\'tab-emergency\')">' +
                    '<i class="bi bi-telephone-plus"></i> <span>Emergency Contact</span>' +
                '</button>' +
            '</div>' +

            // TAB 1: Score & Points
            '<div id="modal-pane-tab-score" class="modal-tab-pane">' +
                // 3 Score Summary Metric Cards
                '<div class="row g-3 mb-4">' +
                    '<div class="col-md-4">' +
                        '<div class="modal-points-summary-box total">' +
                            '<div class="summary-val" id="modal-total-score-box">' + d.total_score + '</div>' +
                            '<div class="summary-lbl">Current Total Score</div>' +
                        '</div>' +
                    '</div>' +
                    '<div class="col-md-4">' +
                        '<div class="modal-points-summary-box earned">' +
                            '<div class="summary-val">+' + d.earned_points + '</div>' +
                            '<div class="summary-lbl">Total Earned (+)</div>' +
                        '</div>' +
                    '</div>' +
                    '<div class="col-md-4">' +
                        '<div class="modal-points-summary-box deducted">' +
                            '<div class="summary-val">-' + d.deducted_points + '</div>' +
                            '<div class="summary-lbl">Total Deducted (-)</div>' +
                        '</div>' +
                    '</div>' +
                '</div>' +

                // Interactive Adjustment Card
                '<div class="interactive-adjustment-card">' +
                    '<div class="d-flex justify-content-between align-items-center mb-3 flex-wrap gap-2">' +
                        '<div>' +
                            '<h6 class="fw-bold mb-0 text-dark"><i class="bi bi-sliders me-2 text-primary"></i>Adjust Student Points</h6>' +
                            '<small class="text-muted">Award bonus points for achievements or deduct for infractions</small>' +
                        '</div>' +
                        '<div class="mode-segmented-toggle mb-0">' +
                            '<button type="button" class="mode-toggle-btn active add" id="inlineModeAdd" onclick="setInlineAdjustmentMode(\'add\')">' +
                                '<i class="bi bi-plus-lg me-1"></i>Award Points (+)' +
                            '</button>' +
                            '<button type="button" class="mode-toggle-btn deduct" id="inlineModeDeduct" onclick="setInlineAdjustmentMode(\'deduct\')">' +
                                '<i class="bi bi-dash-lg me-1"></i>Deduct Points (-)' +
                            '</button>' +
                        '</div>' +
                    '</div>' +

                    '<div class="row g-3 align-items-end">' +
                        '<div class="col-md-4">' +
                            '<label class="form-label small fw-bold text-muted mb-1">Points Amount (XP)</label>' +
                            '<div class="input-group">' +
                                '<input type="number" id="inlinePointAmount" class="form-control fw-bold fs-5" value="10" min="1">' +
                                '<span class="input-group-text bg-light fw-bold text-muted">XP</span>' +
                            '</div>' +
                            '<div class="d-flex gap-1 mt-2 flex-wrap" id="inlinePresetChips">' +
                                '<button type="button" class="chip-preset-btn" onclick="setInlineAmount(5)">5</button>' +
                                '<button type="button" class="chip-preset-btn" onclick="setInlineAmount(10)">10</button>' +
                                '<button type="button" class="chip-preset-btn" onclick="setInlineAmount(25)">25</button>' +
                                '<button type="button" class="chip-preset-btn" onclick="setInlineAmount(50)">50</button>' +
                                '<button type="button" class="chip-preset-btn" onclick="setInlineAmount(100)">100</button>' +
                            '</div>' +
                        '</div>' +
                        '<div class="col-md-5">' +
                            '<label class="form-label small fw-bold text-muted mb-1">Reason / Activity Description</label>' +
                            '<input type="text" id="inlinePointReason" class="form-control" placeholder="e.g., Active participation in class discussion">' +
                            '<div class="d-flex gap-1 mt-2 flex-wrap">' +
                                '<button type="button" class="badge border bg-white text-muted" onclick="document.getElementById(\'inlinePointReason\').value = \'Active class participation\'">Participation</button>' +
                                '<button type="button" class="badge border bg-white text-muted" onclick="document.getElementById(\'inlinePointReason\').value = \'Excellent homework submission\'">Homework</button>' +
                                '<button type="button" class="badge border bg-white text-muted" onclick="document.getElementById(\'inlinePointReason\').value = \'Quiz performance bonus\'">Quiz</button>' +
                                '<button type="button" class="badge border bg-white text-muted" onclick="document.getElementById(\'inlinePointReason\').value = \'Missed deadline or infraction\'">Infraction</button>' +
                            '</div>' +
                        '</div>' +
                        '<div class="col-md-3">' +
                            '<button type="button" class="btn btn-primary w-100 py-2 fw-bold rounded-3" id="inlinePointSubmitBtn" onclick="submitInlinePointAdjustment(' + d.id + ')">' +
                                '<i class="bi bi-check2-circle me-1"></i>Apply Adjustment' +
                            '</button>' +
                        '</div>' +
                    '</div>' +
                '</div>' +

                // Points History Table
                '<div class="d-flex justify-content-between align-items-center mb-2">' +
                    '<h6 class="fw-bold mb-0 text-dark"><i class="bi bi-clock-history me-2 text-primary"></i>Points Adjustment Log</h6>' +
                    '<small class="text-muted">Recent scoring activity</small>' +
                '</div>' +
                '<div class="table-responsive rounded-3 border">' +
                    '<table class="table table-hover align-middle mb-0" id="modalPointsHistoryTable">' +
                        '<thead class="table-light"><tr><th class="ps-3">XP Amount</th><th>Reason</th><th>Awarded By</th><th class="text-end pe-3">Date</th></tr></thead>' +
                        '<tbody>' + pointsRowsHtml + '</tbody>' +
                    '</table>' +
                '</div>' +
            '</div>' +

            // TAB 2: Personal Details
            '<div id="modal-pane-tab-personal" class="modal-tab-pane d-none">' +
                '<div class="row g-3">' +
                    '<div class="col-md-4"><div class="profile-detail-card"><div class="profile-detail-label"><i class="bi bi-person"></i> First Name</div>' + valOrEmpty(d.first_name) + '</div></div>' +
                    '<div class="col-md-4"><div class="profile-detail-card"><div class="profile-detail-label"><i class="bi bi-person"></i> Last Name</div>' + valOrEmpty(d.last_name) + '</div></div>' +
                    '<div class="col-md-4"><div class="profile-detail-card"><div class="profile-detail-label"><i class="bi bi-tag"></i> Nickname</div>' + valOrEmpty(d.nickname) + '</div></div>' +
                    '<div class="col-md-4"><div class="profile-detail-card"><div class="profile-detail-label"><i class="bi bi-people"></i> Father\'s Name</div>' + valOrEmpty(d.father_name) + '</div></div>' +
                    '<div class="col-md-4"><div class="profile-detail-card"><div class="profile-detail-label"><i class="bi bi-people"></i> Mother\'s Name</div>' + valOrEmpty(d.mother_name) + '</div></div>' +
                    '<div class="col-md-4"><div class="profile-detail-card"><div class="profile-detail-label"><i class="bi bi-gender-ambiguous"></i> Gender</div>' + valOrEmpty(d.gender) + '</div></div>' +
                    '<div class="col-md-4"><div class="profile-detail-card"><div class="profile-detail-label"><i class="bi bi-calendar-event"></i> Date of Birth</div>' + valOrEmpty(d.date_of_birth) + '</div></div>' +
                    '<div class="col-md-4"><div class="profile-detail-card"><div class="profile-detail-label"><i class="bi bi-card-text"></i> National ID (Tazkira)</div>' + valOrEmpty(d.national_id) + '</div></div>' +
                    '<div class="col-md-4"><div class="profile-detail-card"><div class="profile-detail-label"><i class="bi bi-journal-text"></i> Passport Number</div>' + valOrEmpty(d.passport_number) + '</div></div>' +
                    '<div class="col-md-4"><div class="profile-detail-card"><div class="profile-detail-label"><i class="bi bi-heart"></i> Marital Status</div>' + valOrEmpty(d.marital_status) + '</div></div>' +
                    '<div class="col-md-4"><div class="profile-detail-card"><div class="profile-detail-label"><i class="bi bi-droplet"></i> Blood Type</div>' + valOrEmpty(d.blood_type) + '</div></div>' +
                    '<div class="col-md-4"><div class="profile-detail-card"><div class="profile-detail-label"><i class="bi bi-shield-check"></i> Account Status</div>' + valOrEmpty(d.status) + '</div></div>' +
                '</div>' +
            '</div>' +

            // TAB 3: Contact & Address
            '<div id="modal-pane-tab-contact" class="modal-tab-pane d-none">' +
                '<div class="row g-3">' +
                    '<div class="col-md-6"><div class="profile-detail-card"><div class="profile-detail-label"><i class="bi bi-envelope"></i> Primary Email</div>' + valOrEmpty(d.email) + '</div></div>' +
                    '<div class="col-md-6"><div class="profile-detail-card"><div class="profile-detail-label"><i class="bi bi-telephone"></i> Phone Number</div>' + valOrEmpty(d.phone) + '</div></div>' +
                    '<div class="col-md-6"><div class="profile-detail-card"><div class="profile-detail-label"><i class="bi bi-whatsapp"></i> WhatsApp Number</div>' + valOrEmpty(d.whatsapp_number) + '</div></div>' +
                    '<div class="col-md-6"><div class="profile-detail-card"><div class="profile-detail-label"><i class="bi bi-mailbox"></i> Postal Code</div>' + valOrEmpty(d.postal_code) + '</div></div>' +
                    '<div class="col-md-6"><div class="profile-detail-card"><div class="profile-detail-label"><i class="bi bi-geo"></i> Province</div>' + valOrEmpty(d.province) + '</div></div>' +
                    '<div class="col-md-6"><div class="profile-detail-card"><div class="profile-detail-label"><i class="bi bi-compass"></i> District</div>' + valOrEmpty(d.district) + '</div></div>' +
                    '<div class="col-12"><div class="profile-detail-card"><div class="profile-detail-label"><i class="bi bi-geo-alt"></i> Current Residential Address</div>' + valOrEmpty(d.current_address) + '</div></div>' +
                    '<div class="col-12"><div class="profile-detail-card"><div class="profile-detail-label"><i class="bi bi-house"></i> Permanent / Origin Address</div>' + valOrEmpty(d.permanent_address) + '</div></div>' +
                '</div>' +
            '</div>' +

            // TAB 4: Academic & Skills
            '<div id="modal-pane-tab-academic" class="modal-tab-pane d-none">' +
                '<div class="row g-3">' +
                    '<div class="col-md-4"><div class="profile-detail-card"><div class="profile-detail-label"><i class="bi bi-award"></i> Education Level</div>' + valOrEmpty(d.education_level) + '</div></div>' +
                    '<div class="col-md-4"><div class="profile-detail-card"><div class="profile-detail-label"><i class="bi bi-building"></i> Last School Name</div>' + valOrEmpty(d.school_name) + '</div></div>' +
                    '<div class="col-md-4"><div class="profile-detail-card"><div class="profile-detail-label"><i class="bi bi-bank"></i> University Name</div>' + valOrEmpty(d.university_name) + '</div></div>' +
                    '<div class="col-md-4"><div class="profile-detail-card"><div class="profile-detail-label"><i class="bi bi-book"></i> Field of Study</div>' + valOrEmpty(d.field_of_study) + '</div></div>' +
                    '<div class="col-md-4"><div class="profile-detail-card"><div class="profile-detail-label"><i class="bi bi-calendar-check"></i> Graduation Year</div>' + valOrEmpty(d.graduation_year) + '</div></div>' +
                    '<div class="col-md-4"><div class="profile-detail-card"><div class="profile-detail-label"><i class="bi bi-graph-up"></i> GPA / Grade</div>' + valOrEmpty(d.gpa) + '</div></div>' +
                    '<div class="col-12"><div class="profile-detail-card"><div class="profile-detail-label"><i class="bi bi-patch-check"></i> Other Certifications</div>' + valOrEmpty(d.other_certifications) + '</div></div>' +
                    '<div class="col-md-6"><div class="profile-detail-card"><div class="profile-detail-label"><i class="bi bi-tools"></i> Skills</div>' + skillsHtml + '</div></div>' +
                    '<div class="col-md-6"><div class="profile-detail-card"><div class="profile-detail-label"><i class="bi bi-translate"></i> Languages</div>' + langsHtml + '</div></div>' +
                    '<div class="col-12"><div class="profile-detail-card"><div class="profile-detail-label"><i class="bi bi-chat-quote"></i> Bio & Self Introduction</div>' + valOrEmpty(d.about_me || d.bio) + '</div></div>' +
                '</div>' +
            '</div>' +

            // TAB 5: Enrolled Courses
            '<div id="modal-pane-tab-courses" class="modal-tab-pane d-none">' +
                coursesHtml +
            '</div>' +

            // TAB 6: Emergency Contact
            '<div id="modal-pane-tab-emergency" class="modal-tab-pane d-none">' +
                '<div class="row g-3">' +
                    '<div class="col-md-4"><div class="profile-detail-card"><div class="profile-detail-label"><i class="bi bi-person-fill"></i> Contact Person</div>' + valOrEmpty(d.emergency_contact_name) + '</div></div>' +
                    '<div class="col-md-4"><div class="profile-detail-card"><div class="profile-detail-label"><i class="bi bi-people-fill"></i> Relationship</div>' + valOrEmpty(d.emergency_contact_relation) + '</div></div>' +
                    '<div class="col-md-4"><div class="profile-detail-card"><div class="profile-detail-label"><i class="bi bi-telephone-fill"></i> Emergency Phone</div>' +
                        (d.emergency_contact_phone ? '<div class="profile-detail-val"><a href="tel:' + escapeHtml(d.emergency_contact_phone) + '" class="text-decoration-none text-primary"><i class="bi bi-telephone-outbound me-1"></i>' + escapeHtml(d.emergency_contact_phone) + '</a></div>' : '<div class="profile-detail-val empty">Not Provided</div>') +
                    '</div></div>' +
                '</div>' +
            '</div>' +
        '</div>' +
    '</div>';

    content.innerHTML = html;
}

/**
 * Switch tabs inside the profile modal
 */
function switchProfileModalTab(tabId) {
    document.querySelectorAll('#profileModalTabs .modal-nav-tab-btn').forEach(function(btn) {
        btn.classList.remove('active');
    });
    event.currentTarget.classList.add('active');

    document.querySelectorAll('.modal-tab-pane').forEach(function(pane) {
        pane.classList.add('d-none');
    });
    var targetPane = document.getElementById('modal-pane-' + tabId);
    if (targetPane) {
        targetPane.classList.remove('d-none');
    }
}

/**
 * Handle Add / Deduct Segmented Switch inside Profile Modal
 */
var currentInlineMode = 'add';

function setInlineAdjustmentMode(mode) {
    currentInlineMode = mode;
    var addBtn = document.getElementById('inlineModeAdd');
    var deductBtn = document.getElementById('inlineModeDeduct');
    var amountInput = document.getElementById('inlinePointAmount');

    if (mode === 'add') {
        addBtn.className = 'mode-toggle-btn active add';
        deductBtn.className = 'mode-toggle-btn deduct';
        if (amountInput.value < 0) amountInput.value = Math.abs(amountInput.value);
    } else {
        addBtn.className = 'mode-toggle-btn add';
        deductBtn.className = 'mode-toggle-btn active deduct';
        if (amountInput.value > 0) amountInput.value = -Math.abs(amountInput.value);
    }
}

function setInlineAmount(val) {
    var amountInput = document.getElementById('inlinePointAmount');
    if (currentInlineMode === 'deduct') {
        amountInput.value = -Math.abs(val);
    } else {
        amountInput.value = Math.abs(val);
    }
}

/**
 * Open Quick Points Modal
 */
var quickPointsCurrentMode = 'add';
var quickPointsBaseScore = 0;

function openPointsModal(userId, userName, currentScore, courseId) {
    document.getElementById('quickPointsUserId').value = userId;
    document.getElementById('quickPointsStudentName').textContent = userName;
    quickPointsBaseScore = parseInt(currentScore, 10) || 0;
    
    var baseScoreEl = document.getElementById('quickPointsCurrentScore');
    if (baseScoreEl) baseScoreEl.textContent = quickPointsBaseScore;

    var amountInput = document.getElementById('quickPointsAmount');
    if (amountInput) amountInput.value = 10;

    var reasonInput = document.getElementById('quickPointsReason');
    if (reasonInput) reasonInput.value = '';
    
    setQuickPointsMode('add');

    var courseSelect = document.getElementById('quickPointsCourseId');
    if (courseSelect && courseId) {
        courseSelect.value = courseId;
    }

    updateQuickPointsLivePreview();

    var modal = new bootstrap.Modal(document.getElementById('quickPointsModal'));
    modal.show();
}

function setQuickPointsMode(mode) {
    quickPointsCurrentMode = mode;
    var addBtn = document.getElementById('quickModeAdd');
    var deductBtn = document.getElementById('quickModeDeduct');
    var amountInput = document.getElementById('quickPointsAmount');
    var submitBtn = document.getElementById('quickPointsSubmitBtn');

    if (mode === 'add') {
        if (addBtn) addBtn.className = 'enr-pm-mode active add';
        if (deductBtn) deductBtn.className = 'enr-pm-mode';
        if (amountInput && amountInput.value < 0) amountInput.value = Math.abs(amountInput.value);
        if (submitBtn) {
            submitBtn.className = 'enr-pm-submit';
            submitBtn.innerHTML = '<i class="bi bi-plus-circle"></i> Award Points';
        }
    } else {
        if (addBtn) addBtn.className = 'enr-pm-mode';
        if (deductBtn) deductBtn.className = 'enr-pm-mode active deduct';
        if (amountInput && amountInput.value > 0) amountInput.value = -Math.abs(amountInput.value);
        if (submitBtn) {
            submitBtn.className = 'enr-pm-submit deduct-mode';
            submitBtn.innerHTML = '<i class="bi bi-dash-circle"></i> Deduct Points';
        }
    }

    updateQuickPointsLivePreview();
}

function setQuickAmount(val) {
    var amountInput = document.getElementById('quickPointsAmount');
    if (!amountInput) return;

    if (quickPointsCurrentMode === 'deduct') {
        amountInput.value = -Math.abs(val);
    } else {
        amountInput.value = Math.abs(val);
    }

    // Highlight active preset chip
    document.querySelectorAll('.enr-pm-chip').forEach(function(chip) {
        chip.classList.remove('active');
    });
    if (event && event.currentTarget) {
        event.currentTarget.classList.add('active');
    }

    updateQuickPointsLivePreview();
}

function updateQuickPointsLivePreview() {
    var amountInput = document.getElementById('quickPointsAmount');
    var deltaEl = document.getElementById('quickPreviewDelta');
    var newScoreEl = document.getElementById('quickPreviewNewScore');

    if (!amountInput || !deltaEl || !newScoreEl) return;

    var delta = parseInt(amountInput.value, 10) || 0;
    var isAdd = delta >= 0;

    deltaEl.className = 'enr-pm-delta ' + (isAdd ? 'add' : 'deduct');
    deltaEl.textContent = (isAdd ? '+' : '') + delta;

    var projected = quickPointsBaseScore + delta;
    newScoreEl.textContent = projected + ' XP';
    newScoreEl.style.color = isAdd ? '#059669' : '#dc2626';
}

function setQuickReason(text) {
    var reasonInput = document.getElementById('quickPointsReason');
    if (reasonInput) {
        reasonInput.value = text;
        reasonInput.focus();
    }
}

/**
 * Submit Quick Points Adjustment Modal
 */
function submitQuickPointsAdjust() {
    var userId = document.getElementById('quickPointsUserId').value;
    var amount = parseInt(document.getElementById('quickPointsAmount').value, 10);
    var reason = document.getElementById('quickPointsReason').value.trim();
    var courseId = document.getElementById('quickPointsCourseId') ? document.getElementById('quickPointsCourseId').value : null;

    if (isNaN(amount) || amount === 0) {
        showToast('Please enter a valid points amount.', 'danger');
        return;
    }

    if (!reason) {
        showToast('Please provide a reason for the adjustment.', 'danger');
        return;
    }

    var btn = document.getElementById('quickPointsSubmitBtn');
    var originalBtnText = btn.innerHTML;
    btn.disabled = true;
    btn.innerHTML = '<span class="spinner-border spinner-border-sm me-1"></span>Saving...';

    fetch('/teacher/students/' + userId + '/points/adjust', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'Accept': 'application/json',
            'X-CSRF-TOKEN': getCsrfToken(),
        },
        body: JSON.stringify({
            amount: amount,
            reason: reason,
            course_id: courseId,
        }),
    })
    .then(function (res) { return res.json(); })
    .then(function (data) {
        btn.disabled = false;
        btn.innerHTML = originalBtnText;

        if (data.status === 'success') {
            showToast(data.message, 'success');
            updateScoreInDOM(userId, data.new_score);

            var modal = bootstrap.Modal.getInstance(document.getElementById('quickPointsModal'));
            if (modal) modal.hide();
        } else {
            showToast(data.message || 'Failed to adjust points.', 'danger');
        }
    })
    .catch(function () {
        btn.disabled = false;
        btn.innerHTML = originalBtnText;
        showToast('An error occurred while communicating with server.', 'danger');
    });
}

/**
 * Quick Single-Click Point Adjustment (+5 / -5)
 */
function quickAdjustPoints(userId, userName, amount, courseId) {
    var actionText = amount > 0 ? ('Award +' + amount + ' points to') : ('Deduct ' + Math.abs(amount) + ' points from');
    var promptReason = prompt(actionText + ' ' + userName + '?\nEnter optional note:', amount > 0 ? 'Good classroom performance' : 'Class disruption or missed deadline');
    if (promptReason === null) return; // Cancelled

    var reason = promptReason.trim() || (amount > 0 ? 'Instructor awarded points' : 'Instructor deducted points');

    fetch('/teacher/students/' + userId + '/points/adjust', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'Accept': 'application/json',
            'X-CSRF-TOKEN': getCsrfToken(),
        },
        body: JSON.stringify({
            amount: amount,
            reason: reason,
            course_id: courseId,
        }),
    })
    .then(function (res) { return res.json(); })
    .then(function (data) {
        if (data.status === 'success') {
            showToast(data.message, 'success');
            updateScoreInDOM(userId, data.new_score);
        } else {
            showToast(data.message || 'Failed to adjust points.', 'danger');
        }
    })
    .catch(function () {
        showToast('An unexpected error occurred.', 'danger');
    });
}

/**
 * Submit Inline Point Adjustment inside Profile Modal
 */
function submitInlinePointAdjustment(userId) {
    var amountInput = document.getElementById('inlinePointAmount');
    var reasonInput = document.getElementById('inlinePointReason');
    var amount = parseInt(amountInput.value, 10);
    var reason = reasonInput.value.trim();

    if (isNaN(amount) || amount === 0) {
        showToast('Please enter a non-zero points amount.', 'danger');
        return;
    }

    if (!reason) {
        showToast('Please provide a reason for the adjustment.', 'danger');
        return;
    }

    var btn = document.getElementById('inlinePointSubmitBtn');
    var origText = btn.innerHTML;
    btn.disabled = true;
    btn.innerHTML = '<span class="spinner-border spinner-border-sm me-1"></span>Saving...';

    fetch('/teacher/students/' + userId + '/points/adjust', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'Accept': 'application/json',
            'X-CSRF-TOKEN': getCsrfToken(),
        },
        body: JSON.stringify({
            amount: amount,
            reason: reason,
        }),
    })
    .then(function (res) { return res.json(); })
    .then(function (data) {
        btn.disabled = false;
        btn.innerHTML = origText;

        if (data.status === 'success') {
            showToast(data.message, 'success');
            reasonInput.value = '';

            // Update scores in DOM
            updateScoreInDOM(userId, data.new_score);

            // Update modal headers & stat boxes
            var modalScoreVal = document.getElementById('modal-score-val');
            if (modalScoreVal) modalScoreVal.textContent = data.new_score;

            var modalTotalScoreBox = document.getElementById('modal-total-score-box');
            if (modalTotalScoreBox) modalTotalScoreBox.textContent = data.new_score;

            // Prepend new row to table
            var tbody = document.querySelector('#modalPointsHistoryTable tbody');
            if (tbody) {
                var isPos = data.point.amount > 0;
                var tr = document.createElement('tr');
                tr.innerHTML = '<td class="ps-3"><span class="badge ' + (isPos ? 'bg-success' : 'bg-danger') + ' px-2 py-1 rounded-pill fw-bold">' + (isPos ? '+' : '') + data.point.amount + ' XP</span></td>' +
                    '<td class="fw-semibold text-dark">' + escapeHtml(data.point.reason) + '</td>' +
                    '<td class="text-muted small">' + escapeHtml(data.point.created_by) + '</td>' +
                    '<td class="text-muted small text-end pe-3">' + escapeHtml(data.point.relative_date || 'Just now') + '</td>';
                tbody.insertBefore(tr, tbody.firstChild);
            }
        } else {
            showToast(data.message || 'Failed to adjust points.', 'danger');
        }
    })
    .catch(function () {
        btn.disabled = false;
        btn.innerHTML = origText;
        showToast('An unexpected error occurred.', 'danger');
    });
}

/**
 * Update Score Displays in DOM
 */
function updateScoreInDOM(userId, newScore) {
    document.querySelectorAll('.student-score-display[data-user-id="' + userId + '"]').forEach(function(el) {
        el.textContent = newScore;
    });

    var badge = document.getElementById('score-badge-' + userId);
    if (badge) {
        badge.textContent = newScore;
    }
}
