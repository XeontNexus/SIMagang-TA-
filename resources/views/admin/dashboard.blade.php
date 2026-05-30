@extends('layouts.app')

@section('title', 'Admin Dashboard - SIMagang')
@section('page-title', 'Dashboard Admin')

@section('content')
<div class="row">
    <!-- Stats Cards -->
    <div class="col-xl-3 col-md-6 mb-4">
        <div class="card card-stats primary h-100 py-2">
            <div class="card-body">
                <div class="row no-gutters align-items-center">
                    <div class="col mr-2">
                        <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">Total Siswa</div>
                        <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $stats['total_students'] }}</div>
                    </div>
                    <div class="col-auto">
                        <i class="fas fa-users fa-2x text-gray-300"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-xl-3 col-md-6 mb-4">
        <div class="card card-stats success h-100 py-2">
            <div class="card-body">
                <div class="row no-gutters align-items-center">
                    <div class="col mr-2">
                        <div class="text-xs font-weight-bold text-success text-uppercase mb-1">Siswa Aktif</div>
                        <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $stats['active_students'] }}</div>
                    </div>
                    <div class="col-auto">
                        <i class="fas fa-user-check fa-2x text-gray-300"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-xl-3 col-md-6 mb-4">
        <div class="card card-stats warning h-100 py-2">
            <div class="card-body">
                <div class="row no-gutters align-items-center">
                    <div class="col mr-2">
                        <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">Proses</div>
                        <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $stats['proses_students'] }}</div>
                    </div>
                    <div class="col-auto">
                        <i class="fas fa-spinner fa-2x text-gray-300"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-xl-3 col-md-6 mb-4">
        <div class="card card-stats secondary h-100 py-2" style="border-left-color: #858796 !important;">
            <div class="card-body">
                <div class="row no-gutters align-items-center">
                    <div class="col mr-2">
                        <div class="text-xs font-weight-bold text-secondary text-uppercase mb-1">Menunggu</div>
                        <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $stats['menunggu_students'] }}</div>
                    </div>
                    <div class="col-auto">
                        <i class="fas fa-clock fa-2x text-gray-300"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-xl-3 col-md-6 mb-4">
        <div class="card card-stats info h-100 py-2">
            <div class="card-body">
                <div class="row no-gutters align-items-center">
                    <div class="col mr-2">
                        <div class="text-xs font-weight-bold text-info text-uppercase mb-1">Presensi Hari Ini</div>
                        <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $stats['total_today'] }}</div>
                    </div>
                    <div class="col-auto">
                        <i class="fas fa-clipboard-check fa-2x text-gray-300"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-xl-3 col-md-6 mb-4">
        <div class="card card-stats warning h-100 py-2">
            <div class="card-body">
                <div class="row no-gutters align-items-center">
                    <div class="col mr-2">
                        <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">Logbook Pending</div>
                        <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $stats['pending_logbooks'] }}</div>
                    </div>
                    <div class="col-auto">
                        <i class="fas fa-book fa-2x text-gray-300"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <!-- Today's Attendance Stats -->
    <div class="col-lg-6 mb-4">
        <div class="card shadow mb-4">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-primary">Presensi Hari Ini</h6>
            </div>
            <div class="card-body">
                <div class="row text-center">
                    <div class="col-3">
                        <div class="h4 text-success">{{ $stats['hadir_today'] }}</div>
                        <small class="text-muted">Hadir</small>
                    </div>
                    <div class="col-3">
                        <div class="h4 text-warning">{{ $stats['izin_today'] }}</div>
                        <small class="text-muted">Izin</small>
                    </div>
                    <div class="col-3">
                        <div class="h4 text-info">{{ $stats['sakit_today'] }}</div>
                        <small class="text-muted">Sakit</small>
                    </div>
                    <div class="col-3">
                        <div class="h4 text-danger">{{ $stats['alpha_today'] }}</div>
                        <small class="text-muted">Alpha</small>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Recent Students -->
    <div class="col-lg-6 mb-4">
        <div class="card shadow mb-4">
            <div class="card-header py-3 d-flex justify-content-between align-items-center">
                <h6 class="m-0 font-weight-bold text-primary">Siswa Terbaru</h6>
                <a href="{{ route('admin.students.index') }}" class="btn btn-sm btn-primary">Lihat Semua</a>
            </div>
            <div class="card-body">
                <div class="list-group list-group-flush">
                    @forelse($recent_students as $student)
                        <div class="list-group-item d-flex justify-content-between align-items-center">
                            <div>
                                <h6 class="mb-1">{{ $student->nama_lengkap }}</h6>
                                <small class="text-muted"><i class="fab fa-whatsapp me-1"></i>{{ $student->no_hp ?? '-' }}</small>
                            </div>
                            @include('partials.student-status-badge', ['status' => $student->status])
                        </div>
                    @empty
                        <div class="text-center text-muted py-3">Belum ada data siswa</div>
                    @endforelse
                </div>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <!-- Pending Logbooks -->
    <div class="col-lg-12">
        <div class="card shadow mb-4">
            <div class="card-header py-3 d-flex justify-content-between align-items-center">
                <h6 class="m-0 font-weight-bold text-primary">Logbook Menunggu Approval</h6>
                <a href="{{ route('admin.logbooks.index') }}" class="btn btn-sm btn-primary">Lihat Semua</a>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-bordered">
                        <thead>
                            <tr>
                                <th>Siswa</th>
                                <th>Minggu Ke</th>
                                <th>Tanggal</th>
                                <th>Status</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($recent_logbooks as $logbook)
                                <tr>
                                    <td>{{ $logbook->user->nama_lengkap }}</td>
                                    <td>{{ $logbook->minggu_ke }}</td>
                                    <td>{{ $logbook->tanggal_mulai->format('d/m/Y') }} - {{ $logbook->tanggal_selesai->format('d/m/Y') }}</td>
                                    <td><span class="badge bg-warning">Submitted</span></td>
                                    <td>
                                        <a href="{{ route('admin.logbooks.show', $logbook) }}" class="btn btn-sm btn-info">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="text-center text-muted">Tidak ada logbook pending</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
