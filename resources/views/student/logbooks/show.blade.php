@extends('layouts.app')

@section('title', 'Detail Logbook - SIMagang')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3 mb-0 text-gray-800">Detail Logbook</h1>
        <div>
            <a href="{{ route('student.logbooks.index') }}" class="btn btn-secondary">
                <i class="fas fa-arrow-left me-2"></i>Kembali
            </a>
            @if($logbook->status == 'draft')
                <a href="{{ route('student.logbooks.edit', $logbook) }}" class="btn btn-warning">
                    <i class="fas fa-edit me-2"></i>Edit
                </a>
            @endif
        </div>
    </div>

    <div class="row">
        <div class="col-lg-8">
            <div class="card shadow mb-4">
                <div class="card-header py-3 d-flex justify-content-between align-items-center">
                    <h6 class="m-0 font-weight-bold text-primary">Informasi Logbook</h6>
                    <span class="badge bg-{{ $logbook->status == 'approved' ? 'success' : ($logbook->status == 'submitted' ? 'info' : ($logbook->status == 'rejected' ? 'danger' : 'secondary')) }}">
                        {{ ucfirst($logbook->status) }}
                    </span>
                </div>
                <div class="card-body">
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label class="text-muted">Minggu Ke</label>
                            <p class="fw-bold">{{ $logbook->minggu_ke }}</p>
                        </div>
                        <div class="col-md-6">
                            <label class="text-muted">Periode</label>
                            <p class="fw-bold">{{ $logbook->tanggal_mulai->format('d/m/Y') }} - {{ $logbook->tanggal_selesai->format('d/m/Y') }}</p>
                        </div>
                    </div>

                    <hr>

                    <div class="mb-3">
                        <label class="text-muted">Kegiatan</label>
                        <p class="fw-bold">{{ $logbook->kegiatan }}</p>
                    </div>

                    <div class="mb-3">
                        <label class="text-muted">Deskripsi</label>
                        <p>{{ $logbook->deskripsi }}</p>
                    </div>

                    <div class="mb-3">
                        <label class="text-muted">Hasil</label>
                        <p>{{ $logbook->hasil }}</p>
                    </div>

                    @if($logbook->kendala)
                    <div class="mb-3">
                        <label class="text-muted">Kendala</label>
                        <p class="text-danger">{{ $logbook->kendala }}</p>
                    </div>
                    @endif

                    @if($logbook->solusi)
                    <div class="mb-3">
                        <label class="text-muted">Solusi</label>
                        <p class="text-success">{{ $logbook->solusi }}</p>
                    </div>
                    @endif

                    @if($logbook->catatan_admin)
                    <hr>
                    <div class="alert alert-info">
                        <h6 class="alert-heading"><i class="fas fa-info-circle me-2"></i>Catatan Admin</h6>
                        <p class="mb-0">{{ $logbook->catatan_admin }}</p>
                    </div>
                    @endif
                </div>
            </div>
        </div>

        <div class="col-lg-4">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Status Logbook</h6>
                </div>
                <div class="card-body">
                    <div class="text-center mb-3">
                        @if($logbook->status == 'draft')
                            <i class="fas fa-file-alt fa-3x text-secondary mb-3"></i>
                            <h5>Draft</h5>
                            <p class="text-muted">Logbook masih dalam status draft. Silakan submit untuk approval.</p>
                        @elseif($logbook->status == 'submitted')
                            <i class="fas fa-clock fa-3x text-info mb-3"></i>
                            <h5>Submitted</h5>
                            <p class="text-muted">Logbook sedang menunggu review dari admin.</p>
                        @elseif($logbook->status == 'approved')
                            <i class="fas fa-check-circle fa-3x text-success mb-3"></i>
                            <h5>Approved</h5>
                            <p class="text-muted">Logbook telah disetujui oleh admin.</p>
                        @elseif($logbook->status == 'rejected')
                            <i class="fas fa-times-circle fa-3x text-danger mb-3"></i>
                            <h5>Rejected</h5>
                            <p class="text-muted">Logbook ditolak. Silakan perbaiki dan submit ulang.</p>
                        @endif
                    </div>

                    @if($logbook->status == 'draft')
                        <form action="{{ route('student.logbooks.submit', $logbook) }}" method="POST" onsubmit="return confirm('Yakin ingin submit logbook ini? Setelah submit tidak bisa diedit lagi.')">
                            @csrf
                            <button type="submit" class="btn btn-primary w-100">
                                <i class="fas fa-paper-plane me-2"></i>Submit Logbook
                            </button>
                        </form>
                    @endif
                </div>
            </div>

            <div class="card shadow">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Informasi Tambahan</h6>
                </div>
                <div class="card-body">
                    <p class="mb-1"><strong>Dibuat:</strong></p>
                    <p class="text-muted">{{ $logbook->created_at->format('d F Y H:i') }}</p>

                    <p class="mb-1"><strong>Terakhir Diupdate:</strong></p>
                    <p class="text-muted">{{ $logbook->updated_at->format('d F Y H:i') }}</p>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
