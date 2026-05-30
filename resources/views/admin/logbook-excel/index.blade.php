@extends('layouts.app')

@section('title', 'Excel Logbook - SIMagang')
@section('page-title', 'Kelola Excel Logbook')

@section('content')
<div class="row">
    <div class="col-lg-4 mb-4">
        <div class="card shadow">
            <div class="card-header bg-primary text-white">
                <h6 class="mb-0"><i class="fas fa-link me-2"></i>Link Google Sheets / Excel Online</h6>
            </div>
            <div class="card-body">
                <p class="text-muted small">
                    Tempel link Google Sheets yang dibagikan. Siswa hanya dapat <strong>melihat</strong> (realtime).
                    Admin dapat mengedit langsung di panel kanan.
                </p>
                <form action="{{ route('admin.logbook-excel.update') }}" method="POST">
                    @csrf
                    @method('PUT')
                    <div class="mb-3">
                        <label for="logbook_excel_url" class="form-label">URL Spreadsheet <span class="text-danger">*</span></label>
                        <input type="url" name="logbook_excel_url" id="logbook_excel_url" class="form-control @error('logbook_excel_url') is-invalid @enderror"
                               value="{{ old('logbook_excel_url', $excelUrl) }}"
                               placeholder="https://docs.google.com/spreadsheets/d/..." required>
                        @error('logbook_excel_url')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                        <small class="text-muted">Gunakan link share Google Sheets (akses: siapa saja dengan link dapat melihat/edit sesuai pengaturan sheet).</small>
                    </div>
                    <button type="submit" class="btn btn-primary w-100">
                        <i class="fas fa-save me-1"></i>Simpan Link
                    </button>
                </form>
            </div>
        </div>
    </div>

    <div class="col-lg-8 mb-4">
        <div class="card shadow h-100">
            <div class="card-header py-3 d-flex justify-content-between align-items-center">
                <h6 class="m-0 font-weight-bold text-primary"><i class="fas fa-table me-2"></i>Preview & Edit (Admin)</h6>
                @if($embedUrl)
                    <a href="{{ $excelUrl }}" target="_blank" class="btn btn-sm btn-outline-primary">
                        <i class="fas fa-external-link-alt me-1"></i>Buka di Tab Baru
                    </a>
                @endif
            </div>
            <div class="card-body p-0">
                @if($embedUrl)
                    <div style="height: 75vh;">
                        <iframe src="{{ $embedUrl }}" width="100%" height="100%" frameborder="0" style="border:0;" allowfullscreen loading="lazy"></iframe>
                    </div>
                @else
                    <div class="text-center text-muted py-5">
                        <i class="fas fa-file-excel fa-3x mb-3"></i>
                        <p>Belum ada link Excel Logbook. Simpan link di form sebelah kiri.</p>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection
