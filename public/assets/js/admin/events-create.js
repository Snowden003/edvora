function previewImage(input) {
    const preview = document.getElementById('imagePreview');
    if (input.files && input.files[0]) {
        const reader = new FileReader();
        reader.onload = function(e) {
            preview.innerHTML = '<img src="' + e.target.result + '" alt="Preview">';
        }
        reader.readAsDataURL(input.files[0]);
    } else {
        preview.innerHTML = '<span><i class="bi bi-image me-2"></i>Image Preview</span>';
    }
}

function toggleLocationFields() {
    const eventMode = document.getElementById('event_mode').value;
    const googleMapsContainer = document.getElementById('google_maps_container');
    const googleMapsInput = document.getElementById('google_maps_url');

    if (eventMode === 'in-person') {
        googleMapsContainer.style.display = 'block';
        googleMapsInput.required = true;
    } else {
        googleMapsContainer.style.display = 'none';
        googleMapsInput.required = false;
        googleMapsInput.value = '';
    }
}

function togglePresenterType() {
    const existingRadio = document.getElementById('presenter_existing');
    const customRadio = document.getElementById('presenter_custom');
    const existingContainer = document.getElementById('existing_presenter_container');
    const customContainer = document.getElementById('custom_presenter_container');

    if (existingRadio.checked) {
        existingContainer.style.display = 'block';
        customContainer.style.display = 'none';
    } else if (customRadio.checked) {
        existingContainer.style.display = 'none';
        customContainer.style.display = 'block';
    }
}

function showTeacherInfo() {
    const select = document.getElementById('presenter_id');
    const selectedOption = select.options[select.selectedIndex];
    const infoCard = document.getElementById('teacher_info_card');

    if (select.value && selectedOption) {
        // Get teacher name from data-name attribute
        const teacherName = selectedOption.getAttribute('data-name') || 'Teacher Name';
        const specialization = selectedOption.getAttribute('data-specialization') || 'General';
        const experience = selectedOption.getAttribute('data-experience') || 'N/A';

        document.getElementById('teacher_name').textContent = teacherName;
        document.getElementById('teacher_specialization').textContent = specialization;
        document.getElementById('teacher_experience').textContent = experience;
        infoCard.classList.remove('d-none');
    } else {
        infoCard.classList.add('d-none');
    }
}

function toggleInvitationFields() {
    const cardType = document.getElementById('invitation_card_type').value;
    const textContainer = document.getElementById('invitation_text_container');
    const fileContainer = document.getElementById('invitation_file_container');

    // Hide all first
    textContainer.style.display = 'none';
    fileContainer.style.display = 'none';

    if (cardType === 'text') {
        textContainer.style.display = 'block';
    } else if (cardType === 'image') {
        fileContainer.style.display = 'block';
    }
    // auto_pdf requires no upload - system generates it automatically
}

function toggleEventTypeFields() {
    const eventType = document.getElementById('type').value;
    const workshopSection = document.getElementById('workshop_section');

    if (eventType === 'workshop') {
        workshopSection.style.display = 'block';
    } else {
        workshopSection.style.display = 'none';
    }
}

function toggleInstructorType() {
    const existingContainer = document.getElementById('existing_instructor_container');
    const customContainer = document.getElementById('custom_instructor_container');
    const customBioContainer = document.getElementById('custom_instructor_bio_container');
    const existingRadio = document.getElementById('instructor_existing');
    const customRadio = document.getElementById('instructor_custom');

    if (existingRadio.checked) {
        existingContainer.style.display = 'block';
        customContainer.style.display = 'none';
        customBioContainer.style.display = 'none';
    } else if (customRadio.checked) {
        existingContainer.style.display = 'none';
        customContainer.style.display = 'block';
        customBioContainer.style.display = 'block';
    }
}

let presenterCount = 0;
function addCustomPresenter() {
    const container = document.getElementById('custom_presenters_container');
    const row = document.createElement('div');
    row.className = 'presenter-row mb-4 p-4 border rounded';
    row.style.background = 'linear-gradient(135deg, #f8f9fa 0%, #ffffff 100%)';
    const currentCount = presenterCount;
    row.innerHTML = `
        <div class="d-flex justify-content-between align-items-center mb-3">
            <h6 class="fw-bold mb-0" style="color: #1F8FFF;">
                <i class="bi bi-person-plus me-2"></i>Guest Speaker #${presenterCount + 1}
            </h6>
            <button type="button" class="btn btn-outline-danger btn-sm" onclick="this.closest('.presenter-row').remove()">
                <i class="bi bi-trash"></i> Remove
            </button>
        </div>
        <div class="row g-3">
            <div class="col-md-6">
                <label class="form-label small fw-bold">
                    <i class="bi bi-person me-1 text-primary"></i>Full Name *
                </label>
                <input type="text" class="form-control" name="presenters[custom][${presenterCount}][name]" placeholder="e.g., Dr. John Smith" required>
            </div>
            <div class="col-md-6">
                <label class="form-label small fw-bold">
                    <i class="bi bi-envelope me-1 text-primary"></i>Email Address *
                </label>
                <input type="email" class="form-control" name="presenters[custom][${presenterCount}][email]" placeholder="speaker@example.com" required>
                <div class="form-text small">Required for event notification</div>
            </div>
            <div class="col-md-4">
                <label class="form-label small fw-bold">
                    <i class="bi bi-briefcase me-1 text-primary"></i>Field of Expertise
                </label>
                <select class="form-select" name="presenters[custom][${presenterCount}][expertise]">
                    <option value="">Select Expertise</option>
                    <option value="Cyber Security">Cyber Security</option>
                    <option value="Web Development">Web Development</option>
                    <option value="AI & Machine Learning">AI & Machine Learning</option>
                    <option value="Data Science">Data Science</option>
                    <option value="Cloud Computing">Cloud Computing</option>
                    <option value="DevOps">DevOps</option>
                    <option value="Mobile Development">Mobile Development</option>
                    <option value="Blockchain">Blockchain</option>
                    <option value="Networking">Networking</option>
                    <option value="Database Management">Database Management</option>
                    <option value="UI/UX Design">UI/UX Design</option>
                    <option value="Software Engineering">Software Engineering</option>
                    <option value="System Administration">System Administration</option>
                    <option value="Project Management">Project Management</option>
                    <option value="Other">Other</option>
                </select>
            </div>
            <div class="col-md-4">
                <label class="form-label small fw-bold">
                    <i class="bi bi-clock-history me-1 text-primary"></i>Years of Experience
                </label>
                <input type="number" class="form-control" name="presenters[custom][${presenterCount}][experience]" placeholder="e.g., 10">
            </div>
            <div class="col-md-4">
                <label class="form-label small fw-bold">
                    <i class="bi bi-image me-1 text-primary"></i>Profile Photo
                </label>
                <input type="file" class="form-control" name="presenters[custom][${presenterCount}][photo]" accept="image/*" onchange="previewPresenterPhoto(this, ${currentCount})">
            </div>
            <div class="col-12">
                <div class="photo-preview mb-3" id="presenter-photo-preview-${currentCount}" style="display: none;">
                    <img src="" alt="Presenter Photo Preview" class="img-fluid rounded" style="max-height: 150px; border: 2px solid #1F8FFF;">
                </div>
            </div>
            <div class="col-12">
                <label class="form-label small fw-bold">
                    <i class="bi bi-file-text me-1 text-primary"></i>Biography / Background
                </label>
                <textarea class="form-control" name="presenters[custom][${presenterCount}][bio]" rows="2" placeholder="Brief background, education, achievements..."></textarea>
            </div>
        </div>
    `;
    container.appendChild(row);
    presenterCount++;
}

function previewPresenterPhoto(input, index) {
    const previewContainer = document.getElementById(`presenter-photo-preview-${index}`);
    const previewImg = previewContainer.querySelector('img');
    if (input.files && input.files[0]) {
        const reader = new FileReader();
        reader.onload = function(e) {
            previewImg.src = e.target.result;
            previewContainer.style.display = 'block';
        }
        reader.readAsDataURL(input.files[0]);
    } else {
        previewContainer.style.display = 'none';
        previewImg.src = '';
    }
}

let scheduleCount = 0;
function addScheduleRow() {
    const container = document.getElementById('schedule_container');
    const row = document.createElement('div');
    row.className = 'schedule-row';
    row.innerHTML = `
        <div class="row g-3 align-items-end">
            <div class="col-md-3">
                <label class="form-label small fw-bold">
                    <i class="bi bi-calendar-day me-1 text-primary"></i>Day
                </label>
                <input type="text" class="form-control" name="workshop_schedules[${scheduleCount}][day]" placeholder="e.g., Day 1, Day 2, Monday, Tuesday">
            </div>
            <div class="col-md-4">
                <label class="form-label small fw-bold">
                    <i class="bi bi-book me-1 text-primary"></i>Topics
                </label>
                <input type="text" class="form-control" name="workshop_schedules[${scheduleCount}][topics]" placeholder="Topics covered on this day">
            </div>
            <div class="col-md-3">
                <label class="form-label small fw-bold">
                    <i class="bi bi-chat-left-text me-1 text-primary"></i>Description (Optional)
                </label>
                <input type="text" class="form-control" name="workshop_schedules[${scheduleCount}][description]" placeholder="Details about this day">
            </div>
            <div class="col-md-2 d-flex align-items-end">
                <button type="button" class="btn btn-outline-danger btn-sm w-100" onclick="this.closest('.schedule-row').remove()">
                    <i class="bi bi-trash"></i> Remove
                </button>
            </div>
        </div>
    `;
    container.appendChild(row);
    scheduleCount++;
}

// Learning Items Management
let learningItemCount = 1;

function addLearningItem() {
    const container = document.getElementById('learning_items_container');
    const row = document.createElement('div');
    row.className = 'learning-item input-group mb-2';
    row.innerHTML = `
        <span class="input-group-text"><i class="bi bi-check-circle text-primary"></i></span>
        <input type="text" class="form-control" name="what_you_will_learn[]" placeholder="e.g., How to Hack CCTV" required>
        <button type="button" class="btn btn-outline-danger" onclick="removeLearningItem(this)">
            <i class="bi bi-trash"></i>
        </button>
    `;
    container.appendChild(row);
    learningItemCount++;

    // Enable remove buttons if more than 1 item
    updateRemoveButtons();
}

function removeLearningItem(button) {
    const row = button.closest('.learning-item');
    row.remove();
    learningItemCount--;

    // Update remove buttons state
    updateRemoveButtons();
}

function updateRemoveButtons() {
    const container = document.getElementById('learning_items_container');
    const rows = container.querySelectorAll('.learning-item');
    const removeButtons = container.querySelectorAll('.learning-item .btn-outline-danger');

    removeButtons.forEach((btn, index) => {
        btn.disabled = rows.length <= 1;
    });
}

// Initialize on page load
document.addEventListener('DOMContentLoaded', function() {
    toggleLocationFields();
    toggleInvitationFields();
    toggleEventTypeFields();
});
