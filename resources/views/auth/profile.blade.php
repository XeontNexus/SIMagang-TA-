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
                    <div class="col-md-8">{{ auth()->user()->jurusan?->nama_jurusan ?? '-' }}</div>
                </div>
                <div class="row mb-3">
                    <div class="col-md-4 fw-bold">Kelas</div>
                    <div class="col-md-8">{{ auth()->user()->kelas?->nama_kelas ?? '-' }}</div>
                </div>
                @else
                <div class="row mb-3">
                    <div class="col-md-4 fw-bold">Email</div>
                    <div class="col-md-8">{{ auth()->user()->email ?? '-' }}</div>
                </div>
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
        <div class="card shadow mt-4">
            <div class="card-header bg-success text-white">
                <h5 class="mb-0"><i class="fas fa-file-export me-2"></i>Export Data Excel</h5>
            </div>
            <div class="card-body">
                @if(session('error'))
                    <div class="alert alert-danger">{{ session('error') }}</div>
                @endif

                <p class="text-muted mb-4">Unduh data siswa dan laporan logbook dalam format Excel (.xlsx).</p>

                <div class="mb-4">
                    <label class="form-label fw-semibold"><i class="fas fa-users me-1"></i> Semua Data Siswa</label>
                    <p class="small text-muted mb-2">Profil, penempatan magang, dan status seluruh siswa.</p>
                    <a href="{{ route('admin.export.excel') }}" class="btn btn-success">
                        <i class="fas fa-file-excel me-2"></i>Export Semua Siswa
                    </a>
                </div>

                <hr>

                <form action="{{ route('admin.export.guru') }}" method="GET" class="mb-4">
                    <label class="form-label fw-semibold"><i class="fas fa-chalkboard-teacher me-1"></i> Export per Guru Pembimbing</label>
                    <p class="small text-muted mb-2">Cetak data siswa yang dibimbing oleh guru yang dipilih.</p>
                    <div class="row g-2 align-items-end">
                        <div class="col-md-8">
                            <select name="guru_pembimbing_id" class="form-select" required>
                                <option value="">-- Pilih Guru Pembimbing --</option>
                                @foreach($exportGuruList as $guru)
                                    <option value="{{ $guru->id }}">{{ $guru->nama_guru }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-4">
                            <button type="submit" class="btn btn-success w-100">
                                <i class="fas fa-download me-1"></i>Export
                            </button>
                        </div>
                    </div>
                </form>

                <form action="{{ route('admin.export.kelas') }}" method="GET" class="mb-4">
                    <label class="form-label fw-semibold"><i class="fas fa-chalkboard me-1"></i> Export per Kelas</label>
                    <p class="small text-muted mb-2">Pilih kelas yang ingin diekspor.</p>
                    <div class="row g-2 align-items-end">
                        <div class="col-md-8">
                            <select name="kelas_id" class="form-select" required>
                                <option value="">-- Pilih Kelas --</option>
                                @foreach($exportKelasList as $kelas)
                                    <option value="{{ $kelas->id }}">
                                        {{ $kelas->tingkat }} {{ $kelas->nama_kelas }}
                                        @if($kelas->jurusan) ({{ $kelas->jurusan->nama_jurusan }}) @endif
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-4">
                            <button type="submit" class="btn btn-success w-100">
                                <i class="fas fa-download me-1"></i>Export
                            </button>
                        </div>
                    </div>
                </form>

                <hr>

                <form action="{{ route('admin.export.logbooks') }}" method="GET">
                    <label class="form-label fw-semibold"><i class="fas fa-book me-1"></i> Export Laporan Logbook</label>
                    <p class="small text-muted mb-2">Unduh data logbook siswa. Kosongkan filter untuk semua data.</p>
                    <div class="row g-2 mb-2">
                        <div class="col-md-6">
                            <select name="kelas_id" class="form-select">
                                <option value="">Semua Kelas</option>
                                @foreach($exportKelasList as $kelas)
                                    <option value="{{ $kelas->id }}">
                                        {{ $kelas->tingkat }} {{ $kelas->nama_kelas }}
                                        @if($kelas->jurusan) ({{ $kelas->jurusan->nama_jurusan }}) @endif
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-6">
                            <select name="guru_pembimbing_id" class="form-select">
                                <option value="">Semua Guru Pembimbing</option>
                                @foreach($exportGuruList as $guru)
                                    <option value="{{ $guru->id }}">{{ $guru->nama_guru }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="row g-2 align-items-end">
                        <div class="col-md-8">
                            <select name="status" class="form-select">
                                <option value="">Semua Status Logbook</option>
                                <option value="draft">Draft</option>
                                <option value="submitted">Submitted (Menunggu ACC)</option>
                                <option value="approved">Approved</option>
                                <option value="rejected">Rejected</option>
                            </select>
                        </div>
                        <div class="col-md-4">
                            <button type="submit" class="btn btn-success w-100">
                                <i class="fas fa-download me-1"></i>Export Logbook
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
        @endif
    </div>
</div>
@endsection
