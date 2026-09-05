/**
 * 3D Glassmorphism Auth Pages Interactive Scripts - Edvora Tech
 */

document.addEventListener('DOMContentLoaded', function () {
    // 1. Password Visibility Toggles
    const togglePassword = document.querySelector('#togglePassword');
    const password = document.querySelector('#password');
    const icon = document.querySelector('#togglePasswordIcon');

    if (togglePassword && password) {
        togglePassword.addEventListener('click', function () {
            const type = password.getAttribute('type') === 'password' ? 'text' : 'password';
            password.setAttribute('type', type);
            if (icon) {
                icon.classList.toggle('bi-eye');
                icon.classList.toggle('bi-eye-slash');
            }
        });
    }

    const toggleConfirmPassword = document.querySelector('#toggleConfirmPassword');
    const confirmPassword = document.querySelector('#password_confirmation');
    const confirmIcon = document.querySelector('#toggleConfirmPasswordIcon');

    if (toggleConfirmPassword && confirmPassword) {
        toggleConfirmPassword.addEventListener('click', function () {
            const type = confirmPassword.getAttribute('type') === 'password' ? 'text' : 'password';
            confirmPassword.setAttribute('type', type);
            if (confirmIcon) {
                confirmIcon.classList.toggle('bi-eye');
                confirmIcon.classList.toggle('bi-eye-slash');
            }
        });
    }

    // 2. Role Selector Updating Google Auth Link (Register Page)
    const roleRadios = document.querySelectorAll('input[name="role"]');
    const googleLink = document.getElementById('google-auth-link');

    function updateGoogleLink() {
        const selectedRadio = document.querySelector('input[name="role"]:checked');
        if (selectedRadio && googleLink) {
            const selectedRole = selectedRadio.value;
            const currentUrl = new URL(googleLink.href, window.location.origin);
            currentUrl.searchParams.set('role', selectedRole);
            googleLink.href = currentUrl.toString();
        }
    }

    if (roleRadios.length > 0 && googleLink) {
        roleRadios.forEach(radio => {
            radio.addEventListener('change', updateGoogleLink);
        });
        updateGoogleLink();
    }

    // 3. 3D Parallax Tilt Effect (Desktop Only)
    const card = document.getElementById('glassCard');
    const wrapper = document.querySelector('.login-wrapper');

    if (card && wrapper) {
        wrapper.addEventListener('mousemove', function (e) {
            if (window.innerWidth < 768) return;

            const rect = wrapper.getBoundingClientRect();
            const x = e.clientX - rect.left;
            const y = e.clientY - rect.top;

            const centerX = rect.width / 2;
            const centerY = rect.height / 2;

            const rotateX = ((y - centerY) / centerY) * -6;
            const rotateY = ((x - centerX) / centerX) * 6;

            card.style.transform = `rotateX(${rotateX}deg) rotateY(${rotateY}deg)`;
        });

        wrapper.addEventListener('mouseleave', function () {
            if (window.innerWidth >= 768) {
                card.style.transform = `rotateX(0deg) rotateY(0deg)`;
                card.style.transition = `transform 0.6s cubic-bezier(0.2, 0.8, 0.2, 1)`;
            } else {
                card.style.transform = '';
            }
        });

        wrapper.addEventListener('mouseenter', function () {
            if (window.innerWidth >= 768) {
                card.style.transition = `none`;
            }
        });

        window.addEventListener('resize', function () {
            if (window.innerWidth < 768) {
                card.style.transform = '';
                card.style.transition = '';
            }
        });
    }
});
