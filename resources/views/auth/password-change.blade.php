@extends('layouts.app')

@section('title', 'Edit Username & Password - SIMagang')
@section('page-title', 'Edit Username & Password')

@section('content')
<div class="row justify-content-center">
    <div class="col-md-6">
        <div class="card shadow">
            <div class="card-header bg-warning text-dark">
                <h5 class="mb-0"><i class="fas fa-user-cog me-2"></i>Edit Username & Password</h5>
            </div>
            <div class="card-body">
                <form method="POST" action="{{ route('password.change.post') }}">
                    @csrf

                    @if(auth()->user()->isSiswa())
                    <div class="mb-3">
                        <label for="username" class="form-label">Username <span class="text-danger">*</span></label>
                        <input type="text" class="form-control @error('username') is-invalid @enderror"
                               id="username" name="username" value="{{ old('username', auth()->user()->username) }}" required>
                        @error('username')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <hr class="my-4">
                    <p class="text-muted small mb-3">Kosongkan password baru jika hanya ingin mengubah username.</p>
                    @endif

                    <div class="mb-3">
                        <label for="current_password" class="form-label">Password Saat Ini <span class="text-danger">*</span></label>
                        <div class="input-group">
                            <input type="password" class="form-control @error('current_password') is-invalid @enderror"
                                   id="current_password" name="current_password" required>
                            <button type="button" class="btn btn-outline-secondary" onclick="togglePassword('current_password', this)">
                                <i class="fas fa-eye"></i>
                            </button>
                        </div>
                        @error('current_password')
                            <div class="invalid-feedback d-block">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label for="new_password" class="form-label">Password Baru @if(!auth()->user()->isSiswa())<span class="text-danger">*</span>@endif</label>
                        <div class="input-group">
                            <input type="password" class="form-control @error('new_password') is-invalid @enderror"
                                   id="new_password" name="new_password" {{ auth()->user()->isAdmin() ? 'required' : '' }}>
                            <button type="button" class="btn btn-outline-secondary" onclick="togglePassword('new_password', this)">
                                <i class="fas fa-eye"></i>
                            </button>
                        </div>
                        @error('new_password')
                            <div class="invalid-feedback d-block">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label for="new_password_confirmation" class="form-label">Konfirmasi Password Baru</label>
                        <div class="input-group">
                            <input type="password" class="form-control"
                                   id="new_password_confirmation" name="new_password_confirmation">
                            <button type="button" class="btn btn-outline-secondary" onclick="togglePassword('new_password_confirmation', this)">
                                <i class="fas fa-eye"></i>
                            </button>
                        </div>
                    </div>

                    <div class="d-flex gap-2">
                        <a href="{{ route('profile.show') }}" class="btn btn-secondary">
                            <i class="fas fa-arrow-left me-1"></i>Kembali
                        </a>
                        <button type="submit" class="btn btn-warning flex-grow-1">
                            <i class="fas fa-save me-2"></i>Simpan Perubahan
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
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
</script>
@endsection
