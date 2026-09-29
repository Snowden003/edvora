<!DOCTYPE html>
<html lang="fa" dir="rtl">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no, viewport-fit=cover">
    <title>ادورا | ورود به سامانه آموزشی</title>
    
    <link rel="icon" type="image/png" href="{{ asset('favicon.png') }}">
    <link rel="manifest" href="/manifest.json">
    <meta name="theme-color" content="#4f46e5">
    <meta name="mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-status-bar-style" content="black-translucent">
    <meta name="apple-mobile-web-app-title" content="Edvora">
    <link rel="apple-touch-icon" href="/icons/icon-192x192.png">

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Vazirmatn:wght@400;500;600;700;800;900&family=Poppins:wght@400;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">

    <style>
        :root {
            --primary: #4f46e5;
            --primary-light: #6366f1;
            --primary-dark: #3730a3;
            --bg-dark: #090d16;
            --card-bg: rgba(17, 24, 39, 0.75);
            --card-border: rgba(255, 255, 255, 0.08);
            --text-main: #f8fafc;
            --text-muted: #94a3b8;
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Vazirmatn', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
            -webkit-tap-highlight-color: transparent;
        }

        body {
            background-color: var(--bg-dark);
            background-image: 
                radial-gradient(at 0% 0%, rgba(79, 70, 229, 0.22) 0px, transparent 50%),
                radial-gradient(at 100% 100%, rgba(14, 165, 233, 0.18) 0px, transparent 50%),
                radial-gradient(at 50% 50%, rgba(99, 102, 241, 0.12) 0px, transparent 60%);
            min-height: 100vh;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
            color: var(--text-main);
            overflow-x: hidden;
            padding: env(safe-area-inset-top, 16px) env(safe-area-inset-right, 16px) env(safe-area-inset-bottom, 16px) env(safe-area-inset-left, 16px);
        }

        .app-header {
            text-align: center;
            padding-top: 18px;
            padding-bottom: 8px;
        }

        .logo-wrap {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            width: 72px;
            height: 72px;
            border-radius: 20px;
            background: linear-gradient(135deg, rgba(79, 70, 229, 0.3) 0%, rgba(14, 165, 233, 0.2) 100%);
            border: 1px solid rgba(255, 255, 255, 0.15);
            box-shadow: 0 10px 25px -5px rgba(79, 70, 229, 0.4);
            margin-bottom: 12px;
            overflow: hidden;
        }

        .logo-wrap img {
            width: 52px;
            height: 52px;
            object-fit: contain;
            border-radius: 12px;
        }

        .app-title {
            font-size: 22px;
            font-weight: 800;
            color: #ffffff;
            letter-spacing: -0.5px;
        }

        .app-subtitle {
            font-size: 13px;
            color: var(--text-muted);
            margin-top: 4px;
        }

        .main-container {
            width: 100%;
            max-width: 420px;
            margin: auto;
            padding: 12px 4px;
        }

        .card-box {
            background: var(--card-bg);
            border: 1px solid var(--card-border);
            border-radius: 24px;
            padding: 28px 24px;
            backdrop-filter: blur(20px);
            -webkit-backdrop-filter: blur(20px);
            box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.6);
        }

        /* Role Switcher Tabs */
        .role-tabs {
            display: flex;
            background: rgba(15, 23, 42, 0.8);
            border: 1px solid rgba(255, 255, 255, 0.06);
            border-radius: 16px;
            padding: 4px;
            margin-bottom: 24px;
            gap: 4px;
        }

        .role-tab-btn {
            flex: 1;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            padding: 12px;
            border: none;
            background: transparent;
            color: var(--text-muted);
            font-size: 14px;
            font-weight: 600;
            border-radius: 12px;
            cursor: pointer;
            transition: all 0.25s cubic-bezier(0.4, 0, 0.2, 1);
        }

        .role-tab-btn.active {
            background: linear-gradient(135deg, var(--primary) 0%, var(--primary-light) 100%);
            color: #ffffff;
            box-shadow: 0 4px 14px 0 rgba(79, 70, 229, 0.35);
        }

        /* Form Elements */
        .form-group {
            margin-bottom: 18px;
        }

        .form-label {
            display: block;
            font-size: 13px;
            font-weight: 600;
            color: #cbd5e1;
            margin-bottom: 8px;
        }

        .input-wrap {
            position: relative;
            display: flex;
            align-items: center;
        }

        .input-wrap i.prefix-icon {
            position: absolute;
            right: 14px;
            color: #64748b;
            font-size: 16px;
            pointer-events: none;
        }

        .form-control {
            width: 100%;
            height: 48px;
            background: rgba(15, 23, 42, 0.7);
            border: 1px solid rgba(255, 255, 255, 0.1);
            border-radius: 14px;
            padding: 0 42px 0 42px;
            font-size: 14px;
            color: #ffffff;
            transition: all 0.2s;
            outline: none;
        }

        .form-control:focus {
            border-color: var(--primary-light);
            background: rgba(15, 23, 42, 0.95);
            box-shadow: 0 0 0 3px rgba(99, 102, 241, 0.2);
        }

        .form-control::placeholder {
            color: #475569;
        }

        .toggle-pwd {
            position: absolute;
            left: 14px;
            background: none;
            border: none;
            color: #64748b;
            cursor: pointer;
            padding: 4px;
            font-size: 15px;
        }

        .form-options {
            display: flex;
            align-items: center;
            justify-content: space-between;
            margin-top: 14px;
            margin-bottom: 22px;
            font-size: 13px;
        }

        .remember-wrap {
            display: flex;
            align-items: center;
            gap: 8px;
            color: #94a3b8;
            cursor: pointer;
        }

        .remember-wrap input[type="checkbox"] {
            accent-color: var(--primary);
            width: 16px;
            height: 16px;
            border-radius: 4px;
            cursor: pointer;
        }

        .forgot-link {
            color: #818cf8;
            text-decoration: none;
            font-weight: 500;
            transition: color 0.2s;
        }

        .forgot-link:hover {
            color: #a5b4fc;
        }

        .btn-submit {
            width: 100%;
            height: 50px;
            border-radius: 14px;
            background: linear-gradient(135deg, #4f46e5 0%, #6366f1 100%);
            color: #ffffff;
            font-size: 15px;
            font-weight: 700;
            border: none;
            cursor: pointer;
            box-shadow: 0 10px 20px -5px rgba(79, 70, 229, 0.4);
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            transition: all 0.2s ease;
        }

        .btn-submit:hover {
            transform: translateY(-2px);
            box-shadow: 0 14px 25px -5px rgba(79, 70, 229, 0.5);
        }

        .btn-submit:active {
            transform: translateY(0);
        }

        /* Error Banner */
        .error-alert {
            background: rgba(239, 68, 68, 0.15);
            border: 1px solid rgba(239, 68, 68, 0.3);
            border-radius: 12px;
            padding: 12px 14px;
            color: #fca5a5;
            font-size: 13px;
            margin-bottom: 18px;
            display: flex;
            align-items: center;
            gap: 8px;
        }

        /* App Footer */
        .app-footer {
            text-align: center;
            padding: 16px 8px 12px;
            font-size: 12px;
            color: #64748b;
        }

        .footer-links {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 16px;
            margin-top: 6px;
        }

        .footer-links a {
            color: #818cf8;
            text-decoration: none;
            transition: color 0.2s;
        }

        .footer-links a:hover {
            color: #c7d2fe;
        }
    </style>
</head>
<body>

    <!-- App Header with Branding -->
    <header class="app-header">
        <div class="logo-wrap">
            <img src="{{ asset('logo.png') }}" alt="Edvora">
        </div>
        <h1 class="app-title">سامانه یادگیری ادورا</h1>
        <p class="app-subtitle" id="roleSubtitle">ورود به داشبورد دانش‌آموزان</p>
    </header>

    <!-- Main Content Box -->
    <main class="main-container">
        <div class="card-box">
            
            <!-- Student / Teacher Selector Tabs -->
            <div class="role-tabs">
                <button type="button" class="role-tab-btn active" id="tabStudent" onclick="switchRole('student')">
                    <i class="bi bi-mortarboard-fill"></i>
                    <span>دانش‌آموز</span>
                </button>
                <button type="button" class="role-tab-btn" id="tabTeacher" onclick="switchRole('teacher')">
                    <i class="bi bi-person-video3"></i>
                    <span>استاد</span>
                </button>
            </div>

            @if ($errors->any())
                <div class="error-alert">
                    <i class="bi bi-exclamation-circle-fill"></i>
                    <span>{{ $errors->first() }}</span>
                </div>
            @endif

            <!-- Direct Login Form (posts to /login) -->
            <form action="{{ route('login') }}" method="POST">
                @csrf
                <input type="hidden" name="intended_role" id="intendedRoleInput" value="student">

                <!-- Email / Username -->
                <div class="form-group">
                    <label class="form-label" for="loginEmail">ایمیل یا شماره تماس</label>
                    <div class="input-wrap">
                        <i class="bi bi-envelope prefix-icon"></i>
                        <input 
                            type="text" 
                            name="email" 
                            id="loginEmail" 
                            class="form-control" 
                            placeholder="example@mail.com" 
                            value="{{ old('email') }}" 
                            required 
                            autofocus 
                            autocomplete="username"
                        >
                    </div>
                </div>

                <!-- Password -->
                <div class="form-group">
                    <label class="form-label" for="loginPassword">رمز عبور</label>
                    <div class="input-wrap">
                        <i class="bi bi-shield-lock prefix-icon"></i>
                        <input 
                            type="password" 
                            name="password" 
                            id="loginPassword" 
                            class="form-control" 
                            placeholder="••••••••" 
                            required 
                            autocomplete="current-password"
                        >
                        <button type="button" class="toggle-pwd" onclick="togglePasswordVisibility()" aria-label="نمایش رمز">
                            <i class="bi bi-eye" id="pwdEyeIcon"></i>
                        </button>
                    </div>
                </div>

                <!-- Remember Me & Forgot Password -->
                <div class="form-options">
                    <label class="remember-wrap">
                        <input type="checkbox" name="remember" id="rememberMe" checked>
                        <span>مرا به خاطر بسپار</span>
                    </label>

                    @if (Route::has('password.request'))
                        <a href="{{ route('password.request') }}" class="forgot-link">فراموشی رمز؟</a>
                    @endif
                </div>

                <!-- Submit Button -->
                <button type="submit" class="btn-submit" id="submitBtn">
                    <span id="submitBtnText">ورود به داشبورد دانش‌آموز</span>
                    <i class="bi bi-arrow-left"></i>
                </button>
            </form>

            @if (Route::has('register'))
                <div style="text-align: center; margin-top: 20px; font-size: 13px; color: #94a3b8;">
                    حساب کاربری ندارید؟ 
                    <a href="{{ route('register') }}" style="color: #818cf8; text-decoration: none; font-weight: 600;">ثبت نام رایگان</a>
                </div>
            @endif
        </div>
    </main>

    <!-- App Footer with Store-Mandatory Legal Links -->
    <footer class="app-footer">
        <div>نسخه اپلیکیشن ادورا (PWA) v1.0.0</div>
        <div class="footer-links">
            <a href="{{ route('privacy') }}">حریم خصوصی</a>
            <span>•</span>
            <a href="{{ route('terms') }}">قوانین و مقررات</a>
        </div>
    </footer>

    <script>
        function switchRole(role) {
            const tabStudent = document.getElementById('tabStudent');
            const tabTeacher = document.getElementById('tabTeacher');
            const subtitle = document.getElementById('roleSubtitle');
            const intendedInput = document.getElementById('intendedRoleInput');
            const submitBtnText = document.getElementById('submitBtnText');

            if (role === 'teacher') {
                tabTeacher.classList.add('active');
                tabStudent.classList.remove('active');
                subtitle.textContent = 'ورود به داشبورد اساتید و مدرسان';
                intendedInput.value = 'teacher';
                submitBtnText.textContent = 'ورود به داشبورد استاد';
                document.getElementById('loginEmail').placeholder = 'teacher@edvora.af';
            } else {
                tabStudent.classList.add('active');
                tabTeacher.classList.remove('active');
                subtitle.textContent = 'ورود به داشبورد دانش‌آموزان';
                intendedInput.value = 'student';
                submitBtnText.textContent = 'ورود به داشبورد دانش‌آموز';
                document.getElementById('loginEmail').placeholder = 'student@edvora.af';
            }
        }

        function togglePasswordVisibility() {
            const pwdInput = document.getElementById('loginPassword');
            const eyeIcon = document.getElementById('pwdEyeIcon');
            if (pwdInput.type === 'password') {
                pwdInput.type = 'text';
                eyeIcon.classList.replace('bi-eye', 'bi-eye-slash');
            } else {
                pwdInput.type = 'password';
                eyeIcon.classList.replace('bi-eye-slash', 'bi-eye');
            }
        }

        // PWA Service Worker Registration
        if ('serviceWorker' in navigator) {
            window.addEventListener('load', () => {
                navigator.serviceWorker.register('/sw.js').catch(() => {});
            });
        }
    </script>
</body>
</html>
