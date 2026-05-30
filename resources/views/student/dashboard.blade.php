@extends('layouts.app')

@section('title', 'Dashboard Siswa - SIMagang')
@section('page-title', 'Dashboard Siswa')

@section('content')
<div class="row">
    <!-- Persistent Notifications: Pending Location Requests -->
    @foreach($pendingLocationRequests as $request)
    <div class="col-12 mb-4">
        <div class="alert alert-info alert-dismissible fade show shadow-sm" role="alert" id="pendingRequest{{ $request->id }}">
            <h6 class="alert-heading mb-2">
                <i class="fas fa-hourglass-half me-2"></i>
                Permintaan Ubah Lokasi Magang
            </h6>
            <div class="small">
                <p class="mb-2">
                    Permintaan Anda untuk mengubah titik koordinat lokasi magang telah dikirim ke admin.<br>
                    <strong>Status: Menunggu Persetujuan</strong>
                </p>
                <div class="mb-2">
                    <strong>Lokasi Lama:</strong><br>
                    <code class="small">{{ round($request->old_latitude, 6) }}, {{ round($request->old_longitude, 6) }}</code>
                </div>
                <div class="mb-2">
                    <strong>Lokasi Baru:</strong><br>
                    <code class="small">{{ round($request->new_latitude, 6) }}, {{ round($request->new_longitude, 6) }}</code>
                </div>
                <p class="mb-0 text-muted">
                    <i class="fas fa-info-circle me-1"></i>
                    Anda akan mendapat notifikasi setelah admin memberikan persetujuan atau penolakan.
                </p>
            </div>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close" 
                    onclick="closePersistentNotif('pendingRequest{{ $request->id }}')"></button>
        </div>
    </div>
    @endforeach

    <!-- Welcome Card -->
    <div class="col-12 mb-4">
        <div class="card shadow">
            <div class="card-body">
                <h4 class="fw-bold">Selamat Datang, {{ auth()->user()->nama_lengkap }}!</h4>
                <p class="text-muted mb-0">
                    @if($todayPresensi)
                        @if($todayPresensi->status == 'hadir')
                            <span class="text-success"><i class="fas fa-check-circle"></i> Anda sudah presensi hari ini ({{ $todayPresensi->jam_masuk }})</span>
                        @else
                            <span class="text-warning"><i class="fas fa-info-circle"></i> Status hari ini: {{ ucfirst($todayPresensi->status) }}</span>
                        @endif
                    @else
                        <span class="text-danger"><i class="fas fa-exclamation-circle"></i> Anda belum presensi hari ini</span>
                    @endif
                </p>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <!-- Attendance Stats -->
    <div class="col-xl-3 col-md-6 mb-4">
        <div class="card card-stats success h-100 py-2">
            <div class="card-body">
                <div class="row no-gutters align-items-center">
                    <div class="col mr-2">
                        <div class="text-xs font-weight-bold text-success text-uppercase mb-1">Hadir Bulan Ini</div>
                        <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $stats['hadir'] }}</div>
                    </div>
                    <div class="col-auto">
                        <i class="fas fa-check-circle fa-2x text-gray-300"></i>
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
                        <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">Izin Bulan Ini</div>
                        <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $stats['izin'] }}</div>
                    </div>
                    <div class="col-auto">
                        <i class="fas fa-envelope fa-2x text-gray-300"></i>
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
                        <div class="text-xs font-weight-bold text-info text-uppercase mb-1">Sakit Bulan Ini</div>
                        <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $stats['sakit'] }}</div>
                    </div>
                    <div class="col-auto">
                        <i class="fas fa-hospital fa-2x text-gray-300"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-xl-3 col-md-6 mb-4">
        <div class="card card-stats danger h-100 py-2">
            <div class="card-body">
                <div class="row no-gutters align-items-center">
                    <div class="col mr-2">
                        <div class="text-xs font-weight-bold text-danger text-uppercase mb-1">Alpha Bulan Ini</div>
                        <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $stats['alpha'] }}</div>
                    </div>
                    <div class="col-auto">
                        <i class="fas fa-times-circle fa-2x text-gray-300"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <!-- Quick Actions -->
    <div class="col-lg-6 mb-4">
        <div class="card shadow">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-primary">Aksi Cepat</h6>
            </div>
            <div class="card-body">
                <div class="d-grid gap-2">
                    <a href="{{ route('student.presensi.create') }}" class="btn btn-success">
                        <i class="fas fa-clipboard-check me-2"></i>Presensi Sekarang
                    </a>
                    <a href="{{ route('student.logbooks.create') }}" class="btn btn-primary">
                        <i class="fas fa-book me-2"></i>Tambah Logbook
                    </a>
                    <a href="{{ route('student.logbooks.index') }}" class="btn btn-info">
                        <i class="fas fa-list me-2"></i>Lihat Logbook
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- Recent Logbooks -->
    <div class="col-lg-6 mb-4">
        <div class="card shadow">
            <div class="card-header py-3 d-flex justify-content-between align-items-center">
                <h6 class="m-0 font-weight-bold text-primary">Logbook Terbaru</h6>
                <a href="{{ route('student.logbooks.index') }}" class="btn btn-sm btn-primary">Lihat Semua</a>
            </div>
            <div class="card-body">
                <div class="list-group list-group-flush">
                    @forelse($recentLogbooks as $logbook)
                        <div class="list-group-item">
                            <div class="d-flex w-100 justify-content-between">
                                <h6 class="mb-1">Minggu Ke-{{ $logbook->minggu_ke }}</h6>
                                <span class="badge bg-{{ $logbook->status == 'approved' ? 'success' : ($logbook->status == 'rejected' ? 'danger' : ($logbook->status == 'submitted' ? 'warning' : 'secondary')) }}">
                                    {{ ucfirst($logbook->status) }}
                                </span>
                            </div>
                            <small class="text-muted">{{ $logbook->tanggal_mulai->format('d/m/Y') }} - {{ $logbook->tanggal_selesai->format('d/m/Y') }}</small>
                        </div>
                    @empty
                        <div class="text-center text-muted py-3">Belum ada logbook</div>
                    @endforelse
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
    // Close persistent notification in dashboard
    function closePersistentNotif(elementId) {
        const element = document.getElementById(elementId);
        if (element) {
            element.style.display = 'none';
        }
    }
</script>
@endpush
@endsection
