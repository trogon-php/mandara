<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Laravel App</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <link rel="icon" href="{{ url('favicon.ico') }}">
    <style>
        body {
            background: linear-gradient(to bottom right, #f0fdf4, #d1fae5);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 1rem;
        }
        .login-card {
            background: white;
            border-radius: 1rem;
            box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.1), 0 10px 10px -5px rgba(0, 0, 0, 0.04);
            padding: 2rem;
            border: 1px solid #f3f4f6;
            max-width: 28rem;
            width: 100%;
        }
        .icon-container {
            width: 3rem;
            height: 3rem;
            background: linear-gradient(to right, #10b981, #059669);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 1rem;
        }
        .icon-container i {
            color: white;
            font-size: 1.25rem;
        }
        .form-control:focus {
            border-color: #10b981;
            box-shadow: 0 0 0 0.2rem rgba(16, 185, 129, 0.25);
        }
        .form-control.is-invalid:focus {
            border-color: #dc3545;
            box-shadow: 0 0 0 0.2rem rgba(220, 53, 69, 0.25);
        }
        .input-icon {
            position: absolute;
            left: 0.75rem;
            top: 50%;
            transform: translateY(-50%);
            color: #9ca3af;
            pointer-events: none;
        }
        .input-wrapper {
            position: relative;
        }
        .input-wrapper .form-control {
            padding-left: 2.5rem;
        }
        .btn-login {
            background: linear-gradient(to right, #059669, #047857);
            border: none;
            color: white;
            width: 100%;
            padding: 0.75rem;
            border-radius: 0.5rem;
            font-weight: 500;
            transition: all 0.2s ease-in-out;
            transform: scale(1);
        }
        .btn-login:hover {
            background: linear-gradient(to right, #047857, #065f46);
            transform: scale(1.02);
            color: white;
        }
        .btn-login:focus {
            box-shadow: 0 0 0 0.2rem rgba(16, 185, 129, 0.5);
        }
        .form-check-input:checked {
            background-color: #059669;
            border-color: #059669;
        }
        .form-check-input:focus {
            border-color: #10b981;
            box-shadow: 0 0 0 0.2rem rgba(16, 185, 129, 0.25);
        }
        .forgot-password {
            color: #059669;
            text-decoration: none;
            font-weight: 500;
            transition: color 0.2s;
        }
        .forgot-password:hover {
            color: #047857;
        }
        .error-message {
            color: #dc3545;
            font-size: 0.875rem;
            margin-top: 0.5rem;
            display: flex;
            align-items: center;
        }
        .error-message i {
            margin-right: 0.25rem;
        }
        .dev-mode-banner {
            margin-top: 10px;
            font-size: 20px;
            padding: 5px;
            border-radius: 5px;
            border: 1px solid #dc3545;
            background-color: rgba(220, 53, 69, 0.1);
            color: #dc3545;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="login-card mx-auto">
            <!-- Header -->
            <div class="text-center mb-4">
                <div class="icon-container">
                    <i class="fas fa-user"></i>
                </div>
                <h2 class="h3 fw-bold text-dark mb-2">Welcome back</h2>
                <p class="text-muted mb-0">Sign in to your account</p>
                
                @if(!app()->isProduction())
                    <div class="dev-mode-banner">
                        <span>TESTING/ DEVELOPMENT MODE</span>
                    </div>
                @endif
            </div>

            <!-- Login Form -->
            <form method="POST" action="{{ route('login.post') }}">
                @csrf
                
                <!-- Email Field -->
                <div class="mb-4">
                    <label for="email" class="form-label fw-medium text-dark">
                        Email Address
                    </label>
                    <div class="input-wrapper">
                        <div class="input-icon">
                            <i class="fas fa-envelope"></i>
                        </div>
                        <input 
                            id="email"
                            type="email" 
                            name="email" 
                            value="{{ old('email') }}" 
                            placeholder="Enter your email"
                            required 
                            autofocus
                            class="form-control @error('email') is-invalid @enderror"
                        >
                    </div>
                    @error('email')
                        <div class="error-message">
                            <i class="fas fa-exclamation-circle"></i>
                            {{ $message }}
                        </div>
                    @enderror
                </div>

                <!-- Password Field -->
                <div class="mb-4">
                    <label for="password" class="form-label fw-medium text-dark">
                        Password
                    </label>
                    <div class="input-wrapper">
                        <div class="input-icon">
                            <i class="fas fa-lock"></i>
                        </div>
                        <input 
                            id="password"
                            type="password" 
                            name="password" 
                            placeholder="Enter your password"
                            required
                            class="form-control @error('password') is-invalid @enderror"
                        >
                    </div>
                    @error('password')
                        <div class="error-message">
                            <i class="fas fa-exclamation-circle"></i>
                            {{ $message }}
                        </div>
                    @enderror
                </div>

                <!-- Remember Me & Forgot Password -->
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <div class="form-check">
                        <input 
                            id="remember" 
                            type="checkbox" 
                            name="remember"
                            class="form-check-input"
                        >
                        <label for="remember" class="form-check-label text-dark">
                            Remember me
                        </label>
                    </div>
                    <div>
                        <a href="#" class="forgot-password">
                            Forgot password?
                        </a>
                    </div>
                </div>

                <!-- Submit Button -->
                <button 
                    type="submit"
                    class="btn btn-login"
                >
                    <i class="fas fa-sign-in-alt me-2"></i>
                    Sign In
                </button>
            </form>
        </div>

        <!-- Footer -->
        <div class="text-center mt-4">
            <p class="text-muted small mb-0">
                © 2025 {{ config('app.name') }}. All rights reserved.
            </p>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
