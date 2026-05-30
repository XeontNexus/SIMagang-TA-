@extends('layouts.app')

@section('title', 'Profil Saya - SIMagang')
@section('page-title', 'Profil Saya')

@section('content')
<div class="row justify-content-center">
    <div class="col-md-8">
        <!-- Data Profil -->
        <div class="card shadow mb-4">
            <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center">
                <h5 class="mb-0"><i class="fas fa-user me-2"></i>Data Profil</h5>
                <a href="{{ route('profile.edit') }}" class="btn btn-light btn-sm">
                    <i class="fas fa-edit me-1"></i>Edit
                </a>
            </div>
            <div class="card-body">
                <div class="row mb-3">
                    <div class="col-md-4 fw-bold">Nama Lengkap</div>
                    <div class="col-md-8">{{ auth()->user()->nama_lengkap }}</div>
                </div>
                <div class="row mb-3">
                    <div class="col-md-4 fw-bold">Username</div>
                    <div class="col-md-8">{{ auth()->user()->username }}</div>
                </div>
                @if(auth()->user()->isSiswa())
                <div class="row mb-3">
                    <div class="col-md-4 fw-bold">NISN</div>
                    <div class="col-md-8">{{ auth()->user()->nisn ?? '-' }}</div>
                </div>
                <div class="row mb-3">
                    <div class="col-md-4 fw-bold">No. HP</div>
                    <div class="col-md-8">{{ auth()->user()->no_hp ?? '-' }}</div>
                </div>
                <div class="row mb-3">
                    <div class="col-md-4 fw-bold">Jurusan</div>
                    <div class="col-md-8">{{ auth()->user()->jurusan()->first()?->nama_jurusan ?? auth()->user()->jurusan ?? '-' }}</div>
                </div>
                <div class="row mb-3">
                    <div class="col-md-4 fw-bold">Kelas</div>
                    <div class="col-md-8">{{ auth()->user()->kelas()->first()?->nama_kelas ?? auth()->user()->kelas ?? '-' }}</div>
                </div>
                @else
                <div class="row mb-3">
                    <div class="col-md-4 fw-bold">No. HP</div>
                    <div class="col-md-8">{{ auth()->user()->no_hp ?? '-' }}</div>
                </div>
                @endif
                <div class="row mb-3">
                    <div class="col-md-4 fw-bold">Status</div>
                    <div class="col-md-8">
                        @include('partials.student-status-badge', ['status' => auth()->user()->status])
                    </div>
                </div>
                <div class="row mb-3">
                    <div class="col-md-4 fw-bold">Role</div>
                    <div class="col-md-8">{{ ucfirst(auth()->user()->role) }}</div>
                </div>
            </div>
        </div>

        @if(auth()->user()->isSiswa())
        <!-- Data Magang Mitra -->
        <div class="card shadow mb-4">
            <div class="card-header bg-info text-white d-flex justify-content-between align-items-center">
                <h5 class="mb-0"><i class="fas fa-briefcase me-2"></i>Data Magang Mitra</h5>
                <a href="{{ route('profile.edit') }}" class="btn btn-light btn-sm">
                    <i class="fas fa-edit me-1"></i>Edit
                </a>
            </div>
            <div class="card-body">
                <div class="row mb-3">
                    <div class="col-md-4 fw-bold">Nama Perusahaan/Mitra</div>
                    <div class="col-md-8">{{ auth()->user()->mitra_magang ?? '-' }}</div>
                </div>
                <div class="row mb-3">
                    <div class="col-md-4 fw-bold">Alamat Magang</div>
                    <div class="col-md-8">{{ auth()->user()->alamat_magang ?? '-' }}</div>
                </div>
                <div class="row mb-3">
                    <div class="col-md-4 fw-bold">Tanggal Mulai</div>
                    <div class="col-md-8">{{ auth()->user()->tanggal_mulai ? auth()->user()->tanggal_mulai->format('d F Y') : '-' }}</div>
                </div>
                <div class="row mb-3">
                    <div class="col-md-4 fw-bold">Tanggal Selesai</div>
                    <div class="col-md-8">{{ auth()->user()->tanggal_selesai ? auth()->user()->tanggal_selesai->format('d F Y') : '-' }}</div>
                </div>
                <div class="row mb-3">
                    <div class="col-md-4 fw-bold">Pembimbing Lapangan</div>
                    <div class="col-md-8">{{ auth()->user()->pembimbing_lapangan ?? '-' }}</div>
                </div>
                <div class="row mb-3">
                    <div class="col-md-4 fw-bold">No. HP Pembimbing Lapangan</div>
                    <div class="col-md-8">{{ auth()->user()->no_hp_pembimbing_lapangan ?? '-' }}</div>
                </div>
                <div class="row mb-3">
                    <div class="col-md-4 fw-bold">Guru Pembimbing</div>
                    <div class="col-md-8">{{ auth()->user()->guruPembimbing->nama_guru ?? '-' }}</div>
                </div>
                <div class="row mb-3">
                    <div class="col-md-4 fw-bold">No. HP Guru Pembimbing</div>
                    <div class="col-md-8">{{ auth()->user()->no_hp_guru_pembimbing ?? '-' }}</div>
                </div>
            </div>
        </div>
        @endif

        <!-- Tombol Edit Username & Password -->
        <div class="card shadow">
            <div class="card-body">
                <div class="d-grid">
                    <a href="{{ route('password.change') }}" class="btn btn-warning">
                        <i class="fas fa-user-cog me-2"></i>Edit Username & Password
                    </a>
                </div>
            </div>
        </div>

        @if(auth()->user()->isAdmin())
        <!-- Cetak Dokumentasi Siswa -->
        <div class="card shadow mt-4">
            <div class="card-header bg-success text-white">
                <h5 class="mb-0"><i class="fas fa-file-export me-2"></i>Cetak Data Seluruh Siswa</h5>
            </div>
            <div class="card-body">
                <p class="text-muted mb-3">Unduh seluruh data dokumentasi siswa (profil, penempatan, dan status) dalam format Excel atau Word.</p>
                <div class="row">
                    <div class="col-6">
                        <a href="{{ route('admin.export.excel') }}" class="btn btn-success w-100">
                            <i class="fas fa-file-excel me-2"></i>Export Excel
                        </a>
                    </div>
                    <div class="col-6">
                        <a href="{{ route('admin.export.word') }}" class="btn btn-primary w-100">
                            <i class="fas fa-file-word me-2"></i>Export Word
                        </a>
                    </div>
                </div>
            </div>
        </div>
        @endif
    </div>
</div>
@endsection
