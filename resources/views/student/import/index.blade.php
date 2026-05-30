@extends('layouts.app')

@section('title', 'Import Data - SIMagang')
@section('page-title', 'Import Data')

@section('content')
<div class="row justify-content-center">
    <div class="col-md-8">
        <div class="card shadow border-0">
            <div class="card-header bg-success text-white">
                <h5 class="mb-0"><i class="fas fa-file-csv me-2"></i>Import Data Pribadi & PKL</h5>
            </div>
            <div class="card-body">
                <p class="text-muted">
                    <i class="fas fa-info-circle me-1"></i>
                    Download template CSV yang sudah terisi dengan username Anda, lengkapi data pribadi dan PKL, lalu upload kembali.
                </p>

                <div class="alert alert-info d-flex align-items-center">
                    <i class="fas fa-lightbulb me-2 fa-lg"></i>
                    <div>
                        <strong>Kolom yang perlu diisi:</strong>
                        no_hp, institusi, jurusan_id, kelas_id, tanggal_mulai, tanggal_selesai,
                        alamat_magang, pembimbing_lapangan, gmap_magang, guru_pembimbing_id
                    </div>
                </div>

                <div class="d-grid gap-2 mb-4">
                    <a href="{{ route('student.import.template-data') }}" class="btn btn-outline-success">
                        <i class="fas fa-download me-2"></i>Download Template Data Saya
                    </a>
                </div>

                <hr>

                <form method="POST" action="{{ route('student.import.data') }}" enctype="multipart/form-data">
                    @csrf
                    <div class="mb-3">
                        <label for="file_data" class="form-label">Upload Template Data (.csv / .xlsx)</label>
                        <input type="file" class="form-control @error('file_data') is-invalid @enderror"
                               id="file_data" name="file_data" accept=".csv,.xlsx,.xls,.txt" required>
                        @error('file_data')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <button type="submit" class="btn btn-success w-100">
                        <i class="fas fa-file-import me-2"></i>Import Data Pribadi
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
