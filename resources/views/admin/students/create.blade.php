@extends('layouts.app')

@section('title', 'Tambah Siswa - SIMagang')
@section('page-title', 'Tambah Siswa')

@section('content')
<div class="card shadow">
    <div class="card-header py-3 d-flex justify-content-between align-items-center">
        <h6 class="m-0 font-weight-bold text-primary">Form Tambah Siswa</h6>
        <a href="{{ route('admin.students.index') }}" class="btn btn-secondary btn-sm">
            <i class="fas fa-arrow-left me-1"></i>Kembali
        </a>
    </div>
    <div class="card-body">
        @if($errors->any())
            <div class="alert alert-danger">
                <ul class="mb-0">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form action="{{ route('admin.students.store') }}" method="POST">
            @csrf

            <div class="row">
                {{-- Data Akun --}}
                <div class="col-12">
                    <h6 class="text-primary fw-bold mb-3"><i class="fas fa-user-circle me-2"></i>Data Akun</h6>
                    <hr>
                </div>

                <div class="col-md-6 mb-3">
                    <label for="nama_lengkap" class="form-label">Nama Lengkap <span class="text-danger">*</span></label>
                    <input type="text" class="form-control" id="nama_lengkap" name="nama_lengkap" 
                           value="{{ old('nama_lengkap') }}" required>
                </div>

                <div class="col-md-6 mb-3">
                    <label for="username" class="form-label">Username <span class="text-danger">*</span></label>
                    <input type="text" class="form-control" id="username" name="username" 
                           value="{{ old('username') }}" required>
                </div>

                <div class="col-md-6 mb-3">
                    <label for="no_hp" class="form-label">No. WhatsApp <span class="text-danger">*</span></label>
                    <input type="text" class="form-control" id="no_hp" name="no_hp" 
                           value="{{ old('no_hp') }}" placeholder="08xxxxxxxxxx" required>
                    <small class="text-muted">Info login akun PKL akan dikirim otomatis ke nomor ini</small>
                </div>

                <div class="col-md-6 mb-3">
                    <label for="password" class="form-label">Password <span class="text-danger">*</span></label>
                    <div class="input-group">
                        <input type="password" class="form-control" id="password" name="password" required minlength="6">
                        <button class="btn btn-outline-secondary" type="button" onclick="togglePassword()">
                            <i class="fas fa-eye" id="toggleIcon"></i>
                        </button>
                    </div>
                    <small class="text-muted">Minimal 6 karakter</small>
                </div>
            </div>

            <hr>
            <div class="d-flex justify-content-end gap-2">
                <a href="{{ route('admin.students.index') }}" class="btn btn-secondary">
                    <i class="fas fa-times me-1"></i>Batal
                </a>
                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-save me-1"></i>Simpan
                </button>
            </div>
        </form>
    </div>
</div>

@push('scripts')
<script>
    function togglePassword() {
        const input = document.getElementById('password');
        const icon = document.getElementById('toggleIcon');
        if (input.type === 'password') {
            input.type = 'text';
            icon.classList.replace('fa-eye', 'fa-eye-slash');
        } else {
            input.type = 'password';
            icon.classList.replace('fa-eye-slash', 'fa-eye');
        }
    }
</script>
@endpush
@endsection
