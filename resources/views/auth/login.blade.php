@extends('layouts.guest')

@section('title', 'Login - SIMagang')

@section('content')
<div class="login-wrapper">
    <div class="login-container">
        <div class="card shadow-lg border-0 login-card">
            <div class="card-body p-5">
                    <div class="text-center mb-4">
                        <i class="fas fa-graduation-cap text-primary mb-3 login-icon"></i>
                        <h3 class="fw-bold login-title">SIMagang</h3>
                        <p class="text-muted login-subtitle">Sistem Informasi Magang</p>
                    </div>
                    
                    @if ($errors->has('error'))
                        <div class="alert alert-danger alert-dismissible fade show login-alert" role="alert">
                            <i class="fas fa-exclamation-circle me-2"></i>{{ $errors->first('error') }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>
                    @endif

                    @if (session('error'))
                        <div class="alert alert-danger alert-dismissible fade show login-alert" role="alert">
                            <i class="fas fa-exclamation-circle me-2"></i>{{ session('error') }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>
                    @endif

                    @if (session('success'))
                        <div class="alert alert-success alert-dismissible fade show login-alert" role="alert">
                            <i class="fas fa-check-circle me-2"></i>{{ session('success') }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>
                    @endif

                    <form method="POST" action="{{ route('login.post') }}">
                        @csrf
                        
                        <div class="mb-3">
                            <label for="username" class="form-label login-label">Username</label>
                            <input type="text" class="form-control form-control-lg @error('username') is-invalid @enderror" 
                                   id="username" name="username" value="{{ old('username') }}" required autofocus>
                            @error('username')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        
                        <div class="mb-4">
                            <label for="password" class="form-label login-label">Password</label>
                            <div class="input-group">
                                <input type="password" class="form-control form-control-lg @error('password') is-invalid @enderror" 
                                       id="password" name="password" required>
                                <button type="button" class="btn btn-outline-secondary" onclick="togglePassword('password', this)">
                                    <i class="fas fa-eye"></i>
                                </button>
                            </div>
                            @error('password')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        
                        <div class="d-grid mb-3">
                            <button type="submit" class="btn btn-primary btn-lg login-btn" id="loginBtn">
                                <span class="btn-text">
                                    <i class="fas fa-sign-in-alt me-2"></i>Login
                                </span>
                                <span class="spinner d-none">
                                    <span class="spinner-border spinner-border-sm me-2" role="status" aria-hidden="true"></span>
                                    <span>Memproses...</span>
                                </span>
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

<style>
    html, body {
        height: 100%;
        margin: 0;
        padding: 0;
    }

    body.bg-dark {
        background: linear-gradient(135deg, #1565C0 0%, #0D47A1 100%) !important;
        min-height: 100vh;
        display: flex;
        align-items: center;
        justify-content: center;
    }

    .login-wrapper {
        display: flex;
        align-items: center;
        justify-content: center;
        min-height: 100vh;
        width: 100%;
        padding: 1rem;
    }

    .login-container {
        width: 100%;
        max-width: 450px;
    }

    .login-card {
        border-radius: 15px;
        box-shadow: 0 20px 60px rgba(0,0,0,0.3) !important;
        border: none;
        overflow: hidden;
    }

    .login-icon {
        font-size: 3.5rem;
        color: #1565C0;
    }

    .login-title {
        font-size: 1.75rem;
        font-weight: 700;
        color: #333;
        margin-bottom: 0.5rem;
    }

    .login-subtitle {
        font-size: 0.95rem;
        color: #6c757d;
    }

    .login-label {
        font-weight: 500;
        color: #333;
        margin-bottom: 0.5rem;
    }

    .login-alert {
        border-radius: 8px;
        border-left: 4px solid;
    }

    /* Mobile Adjustments */
    @media (max-width: 575.98px) {
        body.bg-dark {
            background: white !important;
        }

        .login-wrapper {
            padding: 0.5rem;
        }

        .card-body {
            padding: 2rem 1.5rem !important;
        }

        .login-icon {
            font-size: 2.5rem;
            margin-bottom: 0.75rem !important;
        }

        .login-title {
            font-size: 1.5rem;
            margin-bottom: 0.25rem;
        }

        .login-subtitle {
            font-size: 0.85rem;
            margin-bottom: 1.5rem !important;
        }

        .form-control-lg {
            font-size: 1rem;
            padding: 0.6rem 0.75rem;
        }

        .btn-lg {
            font-size: 1rem;
            padding: 0.6rem 1rem;
        }

        .login-label {
            font-size: 0.85rem;
        }

        .login-alert {
            font-size: 0.8rem;
            padding: 0.6rem;
            margin-bottom: 1rem;
        }

        .mb-4 {
            margin-bottom: 1.25rem !important;
        }
    }

    .login-btn {
        transition: all 0.3s ease;
        font-weight: 500;
        letter-spacing: 0.5px;
    }

    .login-btn:disabled {
        opacity: 0.8;
        cursor: not-allowed;
    }

    .login-btn .spinner-border-sm {
        width: 1rem;
        height: 1rem;
        border-width: 0.2em;
    }

    .login-btn .spinner {
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 0.5rem;
    }

    @media (max-width: 360px) {
        .card-body {
            padding: 1.5rem 1rem !important;
        }

        .login-icon {
            font-size: 2rem;
            margin-bottom: 0.5rem !important;
        }

        .login-title {
            font-size: 1.25rem;
        }

        .form-control-lg,
        .btn-lg {
            font-size: 0.95rem;
            padding: 0.5rem 0.5rem;
        }
    }
</style>

<script>
    function togglePassword(inputId, btn) {
        const input = document.getElementById(inputId);
        const icon = btn.querySelector('i');
        if (input.type === 'password') {
            input.type = 'text';
            icon.classList.remove('fa-eye');
            icon.classList.add('fa-eye-slash');
        } else {
            input.type = 'password';
            icon.classList.remove('fa-eye-slash');
            icon.classList.add('fa-eye');
        }
    }

    // Add loading spinner on form submit
    document.querySelector('form').addEventListener('submit', function(e) {
        const btn = document.getElementById('loginBtn');
        const btnText = btn.querySelector('.btn-text');
        const spinner = btn.querySelector('.spinner');
        
        // Show spinner and hide text
        btnText.classList.add('d-none');
        spinner.classList.remove('d-none');
        
        // Disable button
        btn.disabled = true;
    });
</script>
@endsection
