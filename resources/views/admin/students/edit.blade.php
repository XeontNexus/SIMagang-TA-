@extends('layouts.app')

@section('title', 'Edit Siswa - SIMagang')
@section('page-title', 'Edit Siswa')

@section('content')
<div class="card shadow">
    <div class="card-header py-3 d-flex justify-content-between align-items-center">
        <h6 class="m-0 font-weight-bold text-primary">Edit Akun Siswa: {{ $student->nama_lengkap }}</h6>
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

        <div class="alert alert-info">
            <i class="fas fa-info-circle me-1"></i>
            Data institusi & magang diisi oleh siswa melalui menu <strong>Profile</strong> di akun mereka.
            Status diperbarui otomatis: <em>Menunggu</em> → <em>Proses</em> → <em>Aktif</em>.
        </div>

        <div class="mb-4">
            <label class="form-label text-muted small mb-1">Status Saat Ini</label>
            <div>@include('partials.student-status-badge', ['status' => $student->status])</div>
        </div>

        <form action="{{ route('admin.students.update', $student) }}" method="POST">
            @csrf
            @method('PUT')

            <div class="row">
                <div class="col-12">
                    <h6 class="text-primary fw-bold mb-3"><i class="fas fa-user-circle me-2"></i>Data Akun</h6><hr>
                </div>

                <div class="col-md-6 mb-3">
                    <label for="nama_lengkap" class="form-label">Nama Lengkap <span class="text-danger">*</span></label>
                    <input type="text" class="form-control" id="nama_lengkap" name="nama_lengkap" value="{{ old('nama_lengkap', $student->nama_lengkap) }}" required>
                </div>

                <div class="col-md-6 mb-3">
                    <label for="username" class="form-label">Username <span class="text-danger">*</span></label>
                    <input type="text" class="form-control" id="username" name="username" value="{{ old('username', $student->username) }}" required>
                </div>

                <div class="col-md-6 mb-3">
                    <label for="no_hp" class="form-label">No. WhatsApp <span class="text-danger">*</span></label>
                    <input type="text" class="form-control" id="no_hp" name="no_hp" value="{{ old('no_hp', $student->no_hp) }}" placeholder="08xxxxxxxxxx" required>
                </div>

                <div class="col-md-6 mb-3">
                    <label for="password" class="form-label">Password Baru</label>
                    <input type="password" class="form-control" id="password" name="password" placeholder="Kosongkan jika tidak ingin mengubah">
                    <small class="text-muted">Kosongkan jika tidak ingin mengubah password</small>
                </div>
            </div>

            <hr>
            <div class="d-flex justify-content-end gap-2">
                <a href="{{ route('admin.students.index') }}" class="btn btn-secondary"><i class="fas fa-times me-1"></i>Batal</a>
                <button type="submit" class="btn btn-primary"><i class="fas fa-save me-1"></i>Update</button>
            </div>
        </form>
    </div>
</div>
@endsection
