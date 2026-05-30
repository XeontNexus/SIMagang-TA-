@extends('layouts.app')

@section('title', 'Lupa Password - SIMagang')

@section('content')
<style>
    html, body {
        background: #0a1628 !important;
        height: 100%;
        overflow: hidden;
    }
    .main-content {
        margin-left: 0 !important;
        margin-top: 0 !important;
        height: 100vh !important;
        width: 100% !important;
        display: flex;
        align-items: center;
        justify-content: center;
        overflow: hidden;
    }
    .card {
        border-radius: 15px;
        width: 100%;
        max-width: 450px;
    }
    .container {
        width: 100%;
        max-width: 100%;
        padding: 0 15px;
    }
    .row {
        width: 100%;
        justify-content: center;
        margin: 0;
    }
    .col-md-5 {
        flex: 0 0 auto;
        width: 100%;
        max-width: 450px;
    }
</style>

<div class="container">
    <div class="row justify-content-center min-vh-100 align-items-center">
        <div class="col-md-5">
            <div class="card shadow-lg border-0">
                <div class="card-body p-5">
                    <div class="text-center mb-4">
                        <i class="fas fa-key fa-3x text-primary mb-3"></i>
                        <h3 class="fw-bold">Lupa Password</h3>
                        <p class="text-muted">Masukkan email Anda untuk menerima link reset password</p>
                    </div>
                    
                    @if(session('status'))
                        <div class="alert alert-success" role="alert">
                            {{ session('status') }}
                        </div>
                    @endif
                    
                    <form method="POST" action="{{ route('password.email') }}">
                        @csrf
                        
                        <div class="mb-3">
                            <label for="email" class="form-label">Email</label>
                            <input type="email" class="form-control @error('email') is-invalid @enderror" 
                                   id="email" name="email" value="{{ old('email') }}" required autofocus>
                            @error('email')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        
                        <div class="d-grid mb-3">
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-paper-plane me-2"></i>Kirim Link Reset Password
                            </button>
                        </div>
                        
                        <div class="text-center">
                            <a href="{{ route('login') }}" class="text-decoration-none">
                                <i class="fas fa-arrow-left me-1"></i>Kembali ke Login
                            </a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
