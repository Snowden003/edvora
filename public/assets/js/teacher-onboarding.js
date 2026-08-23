const STORAGE_KEY = 'teacher_onboarding_draft';

function saveFormToStorage() {
    const form = document.getElementById('onboardingForm');
    if (!form) return;
    const data = {};
    form.querySelectorAll('input[name], select[name], textarea[name]').forEach(el => {
        if (el.type === 'file') return;
        data[el.name] = el.value;
    });
    localStorage.setItem(STORAGE_KEY, JSON.stringify(data));
}

function restoreFormFromStorage() {
    const raw = localStorage.getItem(STORAGE_KEY);
    if (!raw) return;
    let data;
    try { data = JSON.parse(raw); } catch (e) { return; }
    const form = document.getElementById('onboardingForm');
    if (!form) return;
    Object.keys(data).forEach(name => {
        const el = form.querySelector(`[name="${name}"]`);
        if (!el || el.type === 'file') return;
        el.value = data[name];
    });
}

function clearFormStorage() {
    localStorage.removeItem(STORAGE_KEY);
}

document.addEventListener('DOMContentLoaded', function () {
    restoreFormFromStorage();

    const form = document.getElementById('onboardingForm');
    if (form) {
        form.querySelectorAll('input[name], select[name], textarea[name]').forEach(el => {
            if (el.type === 'file') return;
            el.addEventListener('input', saveFormToStorage);
            el.addEventListener('change', saveFormToStorage);
        });
    }

    // Bio character counter + real-time min-50 warning
    const bioTextarea = document.querySelector('textarea[name="bio"]');
    const bioCount = document.getElementById('bioCount');
    if (bioTextarea && bioCount) {
        function updateBioUI() {
            const len = bioTextarea.value.length;
            bioCount.textContent = len;
            const errEl = document.getElementById('bioMinError');
            if (errEl) {
                if (len > 0 && len < 50) {
                    errEl.textContent = 'Bio must be more than 50 characters. (' + len + '/50)';
                    errEl.style.display = 'block';
                    bioTextarea.classList.add('is-invalid');
                } else {
                    errEl.style.display = 'none';
                    if (!bioTextarea.classList.contains('laravel-invalid')) {
                        bioTextarea.classList.remove('is-invalid');
                    }
                }
            }
        }
        bioTextarea.addEventListener('input', updateBioUI);
        updateBioUI();
    }

    // Restore step if validation failed (check which fields have errors)
    const invalidFields = document.querySelectorAll('.is-invalid');
    if (invalidFields.length > 0) {
        const firstInvalid = invalidFields[0];
        const step2Fields = ['department', 'experience_years', 'specialization', 'expertise', 'linkedin', 'github', 'website'];
        const fieldName = firstInvalid.getAttribute('name');
        if (step2Fields.includes(fieldName)) {
            goToStep(2);
        } else {
            goToStep(1);
        }
    }
});

function goToStep(step) {
    // Validate current step before moving forward
    const currentStep = getCurrentStep();
    if (step > currentStep && !validateStep(currentStep)) return;

    // Hide all steps
    document.querySelectorAll('.ob-step-content').forEach(el => el.classList.remove('active'));
    document.querySelectorAll('.ob-step').forEach((el, i) => {
        el.classList.remove('active', 'done');
        if (i + 1 < step) el.classList.add('done');
        if (i + 1 === step) el.classList.add('active');
    });

    // Update step lines
    document.querySelectorAll('.ob-step-line').forEach((line, i) => {
        line.classList.toggle('done', i + 1 < step);
    });

    // Show target step
    document.getElementById('step' + step).classList.add('active');

    // Scroll to top of card
    document.getElementById('step' + step).scrollIntoView({ behavior: 'smooth', block: 'start' });
}

function getCurrentStep() {
    const active = document.querySelector('.ob-step-content.active');
    return active ? parseInt(active.id.replace('step', '')) : 1;
}

function validateStep(step) {
    const stepEl = document.getElementById('step' + step);
    const requiredFields = stepEl.querySelectorAll('[required]');
    let valid = true;

    requiredFields.forEach(field => {
        field.classList.remove('is-invalid');
        if (!field.value.trim()) {
            field.classList.add('is-invalid');
            valid = false;
        } else if (field.name === 'bio' && field.value.trim().length < 50) {
            field.classList.add('is-invalid');
            const errEl = document.getElementById('bioMinError');
            if (errEl) {
                errEl.textContent = 'Bio must be more than 50 characters. (' + field.value.trim().length + '/50)';
                errEl.style.display = 'block';
            }
            valid = false;
        }
    });

    if (!valid) {
        const firstInvalid = stepEl.querySelector('.is-invalid');
        if (firstInvalid) firstInvalid.focus();
    }

    return valid;
}

function previewAvatar(input) {
    if (input.files && input.files[0]) {
        const reader = new FileReader();
        reader.onload = function (e) {
            const img = document.getElementById('avatarImg');
            const icon = document.getElementById('avatarIcon');
            img.src = e.target.result;
            img.style.display = 'block';
            if (icon) icon.style.display = 'none';
        };
        reader.readAsDataURL(input.files[0]);
    }
}

function showCvName(input) {
    const label = document.getElementById('cvFileName');
    if (input.files && input.files[0]) {
        label.textContent = input.files[0].name;
        label.style.color = '#198754';
    } else {
        label.textContent = 'No file selected';
        label.style.color = '';
    }
}

// Submit with loading state
document.getElementById('onboardingForm')?.addEventListener('submit', function (e) {
    // Validate all steps before actual submission
    if (!validateStep(1) || !validateStep(2) || !validateStep(3)) {
        e.preventDefault();
        if (!validateStep(1)) goToStep(1);
        else if (!validateStep(2)) goToStep(2);
        else goToStep(3);
        return;
    }

    clearFormStorage();
    const overlay = document.getElementById('onboardingLoadingOverlay');
    if (overlay) {
        overlay.hidden = false;
    }

    const btn = document.getElementById('submitBtn');
    if (btn) {
        setTimeout(() => {
            btn.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span>Submitting...';
            btn.disabled = true;
        }, 10);
    }
});

// Initialize on DOM ready
document.addEventListener('DOMContentLoaded', function () {
    initMultiSelect('expertiseWrapper', 'expertiseSearch', 'expertiseDropdown', 'expertiseTags', 'expertiseInput', 'expertiseCount', 10);

    // After multi-select is initialized, re-save on expertise changes
    const expertiseInput = document.getElementById('expertiseInput');
    if (expertiseInput) {
        const observer = new MutationObserver(saveFormToStorage);
        observer.observe(expertiseInput, { attributes: true, attributeFilter: ['value'] });
        expertiseInput.addEventListener('change', saveFormToStorage);
    }
});

// Multi-Select functionality
function initMultiSelect(wrapperId, searchId, dropdownId, tagsId, inputId, countId, maxItems) {
    const wrapper = document.getElementById(wrapperId);
    const search = document.getElementById(searchId);
    const dropdown = document.getElementById(dropdownId);
    const tagsContainer = document.getElementById(tagsId);
    const hiddenInput = document.getElementById(inputId);
    const countSpan = document.getElementById(countId);

    if (!wrapper || !search || !dropdown || !tagsContainer || !hiddenInput || !countSpan) return;

    let selectedItems = [];

    // Load old values if exists
    const oldValue = hiddenInput.value;
    if (oldValue) {
        selectedItems = oldValue.split(',').filter(v => v.trim());
        renderTags();
        updateOptions();
    }

    // Show dropdown on focus
    search.addEventListener('focus', () => {
        wrapper.classList.add('active');
        filterDropdown('');
    });

    // Filter on input
    search.addEventListener('input', (e) => {
        filterDropdown(e.target.value);
        wrapper.classList.add('active');
    });

    // Close when clicking outside
    document.addEventListener('click', (e) => {
        if (!wrapper.contains(e.target)) {
            wrapper.classList.remove('active');
        }
    });

    // Focus search when clicking input area
    wrapper.querySelector('.multi-select-input-area').addEventListener('click', (e) => {
        if (e.target.classList.contains('multi-select-input-area') || e.target.classList.contains('multi-select-tags')) {
            search.focus();
        }
    });

    // Handle option click
    dropdown.querySelectorAll('.multi-select-option').forEach(option => {
        option.addEventListener('click', () => {
            const value = option.dataset.value;
            if (selectedItems.includes(value)) {
                // Remove if already selected
                selectedItems = selectedItems.filter(item => item !== value);
            } else if (selectedItems.length < maxItems) {
                // Add if not at max
                selectedItems.push(value);
            }
            renderTags();
            updateOptions();
            search.value = '';
            search.focus();
            filterDropdown('');
            checkMaxReached();
        });
    });

    function renderTags() {
        tagsContainer.innerHTML = '';
        selectedItems.forEach(value => {
            const tag = document.createElement('span');
            tag.className = 'multi-select-tag';
            tag.innerHTML = `${value} <span class="remove-tag">&times;</span>`;
            tag.querySelector('.remove-tag').addEventListener('click', (e) => {
                e.stopPropagation();
                selectedItems = selectedItems.filter(item => item !== value);
                renderTags();
                updateOptions();
                checkMaxReached();
            });
            tagsContainer.appendChild(tag);
        });
        hiddenInput.value = selectedItems.join(',');
        countSpan.textContent = selectedItems.length;
    }

    function updateOptions() {
        dropdown.querySelectorAll('.multi-select-option').forEach(option => {
            const value = option.dataset.value;
            option.classList.toggle('selected', selectedItems.includes(value));
        });
    }

    function filterDropdown(term) {
        const lowerTerm = term.toLowerCase();
        dropdown.querySelectorAll('.multi-select-option').forEach(option => {
            const text = option.textContent.toLowerCase();
            option.classList.toggle('hidden', !text.includes(lowerTerm));
        });
    }

    function checkMaxReached() {
        if (selectedItems.length >= maxItems) {
            wrapper.classList.add('max-reached');
        } else {
            wrapper.classList.remove('max-reached');
        }
    }

    checkMaxReached();
}
