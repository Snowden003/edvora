// Authentication JavaScript
document.addEventListener('DOMContentLoaded', function() {
    initPasswordToggle();
});

// Initialize Login Form
function initLoginForm() {
    const loginForm = document.getElementById('loginForm');
    if (!loginForm) return;

    loginForm.addEventListener('submit', function(e) {
        e.preventDefault();
        
        const email = document.getElementById('email').value;
        const password = document.getElementById('password').value;
        const userType = document.getElementById('userType').value;
        const rememberMe = document.getElementById('rememberMe').checked;

        // Basic validation
        if (!email || !password || !userType) {
            showAlert('Please fill in all required fields.', 'danger');
            return;
        }

        // Show loading state
        const submitBtn = loginForm.querySelector('button[type="submit"]');
        const originalText = submitBtn.innerHTML;
        submitBtn.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span>Signing In...';
        submitBtn.disabled = true;

        // Simulate login process
        setTimeout(() => {
            // Demo login logic
            if (isValidDemoLogin(email, password, userType)) {
                // Store user session
                if (rememberMe) {
                    localStorage.setItem('userSession', JSON.stringify({
                        email: email,
                        userType: userType,
                        loginTime: new Date().toISOString()
                    }));
                } else {
                    sessionStorage.setItem('userSession', JSON.stringify({
                        email: email,
                        userType: userType,
                        loginTime: new Date().toISOString()
                    }));
                }

                showAlert('Login successful! Redirecting...', 'success');
                
                // Redirect based on user type
                setTimeout(() => {
                    switch(userType) {
                        case 'student':
                            window.location.href = 'student-dashboard.html';
                            break;
                        case 'teacher':
                            window.location.href = 'teacher-dashboard.html';
                            break;
                        case 'admin':
                            window.location.href = 'admin-dashboard.html';
                            break;
                        default:
                            window.location.href = 'index.html';
                    }
                }, 1500);
            } else {
                showAlert('Invalid credentials. Please check your email, password, and user type.', 'danger');
                submitBtn.innerHTML = originalText;
                submitBtn.disabled = false;
            }
        }, 2000);
    });
}

// Initialize Signup Form
function initSignupForm() {
    const signupForm = document.getElementById('signupForm');
    if (!signupForm) return;

    signupForm.addEventListener('submit', function(e) {
        e.preventDefault();
        
        const firstName = document.getElementById('firstName')?.value;
        const lastName = document.getElementById('lastName')?.value;
        const email = document.getElementById('email').value;
        const password = document.getElementById('password').value;
        const confirmPassword = document.getElementById('confirmPassword')?.value;
        const userType = document.getElementById('userType').value;
        const agreeTerms = document.getElementById('agreeTerms')?.checked;

        // Validation
        if (!firstName || !lastName || !email || !password || !confirmPassword || !userType) {
            showAlert('Please fill in all required fields.', 'danger');
            return;
        }

        if (password !== confirmPassword) {
            showAlert('Passwords do not match.', 'danger');
            return;
        }

        if (password.length < 6) {
            showAlert('Password must be at least 6 characters long.', 'danger');
            return;
        }

        if (!agreeTerms) {
            showAlert('Please agree to the Terms of Service and Privacy Policy.', 'danger');
            return;
        }

        // Show loading state
        const submitBtn = signupForm.querySelector('button[type="submit"]');
        const originalText = submitBtn.innerHTML;
        submitBtn.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span>Creating Account...';
        submitBtn.disabled = true;

        // Simulate signup process
        setTimeout(() => {
            showAlert('Account created successfully! Please check your email for verification.', 'success');
            
            setTimeout(() => {
                window.location.href = 'login.html';
            }, 2000);
        }, 2000);
    });
}

// Initialize Password Toggle
function initPasswordToggle() {
    const toggleButtons = document.querySelectorAll('#togglePassword, #toggleConfirmPassword');
    
    toggleButtons.forEach(button => {
        button.addEventListener('click', function() {
            const targetId = this.id === 'togglePassword' ? 'password' : 'confirmPassword';
            const passwordInput = document.getElementById(targetId);
            const icon = this.querySelector('i');
            
            if (passwordInput.type === 'password') {
                passwordInput.type = 'text';
                icon.className = 'bi bi-eye-slash';
            } else {
                passwordInput.type = 'password';
                icon.className = 'bi bi-eye';
            }
        });
    });
}

// Demo Login Validation
function isValidDemoLogin(email, password, userType) {
    const demoAccounts = {
        'student@demo.com': { password: 'demo123', type: 'student' },
        'teacher@demo.com': { password: 'demo123', type: 'teacher' },
        'admin@demo.com': { password: 'demo123', type: 'admin' }
    };

    const account = demoAccounts[email];
    return account && account.password === password && account.type === userType;
}

// Fill Demo Credentials
function fillDemoCredentials(userType) {
    const emailInput = document.getElementById('email');
    const passwordInput = document.getElementById('password');
    const userTypeSelect = document.getElementById('userType');

    const demoCredentials = {
        'student': { email: 'student@demo.com', password: 'demo123' },
        'teacher': { email: 'teacher@demo.com', password: 'demo123' },
        'admin': { email: 'admin@demo.com', password: 'demo123' }
    };

    const credentials = demoCredentials[userType];
    if (credentials) {
        emailInput.value = credentials.email;
        passwordInput.value = credentials.password;
        userTypeSelect.value = userType;
        
        // Add visual feedback
        [emailInput, passwordInput, userTypeSelect].forEach(input => {
            input.classList.add('border-success');
            setTimeout(() => {
                input.classList.remove('border-success');
            }, 2000);
        });
    }
}

// Show Alert
function showAlert(message, type = 'info') {
    // Remove existing alerts
    const existingAlerts = document.querySelectorAll('.alert');
    existingAlerts.forEach(alert => alert.remove());

    // Create new alert
    const alertDiv = document.createElement('div');
    alertDiv.className = `alert alert-${type} alert-dismissible fade show`;
    alertDiv.innerHTML = `
        ${message}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    `;

    // Insert alert at the top of the form
    const form = document.querySelector('form');
    if (form) {
        form.insertBefore(alertDiv, form.firstChild);
    }

    // Auto-dismiss success alerts
    if (type === 'success') {
        setTimeout(() => {
            alertDiv.remove();
        }, 5000);
    }
}

// Check if user is already logged in
function checkAuthStatus() {
    const userSession = localStorage.getItem('userSession') || sessionStorage.getItem('userSession');
    
    if (userSession) {
        const session = JSON.parse(userSession);
        const currentPage = window.location.pathname.split('/').pop();
        
        // Redirect to appropriate dashboard if on login/signup page
        if (currentPage === 'login.html' || currentPage === 'signup.html') {
            switch(session.userType) {
                case 'student':
                    window.location.href = 'student-dashboard.html';
                    break;
                case 'teacher':
                    window.location.href = 'teacher-dashboard.html';
                    break;
                case 'admin':
                    window.location.href = 'admin-dashboard.html';
                    break;
            }
        }
    }
}

// Logout function
function logout() {
    localStorage.removeItem('userSession');
    sessionStorage.removeItem('userSession');
    window.location.href = 'index.html';
}

// Password strength checker
function checkPasswordStrength(password) {
    const strength = {
        score: 0,
        feedback: []
    };

    if (password.length >= 8) {
        strength.score += 1;
    } else {
        strength.feedback.push('Use at least 8 characters');
    }

    if (/[a-z]/.test(password)) {
        strength.score += 1;
    } else {
        strength.feedback.push('Include lowercase letters');
    }

    if (/[A-Z]/.test(password)) {
        strength.score += 1;
    } else {
        strength.feedback.push('Include uppercase letters');
    }

    if (/\d/.test(password)) {
        strength.score += 1;
    } else {
        strength.feedback.push('Include numbers');
    }

    if (/[^a-zA-Z\d]/.test(password)) {
        strength.score += 1;
    } else {
        strength.feedback.push('Include special characters');
    }

    return strength;
}

// Email validation
function isValidEmail(email) {
    const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
    return emailRegex.test(email);
}

// Initialize on page load
document.addEventListener('DOMContentLoaded', function() {
    checkAuthStatus();
});
