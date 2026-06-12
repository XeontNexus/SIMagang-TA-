@extends('layouts.app')

@section('title', 'Import Data - SIMagang')
@section('page-title', 'Import Data')

@section('content')
<div class="row">
    <!-- Template Pembuatan Akun -->
    <div class="col-md-8 mx-auto mb-4">
        <div class="card shadow border-0">
            <div class="card-header bg-primary text-white">
                <h5 class="mb-0"><i class="fas fa-user-plus me-2"></i>Template Pembuatan Akun</h5>
            </div>
            <div class="card-body">
                <p class="text-muted">
                    <i class="fas fa-info-circle me-1"></i>
                    Download template Excel untuk membuat akun siswa secara massal. Isi kolom <strong>username, nisn, nama_lengkap, no_hp</strong> lalu upload kembali. Kolom <strong>nisn</strong> akan otomatis disimpan sebagai NISN siswa sekaligus password akunnya. Setelah terdaftar, Anda dapat mengirim informasi login secara manual via WhatsApp.
                </p>

                <div class="d-grid gap-2 mb-3">
                    <a href="{{ route('admin.import.template-akun') }}" class="btn btn-outline-primary" download="template_pembuatan_akun.xlsx">
                        <i class="fas fa-download me-2"></i>Download Template Akun
                    </a>
                </div>

                <hr>

                <form method="POST" action="{{ route('admin.import.akun') }}" enctype="multipart/form-data">
                    @csrf
                    <div class="mb-3">
                        <label for="file_akun" class="form-label">Upload Template Akun (.csv / .xlsx)</label>
                        <input type="file" class="form-control @error('file_akun') is-invalid @enderror"
                               id="file_akun" name="file_akun" accept=".csv,.xlsx,.xls,.txt" required>
                        @error('file_akun')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <button type="submit" class="btn btn-primary w-100">
                        <i class="fas fa-file-import me-2"></i>Import Akun
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- Error Details -->
@if(session('import_errors') && count(session('import_errors')) > 0)
<div class="card shadow border-0 mt-3">
    <div class="card-header bg-warning text-dark">
        <h6 class="mb-0"><i class="fas fa-exclamation-triangle me-2"></i>Detail Error Import</h6>
    </div>
    <div class="card-body">
        <ul class="list-group list-group-flush">
            @foreach(session('import_errors') as $error)
            <li class="list-group-item text-danger">{{ $error }}</li>
            @endforeach
        </ul>
    </div>
</div>
@endif
@endsection
