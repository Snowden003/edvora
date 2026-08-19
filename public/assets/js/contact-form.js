document.addEventListener('DOMContentLoaded', function () {
    var form = document.getElementById('contactForm');
    if (!form) return;

    form.addEventListener('submit', function (e) {
        e.preventDefault();

        var url = form.getAttribute('data-url');
        var submitBtn = form.querySelector('[type="submit"]');
        var alertBox = document.getElementById('contactAlert');

        // Detect button type (home page vs contact page)
        var homeBtnText = submitBtn.querySelector('.btn-text');
        var homeBtnLoading = submitBtn.querySelector('.btn-loading');

        // Clear previous errors
        alertBox.classList.add('d-none');
        alertBox.classList.remove('alert-success', 'alert-danger');
        form.querySelectorAll('.is-invalid').forEach(function (el) {
            el.classList.remove('is-invalid');
        });
        form.querySelectorAll('.invalid-feedback').forEach(function (el) {
            el.remove();
        });

        // Show loading
        submitBtn.disabled = true;
        if (homeBtnText && homeBtnLoading) {
            homeBtnText.classList.add('d-none');
            homeBtnLoading.classList.remove('d-none');
        } else {
            submitBtn.setAttribute('data-original-text', submitBtn.innerHTML);
            var btnContent = submitBtn.querySelector('.btn-content');
            if (btnContent) {
                btnContent.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span> Sending...';
            }
        }

        var formData = new FormData(form);

        fetch(url, {
            method: 'POST',
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
                'Accept': 'application/json',
            },
            body: formData,
        })
            .then(function (response) {
                return response.json().then(function (data) {
                    return { status: response.status, data: data };
                });
            })
            .then(function (result) {
                if (result.status === 200 && result.data.success) {
                    alertBox.textContent = result.data.message;
                    alertBox.classList.remove('d-none', 'alert-danger');
                    alertBox.classList.add('alert-success');
                    form.reset();
                } else if (result.status === 422 && result.data.errors) {
                    var errors = result.data.errors;
                    Object.keys(errors).forEach(function (field) {
                        var input = form.querySelector('[name="' + field + '"]');
                        if (input) {
                            input.classList.add('is-invalid');
                            var feedback = document.createElement('div');
                            feedback.className = 'invalid-feedback';
                            feedback.textContent = errors[field][0];
                            input.parentNode.appendChild(feedback);
                        }
                    });
                    alertBox.textContent = 'Please fix the errors below.';
                    alertBox.classList.remove('d-none', 'alert-success');
                    alertBox.classList.add('alert-danger');
                } else {
                    alertBox.textContent = result.data.message || 'Something went wrong. Please try again.';
                    alertBox.classList.remove('d-none', 'alert-success');
                    alertBox.classList.add('alert-danger');
                }
            })
            .catch(function () {
                alertBox.textContent = 'Network error. Please check your connection and try again.';
                alertBox.classList.remove('d-none', 'alert-success');
                alertBox.classList.add('alert-danger');
            })
            .finally(function () {
                submitBtn.disabled = false;
                if (homeBtnText && homeBtnLoading) {
                    homeBtnText.classList.remove('d-none');
                    homeBtnLoading.classList.add('d-none');
                } else {
                    var originalHtml = submitBtn.getAttribute('data-original-text');
                    if (originalHtml) {
                        submitBtn.innerHTML = originalHtml;
                    }
                }
            });
    });
});
