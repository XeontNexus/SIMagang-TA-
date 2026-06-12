@extends('layouts.app')

@section('title', 'Register - SIMagang')
@section('page-title', 'Pendaftaran Akun Siswa')

@section('content')
<div class="row justify-content-center">
    <div class="col-md-6">
        <div class="card shadow">
            <div class="card-header bg-success text-white">
                <h5 class="mb-0"><i class="fas fa-user-plus me-2"></i>Form Pendaftaran</h5>
            </div>
            <div class="card-body">
                <form method="POST" action="{{ route('register.post') }}" id="registerForm">
                    @csrf

                    <div class="mb-3">
                        <label for="nama_lengkap" class="form-label">Nama Lengkap *</label>
                        <input type="text" class="form-control @error('nama_lengkap') is-invalid @enderror" 
                               id="nama_lengkap" name="nama_lengkap" value="{{ old('nama_lengkap') }}" required>
                        @error('nama_lengkap')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label for="username" class="form-label">Username *</label>
                        <input type="text" class="form-control @error('username') is-invalid @enderror" 
                               id="username" name="username" value="{{ old('username') }}" required>
                        
                        <!-- Notifikasi peringatan jika username dan nama sama -->
                        <div id="duplicateWarning" class="alert alert-warning mt-2 d-none" role="alert">
                            <i class="fas fa-exclamation-triangle me-2"></i>
                            <strong>Perhatian:</strong> Kombinasi username dan nama sudah terdaftar! 
                            Silakan gunakan username yang berbeda untuk membedakan akun Anda.
                        </div>

                        @error('username')
                            <div class="invalid-feedback d-block">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label for="password" class="form-label">Password *</label>
                        <div class="input-group">
                            <input type="password" class="form-control @error('password') is-invalid @enderror" 
                                   id="password" name="password" required>
                            <button type="button" class="btn btn-outline-secondary" onclick="togglePassword('password', this)">
                                <i class="fas fa-eye"></i>
                            </button>
                        </div>
                        @error('password')
                            <div class="invalid-feedback d-block">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label for="password_confirmation" class="form-label">Konfirmasi Password *</label>
                        <div class="input-group">
                            <input type="password" class="form-control" 
                                   id="password_confirmation" name="password_confirmation" required>
                            <button type="button" class="btn btn-outline-secondary" onclick="togglePassword('password_confirmation', this)">
                                <i class="fas fa-eye"></i>
                            </button>
                        </div>
                    </div>

                    <div class="d-grid">
                        <button type="submit" class="btn btn-success">
                            <i class="fas fa-user-plus me-2"></i>Daftar
                        </button>
                    </div>

                    <div class="text-center mt-3">
                        <p>Sudah punya akun? <a href="{{ route('login') }}">Login di sini</a></p>
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

    // Real-time check untuk username dan nama_lengkap
    const usernameInput = document.getElementById('username');
    const namaLengkapInput = document.getElementById('nama_lengkap');
    const duplicateWarning = document.getElementById('duplicateWarning');

    async function checkDuplicateUsernameAndNama() {
        const username = usernameInput.value.trim();
        const namaLengkap = namaLengkapInput.value.trim();

        if (!username || !namaLengkap) {
            duplicateWarning.classList.add('d-none');
            return;
        }

        try {
            const response = await fetch('{{ route("register.check-duplicate") }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content || '',
                },
                body: JSON.stringify({ username, nama_lengkap: namaLengkap })
            });

            const data = await response.json();

            if (data.isDuplicate) {
                duplicateWarning.classList.remove('d-none');
                usernameInput.classList.add('is-invalid');
            } else {
                duplicateWarning.classList.add('d-none');
                usernameInput.classList.remove('is-invalid');
            }
        } catch (error) {
            console.error('Error checking duplicate:', error);
        }
    }

    // Listen to input changes
    usernameInput.addEventListener('input', checkDuplicateUsernameAndNama);
    namaLengkapInput.addEventListener('input', checkDuplicateUsernameAndNama);

    // Check on page load if form has old values
    window.addEventListener('load', checkDuplicateUsernameAndNama);
</script>
@endsection
