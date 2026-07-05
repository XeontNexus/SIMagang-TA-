@extends('layouts.app')

@section('title', 'Detail Siswa - SIMagang')
@section('page-title', 'Detail Siswa')

@section('content')
<div class="row">
    <div class="col-lg-4 mb-4">
        <div class="card shadow h-100">
            <div class="card-body text-center">
                <div class="mb-3">
                    @if($student->foto_profile)
                        <img src="{{ asset('storage/' . $student->foto_profile) }}" class="rounded-circle" width="120" height="120" style="object-fit: cover;">
                    @else
                        <div class="rounded-circle bg-primary d-inline-flex align-items-center justify-content-center" style="width: 120px; height: 120px;">
                            <i class="fas fa-user-graduate fa-3x text-white"></i>
                        </div>
                    @endif
                </div>
                <h5 class="fw-bold mb-1">{{ $student->nama_lengkap }}</h5>
                <p class="text-muted mb-2"><i class="fas fa-at me-1"></i>{{ $student->username }}</p>
                <span class="fs-6">@include('partials.student-status-badge', ['status' => $student->status])</span>
            </div>
        </div>
    </div>

    <div class="col-lg-8 mb-4">
        <div class="card shadow h-100">
            <div class="card-header py-3 d-flex justify-content-between align-items-center">
                <h6 class="m-0 font-weight-bold text-primary">Informasi Lengkap</h6>
                <div>
                    <a href="{{ route('admin.students.edit', $student) }}" class="btn btn-warning btn-sm"><i class="fas fa-edit me-1"></i>Edit</a>
                    <a href="{{ route('admin.students.index') }}" class="btn btn-secondary btn-sm"><i class="fas fa-arrow-left me-1"></i>Kembali</a>
                </div>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-12 mb-3">
                        <h6 class="text-primary fw-bold"><i class="fas fa-user me-2"></i>Data Pribadi</h6><hr class="mt-1">
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label text-muted small mb-0">Nama Lengkap</label>
                        <p class="fw-semibold mb-0">{{ $student->nama_lengkap }}</p>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label text-muted small mb-0">Username</label>
                        <p class="fw-semibold mb-0">{{ $student->username }}</p>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label text-muted small mb-0">Nomor Telepon</label>
                        <p class="fw-semibold mb-0"><i class="fas fa-phone me-1 text-primary"></i>{{ $student->no_hp ?? '-' }}</p>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label text-muted small mb-0">NISN</label>
                        <p class="fw-semibold mb-0">{{ $student->nisn ?? '-' }}</p>
                    </div>

                    <div class="col-12 mb-3 mt-2">
                        <h6 class="text-primary fw-bold"><i class="fas fa-school me-2"></i>Data Institusi</h6><hr class="mt-1">
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label text-muted small mb-0">Institusi/Sekolah</label>
                        <p class="fw-semibold mb-0">{{ $student->institusi ?? '-' }}</p>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label text-muted small mb-0">Jurusan</label>
                        <p class="fw-semibold mb-0">{{ $student->jurusan?->nama_jurusan ?? '-' }}</p>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label text-muted small mb-0">Kelas</label>
                        <p class="fw-semibold mb-0">{{ $student->kelas?->nama_kelas ?? '-' }}</p>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label text-muted small mb-0">Guru Pembimbing</label>
                        <p class="fw-semibold mb-0">{{ $student->guruPembimbing?->nama_guru ?? '-' }}</p>
                    </div>

                    <div class="col-12 mb-3 mt-2">
                        <h6 class="text-primary fw-bold"><i class="fas fa-briefcase me-2"></i>Data Magang</h6><hr class="mt-1">
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label text-muted small mb-0">Mitra/Perusahaan Magang</label>
                        <p class="fw-semibold mb-0">{{ $student->mitra_magang ?? '-' }}</p>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label text-muted small mb-0">Tanggal Mulai</label>
                        <p class="fw-semibold mb-0"><i class="fas fa-calendar me-1 text-muted"></i>{{ $student->tanggal_mulai ? $student->tanggal_mulai->format('d F Y') : '-' }}</p>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label text-muted small mb-0">Tanggal Selesai</label>
                        <p class="fw-semibold mb-0"><i class="fas fa-calendar-check me-1 text-muted"></i>{{ $student->tanggal_selesai ? $student->tanggal_selesai->format('d F Y') : '-' }}</p>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label text-muted small mb-0">Pembimbing Lapangan</label>
                        <p class="fw-semibold mb-0">{{ $student->pembimbing_lapangan ?? '-' }}</p>
                    </div>
                    <div class="col-12 mb-3">
                        <label class="form-label text-muted small mb-0">Alamat Tempat Magang</label>
                        <p class="fw-semibold mb-0">{{ $student->alamat_magang ?? '-' }}</p>
                    </div>
                    @if($student->gmap_magang)
                    <div class="col-12 mb-3 mt-3">
                        <h6 class="text-primary fw-bold"><i class="fas fa-map-marker-alt me-2"></i>Lokasi Mitra di Peta (Google Maps)</h6><hr class="mt-1">
                        <div class="mb-2">
                            <span class="badge bg-{{ $student->latitude ? 'success' : 'danger' }}">
                                {{ $student->latitude ? 'Koordinat Valid (Diekstrak otomatis)' : 'Koordinat Gagal Diekstrak' }}
                            </span>
                            @if($student->latitude)
                                <small class="text-muted ms-2">Lat: {{ $student->latitude }}, Lng: {{ $student->longitude }}</small>
                            @endif
                        </div>
                        <p class="small mb-2">
                            <strong>Link GMap Asli:</strong> <a href="{{ $student->gmap_magang }}" target="_blank">{{ $student->gmap_magang }}</a>
                        </p>
                        @if($student->latitude)
                        <div class="ratio ratio-16x9 border rounded overflow-hidden mt-3">
                            <iframe 
                                src="https://maps.google.com/maps?q={{ $student->latitude }},{{ $student->longitude }}&hl=id&z=15&output=embed" 
                                width="100%" 
                                height="100%" 
                                style="border:0;" 
                                allowfullscreen="" 
                                loading="lazy">
                            </iframe>
                        </div>
                        @else
                        <div class="alert alert-warning small">
                            Siswa telah memberikan Link Google Maps, namun sistem belum berhasil mengekstrak titik koordinat. Siswa mungkin perlu mengupdate link GMap yang lebih spesifik.
                        </div>
                        @endif
                    </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-md-6 mb-4">
        <div class="card shadow card-stats primary">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <div class="text-xs text-uppercase text-muted fw-bold">Total Presensi</div>
                        <div class="h4 mb-0 fw-bold">{{ $student->presensis->count() }}</div>
                    </div>
                    <div class="text-primary"><i class="fas fa-clipboard-check fa-2x"></i></div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-6 mb-4">
        <div class="card shadow card-stats info">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <div class="text-xs text-uppercase text-muted fw-bold">Total Logbook</div>
                        <div class="h4 mb-0 fw-bold">{{ $student->logbooks->count() }}</div>
                    </div>
                    <div class="text-info"><i class="fas fa-book fa-2x"></i></div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-md-3 col-sm-6 mb-4">
        <div class="card shadow card-stats success">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <div class="text-xs text-uppercase text-muted fw-bold">Hadir</div>
                        <div class="h4 mb-0 fw-bold text-success">{{ $student->presensis->where('status', 'hadir')->count() }}</div>
                    </div>
                    <div class="text-success"><i class="fas fa-check-circle fa-2x"></i></div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-3 col-sm-6 mb-4">
        <div class="card shadow card-stats warning">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <div class="text-xs text-uppercase text-muted fw-bold">Sakit</div>
                        <div class="h4 mb-0 fw-bold text-warning">{{ $student->presensis->where('status', 'sakit')->count() }}</div>
                    </div>
                    <div class="text-warning"><i class="fas fa-hospital-user fa-2x"></i></div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-3 col-sm-6 mb-4">
        <div class="card shadow card-stats info">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <div class="text-xs text-uppercase text-muted fw-bold">Izin</div>
                        <div class="h4 mb-0 fw-bold text-info">{{ $student->presensis->where('status', 'izin')->count() }}</div>
                    </div>
                    <div class="text-info"><i class="fas fa-envelope-open-text fa-2x"></i></div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-3 col-sm-6 mb-4">
        <div class="card shadow card-stats danger">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <div class="text-xs text-uppercase text-muted fw-bold">Alpha</div>
                        <div class="h4 mb-0 fw-bold text-danger">{{ $student->presensis->where('status', 'alpha')->count() }}</div>
                    </div>
                    <div class="text-danger"><i class="fas fa-times-circle fa-2x"></i></div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="card shadow mb-4">
    <div class="card-header py-3">
        <h6 class="m-0 font-weight-bold text-primary"><i class="fas fa-info-circle me-2"></i>Info Akun</h6>
    </div>
    <div class="card-body">
        <div class="row">
            <div class="col-md-4 mb-2">
                <label class="form-label text-muted small mb-0">Tanggal Registrasi</label>
                <p class="fw-semibold mb-0">{{ $student->created_at->format('d F Y, H:i') }}</p>
            </div>
            <div class="col-md-4 mb-2">
                <label class="form-label text-muted small mb-0">Terakhir Diupdate</label>
                <p class="fw-semibold mb-0">{{ $student->updated_at->format('d F Y, H:i') }}</p>
            </div>
            <div class="col-md-4 mb-2">
                <label class="form-label text-muted small mb-0">Status Akun</label>
                @include('partials.student-status-badge', ['status' => $student->status])
            </div>
        </div>
    </div>
</div>
@endsection
