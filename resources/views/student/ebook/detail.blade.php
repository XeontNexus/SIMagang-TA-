@extends('layouts.app')

@section('title', 'E-Book - '.$user->nama_lengkap)
@section('page-title', 'E-Book')

@section('content')
<div class="card shadow mb-4">
    <div class="card-header py-3 bg-info text-white d-flex justify-content-between align-items-center">
        <div>
            <h6 class="m-0 font-weight-bold"><i class="fas fa-book-reader me-2"></i>Laporan Akhir - {{ $user->nama_lengkap }}</h6>
        </div>
        <a href="{{ route('student.ebook.index') }}" class="btn btn-light btn-sm">
            <i class="fas fa-arrow-left me-1"></i>Kembali
        </a>
    </div>
    <div class="card-body">
        <div class="row mb-4">
            <div class="col-md-6">
                <div class="d-flex align-items-center">
                    @if($user->foto_profile)
                        <img src="{{ asset('storage/'.$user->foto_profile) }}" class="rounded-circle me-3" width="80" height="80" alt="{{ $user->nama_lengkap }}">
                    @else
                        <div class="bg-primary text-white rounded-circle d-flex align-items-center justify-content-center me-3" style="width:80px;height:80px">
                            <i class="fas fa-user fa-3x"></i>
                        </div>
                    @endif
                    <div>
                        <h5 class="mb-1 fw-bold">{{ $user->nama_lengkap }}</h5>
                        <p class="mb-0 text-muted">{{ $user->kelas?->nama_kelas ?? '-' }} | {{ $user->jurusan?->nama_jurusan ?? '-' }}</p>
                        <p class="mb-0 text-muted small"><i class="fas fa-building me-1"></i>{{ $user->institusi ?? '-' }}</p>
                    </div>
                </div>
            </div>
            <div class="col-md-6 text-md-end">
                <div class="badge bg-success mb-1">{{ $logbooks->count() }} Logbook Approved</div>
                <p class="mb-0 text-muted small">
                    <i class="fas fa-calendar-alt me-1"></i>
                    {{ $user->tanggal_mulai ? $user->tanggal_mulai->format('d M Y') : '-' }} - {{ $user->tanggal_selesai ? $user->tanggal_selesai->format('d M Y') : '-' }}
                </p>
            </div>
        </div>

        @if($logbooks->count() > 0)
        <div class="accordion" id="logbookAccordion">
            @foreach($logbooks as $index => $logbook)
            <div class="accordion-item">
                <h2 class="accordion-header">
                    <button class="accordion-button {{ $index > 0 ? 'collapsed' : '' }}" type="button" data-bs-toggle="collapse" data-bs-target="#collapse{{ $logbook->id }}">
                        <i class="fas fa-book me-2"></i>Minggu Ke-{{ $logbook->minggu_ke }}
                        <span class="ms-2 badge bg-success">Approved</span>
                    </button>
                </h2>
                <div id="collapse{{ $logbook->id }}" class="accordion-collapse collapse {{ $index === 0 ? 'show' : '' }}" data-bs-parent="#logbookAccordion">
                    <div class="accordion-body">
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label class="text-muted small">Periode</label>
                                <p class="mb-0">
                                    {{ $logbook->tanggal_mulai->format('d M Y') }} - {{ $logbook->tanggal_selesai->format('d M Y') }}
                                </p>
                            </div>
                            <div class="col-md-6">
                                <label class="text-muted small">Kegiatan</label>
                                <p class="mb-0">{{ $logbook->kegiatan }}</p>
                            </div>
                        </div>
                        @if($logbook->deskripsi)
                        <div class="mb-3">
                            <label class="text-muted small">Deskripsi</label>
                            <p class="mb-0">{{ $logbook->deskripsi }}</p>
                        </div>
                        @endif
                        @if($logbook->hasil)
                        <div class="mb-3">
                            <label class="text-muted small">Hasil</label>
                            <p class="mb-0">{{ $logbook->hasil }}</p>
                        </div>
                        @endif
                        @if($logbook->kendala)
                        <div class="mb-3">
                            <label class="text-muted small">Kendala</label>
                            <p class="mb-0">{{ $logbook->kendala }}</p>
                        </div>
                        @endif
                        @if($logbook->solusi)
                        <div class="mb-3">
                            <label class="text-muted small">Solusi</label>
                            <p class="mb-0">{{ $logbook->solusi }}</p>
                        </div>
                        @endif
                    </div>
                </div>
            </div>
            @endforeach
        </div>
        @else
        <div class="text-center py-5">
            <i class="fas fa-book-open fa-4x text-muted mb-3"></i>
            <h5 class="text-muted">Belum Ada Logbook</h5>
            <p class="text-muted">Kakak kelas ini belum memiliki logbook yang disetujui.</p>
        </div>
        @endif
    </div>
</div>
@endsection
