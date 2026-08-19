<x-guest-layout>
    <div class="reset-container">
        <div class="reset-card">
            {{-- Header with Icon --}}
            <div class="reset-header">
                <div class="reset-icon">
                    <i class="bi bi-shield-lock-fill"></i>
                </div>
                <h1 class="reset-title">Reset Password</h1>
                <p class="reset-subtitle">Create a new secure password for your account</p>
            </div>

            {{-- Reset Form --}}
            <form method="POST" action="{{ route('password.store') }}" class="reset-form">
                @csrf

                {{-- Password Reset Token --}}
                <input type="hidden" name="token" value="{{ $request->route('token') }}">

                {{-- Email Field --}}
                <div class="form-group">
                    <label for="email" class="form-label">
                        <i class="bi bi-envelope"></i>
                        Email Address
                    </label>
                    <input 
                        id="email" 
                        type="email" 
                        name="email" 
                        value="{{ old('email', $request->email) }}" 
                        required 
                        autofocus 
                        autocomplete="username"
                        class="form-input @error('email') is-invalid @enderror"
                        placeholder="Enter your email"
                        readonly
                    >
                    @error('email')
                        <span class="error-message">{{ $message }}</span>
                    @enderror
                </div>

                {{-- Password Field --}}
                <div class="form-group">
                    <label for="password" class="form-label">
                        <i class="bi bi-lock"></i>
                        New Password
                    </label>
                    <div class="password-wrapper">
                        <input 
                            id="password" 
                            type="password" 
                            name="password" 
                            required 
                            autocomplete="new-password"
                            class="form-input @error('password') is-invalid @enderror"
                            placeholder="Enter new password"
                        >
                        <button type="button" class="toggle-password" onclick="togglePassword('password')">
                            <i class="bi bi-eye"></i>
                        </button>
                    </div>
                    @error('password')
                        <span class="error-message">{{ $message }}</span>
                    @enderror
                </div>

                {{-- Confirm Password Field --}}
                <div class="form-group">
                    <label for="password_confirmation" class="form-label">
                        <i class="bi bi-lock-fill"></i>
                        Confirm Password
                    </label>
                    <div class="password-wrapper">
                        <input 
                            id="password_confirmation" 
                            type="password" 
                            name="password_confirmation" 
                            required 
                            autocomplete="new-password"
                            class="form-input"
                            placeholder="Confirm your password"
                        >
                        <button type="button" class="toggle-password" onclick="togglePassword('password_confirmation')">
                            <i class="bi bi-eye"></i>
                        </button>
                    </div>
                    @error('password_confirmation')
                        <span class="error-message">{{ $message }}</span>
                    @enderror
                </div>

                {{-- Submit Button --}}
                <button type="submit" class="reset-btn">
                    <i class="bi bi-arrow-repeat"></i>
                    Reset Password
                </button>
            </form>

            {{-- Back to Login --}}
            <div class="reset-footer">
                <a href="{{ route('login') }}" class="back-link">
                    <i class="bi bi-arrow-left"></i>
                    Back to Login
                </a>
            </div>
        </div>
    </div>

    @push('styles')
    <style>
        .reset-container {
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 2rem;
            background: linear-gradient(135deg, #0f172a 0%, #1e3a8a 50%, #312e81 100%);
            position: relative;
            overflow: hidden;
        }

        .reset-container::before {
            content: '';
            position: absolute;
            top: -50%;
            right: -50%;
            width: 100%;
            height: 100%;
            background: radial-gradient(circle, rgba(31, 143, 255, 0.15) 0%, transparent 70%);
            animation: pulse 4s ease-in-out infinite;
        }

        @keyframes pulse {
            0%, 100% { transform: scale(1); opacity: 0.5; }
            50% { transform: scale(1.1); opacity: 0.8; }
        }

        .reset-card {
            background: rgba(255, 255, 255, 0.98);
            border-radius: 24px;
            padding: 2.5rem;
            width: 100%;
            max-width: 420px;
            box-shadow: 0 25px 80px rgba(0, 0, 0, 0.25);
            position: relative;
            z-index: 1;
            backdrop-filter: blur(20px);
            -webkit-backdrop-filter: blur(20px);
            border: 1px solid rgba(255, 255, 255, 0.3);
        }

        .reset-header {
            text-align: center;
            margin-bottom: 2rem;
        }

        .reset-icon {
            width: 80px;
            height: 80px;
            background: linear-gradient(135deg, #1F8FFF 0%, #6366f1 100%);
            border-radius: 20px;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 1.5rem;
            box-shadow: 0 10px 40px rgba(31, 143, 255, 0.3);
        }

        .reset-icon i {
            font-size: 2.5rem;
            color: white;
        }

        .reset-title {
            font-size: 1.75rem;
            font-weight: 800;
            color: #1e293b;
            margin: 0 0 0.5rem 0;
        }

        .reset-subtitle {
            font-size: 0.9rem;
            color: #64748b;
            margin: 0;
        }

        .reset-form {
            display: flex;
            flex-direction: column;
            gap: 1.25rem;
        }

        .form-group {
            display: flex;
            flex-direction: column;
            gap: 0.4rem;
        }

        .form-label {
            font-size: 0.85rem;
            font-weight: 600;
            color: #475569;
            display: flex;
            align-items: center;
            gap: 0.4rem;
        }

        .form-label i {
            color: #1F8FFF;
            font-size: 1rem;
        }

        .form-input {
            padding: 0.9rem 1rem;
            border: 2px solid #e2e8f0;
            border-radius: 12px;
            font-size: 0.95rem;
            color: #1e293b;
            background: #fff;
            transition: all 0.2s ease;
        }

        .form-input:focus {
            outline: none;
            border-color: #1F8FFF;
            box-shadow: 0 0 0 4px rgba(31, 143, 255, 0.1);
        }

        .form-input.is-invalid {
            border-color: #ef4444;
            box-shadow: 0 0 0 4px rgba(239, 68, 68, 0.1);
        }

        .form-input[readonly] {
            background: #f8fafc;
            cursor: not-allowed;
        }

        .password-wrapper {
            position: relative;
        }

        .password-wrapper .form-input {
            padding-right: 3rem;
        }

        .toggle-password {
            position: absolute;
            right: 1rem;
            top: 50%;
            transform: translateY(-50%);
            background: none;
            border: none;
            color: #94a3b8;
            cursor: pointer;
            padding: 0.25rem;
            transition: color 0.2s;
        }

        .toggle-password:hover {
            color: #1F8FFF;
        }

        .error-message {
            font-size: 0.8rem;
            color: #ef4444;
            display: flex;
            align-items: center;
            gap: 0.3rem;
        }

        .reset-btn {
            background: linear-gradient(135deg, #1F8FFF 0%, #6366f1 100%);
            color: white;
            border: none;
            border-radius: 12px;
            padding: 1rem;
            font-size: 1rem;
            font-weight: 700;
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 0.5rem;
            margin-top: 0.5rem;
            box-shadow: 0 10px 30px rgba(31, 143, 255, 0.3);
            transition: all 0.2s ease;
        }

        .reset-btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 15px 40px rgba(31, 143, 255, 0.4);
        }

        .reset-btn:active {
            transform: translateY(0);
        }

        .reset-footer {
            text-align: center;
            margin-top: 1.5rem;
            padding-top: 1.5rem;
            border-top: 1px solid #e2e8f0;
        }

        .back-link {
            color: #64748b;
            font-size: 0.9rem;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 0.4rem;
            transition: color 0.2s;
        }

        .back-link:hover {
            color: #1F8FFF;
        }

        /* Mobile Responsive */
        @media (max-width: 480px) {
            .reset-container {
                padding: 1rem;
                background: linear-gradient(180deg, #0f172a 0%, #1e3a8a 100%);
            }

            .reset-container::before {
                display: none;
            }

            .reset-card {
                padding: 1.75rem;
                border-radius: 20px;
                backdrop-filter: none;
                -webkit-backdrop-filter: none;
            }

            .reset-icon {
                width: 70px;
                height: 70px;
                border-radius: 16px;
            }

            .reset-icon i {
                font-size: 2rem;
            }

            .reset-title {
                font-size: 1.5rem;
            }

            .reset-subtitle {
                font-size: 0.85rem;
            }

            .form-input {
                padding: 0.8rem 0.9rem;
                font-size: 0.9rem;
            }

            .reset-btn {
                padding: 0.9rem;
                font-size: 0.95rem;
            }
        }
    </style>
    @endpush

    @push('scripts')
    <script>
        function togglePassword(inputId) {
            const input = document.getElementById(inputId);
            const button = input.nextElementSibling;
            const icon = button.querySelector('i');
            
            if (input.type === 'password') {
                input.type = 'text';
                icon.classList.remove('bi-eye');
                icon.classList.add('bi-eye-slash');
            } else {
                input.type = 'password';
                icon.classList.remove('bi-eye-slash');
                icon.classList.add('bi-eye');
            }
        }
    </script>
    @endpush
</x-guest-layout>
