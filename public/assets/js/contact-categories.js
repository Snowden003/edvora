document.addEventListener('DOMContentLoaded', function () {
    var categoryStep = document.getElementById('categoryStep');
    var formStep = document.getElementById('formStep');
    var categoryInput = document.getElementById('categoryInput');
    var backBtn = document.getElementById('backToCategories');
    var selectedIcon = document.getElementById('selectedCategoryIcon');
    var selectedName = document.getElementById('selectedCategoryName');
    var cards = document.querySelectorAll('.category-card');

    if (!categoryStep || !formStep) return;

    var iconMap = {
        'bug_report': 'bi-bug',
        'system_issue': 'bi-exclamation-triangle',
        'partnership': 'bi-handshake',
        'course_inquiry': 'bi-book',
        'account_help': 'bi-person-gear',
        'billing': 'bi-credit-card',
        'general': 'bi-chat-dots'
    };

    var nameMap = {
        'bug_report': 'Bug Report',
        'system_issue': 'System Issue',
        'partnership': 'Partnership',
        'course_inquiry': 'Course Inquiry',
        'account_help': 'Account Help',
        'billing': 'Billing & Payment',
        'general': 'General'
    };

    var placeholderMap = {
        'bug_report': 'Describe the bug you encountered in detail...',
        'system_issue': 'Describe the system issue you are facing...',
        'partnership': 'Please describe your partnership proposal, including your organization details and collaboration ideas...',
        'course_inquiry': 'What would you like to know about our courses?',
        'account_help': 'Describe the account or profile help you need...',
        'billing': 'Please describe your billing or payment issue...',
        'general': 'Tell us how we can help you...'
    };

    var subjectMap = {
        'bug_report': 'Bug Report: ',
        'system_issue': 'System Issue: ',
        'partnership': 'Partnership Proposal: ',
        'course_inquiry': 'Course Inquiry: ',
        'account_help': 'Account Help: ',
        'billing': 'Billing Issue: ',
        'general': ''
    };

    function toggleFormalFields(isFormal) {
        var formalFields = document.querySelectorAll('.formal-field');
        var normalFields = document.querySelectorAll('.normal-field');

        formalFields.forEach(function (el) {
            if (isFormal) {
                el.classList.remove('d-none');
            } else {
                el.classList.add('d-none');
            }
        });

        normalFields.forEach(function (el) {
            if (isFormal) {
                el.classList.add('d-none');
            } else {
                el.classList.remove('d-none');
            }
        });
    }

    cards.forEach(function (card) {
        card.addEventListener('click', function () {
            var category = card.getAttribute('data-category');
            var isFormal = card.getAttribute('data-formal') === 'true';

            // Set hidden input
            categoryInput.value = category;

            // Update header bar
            selectedIcon.className = 'bi ' + (iconMap[category] || 'bi-chat-dots');
            selectedName.textContent = nameMap[category] || category;

            // Toggle formal/normal fields
            toggleFormalFields(isFormal);

            // Update textarea placeholder
            var textarea = document.getElementById('contactMessage');
            if (textarea) {
                textarea.placeholder = placeholderMap[category] || 'Tell us how we can help you...';
            }

            // Pre-fill subject prefix
            var subjectInput = document.getElementById('contactSubject');
            if (subjectInput && !subjectInput.value) {
                subjectInput.value = subjectMap[category] || '';
            }

            // Switch steps
            categoryStep.classList.add('d-none');
            formStep.classList.remove('d-none');
        });
    });

    // Back button
    if (backBtn) {
        backBtn.addEventListener('click', function () {
            formStep.classList.add('d-none');
            categoryStep.classList.remove('d-none');
        });
    }
});
