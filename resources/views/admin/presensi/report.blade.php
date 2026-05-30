@extends('layouts.app')

@section('title', 'Laporan Presensi - SIMagang')
@section('page-title', 'Laporan Presensi')

@section('content')
<!-- Filter Form Premium Card -->
<div class="card shadow mb-4">
    <div class="card-body">
        <form method="GET" class="row g-3">
            <div class="col-md-8">
                <label class="form-label small fw-bold">Cari Siswa (Hari Ini)</label>
                <input type="text" name="search" class="form-control" placeholder="Cari nama/institusi/kelas..." 
                       value="{{ request('search') }}">
            </div>
            <div class="col-md-4 d-flex align-items-end">
                <button type="submit" class="btn btn-primary me-2">
                    <i class="fas fa-search me-2"></i>Filter
                </button>
                <a href="{{ route('admin.presensi.report') }}" class="btn btn-secondary">
                    <i class="fas fa-sync-alt me-1"></i>Reset
                </a>
            </div>
        </form>
    </div>
</div>

<!-- Section: Presensi Hari Ini -->
<div class="card shadow mb-4">
    <div class="card-header py-3 d-flex justify-content-between align-items-center">
        <h6 class="m-0 font-weight-bold text-primary"><i class="fas fa-calendar-day me-2"></i>Presensi Siswa Hari Ini ({{ \Carbon\Carbon::today()->translatedFormat('d F Y') }})</h6>
        <span class="badge bg-primary px-3 py-2">{{ $todayPresensi->count() }} Terdata</span>
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-bordered table-hover">
                <thead class="table-light">
                    <tr>
                        <th width="4%" class="text-center">No</th>
                        <th width="20%">Nama Siswa</th>
                        <th width="15%">Tempat Mitra</th>
                        <th width="10%" class="text-center">Jam Masuk</th>
                        <th width="10%" class="text-center">Jam Keluar</th>
                        <th width="16%">Koordinat Presensi</th>
                        <th width="10%" class="text-center">Kecocokan</th>
                        <th width="15%">Status / Alasan Masuk</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($todayPresensi as $index => $presensi)
                        @php
                            $kecocokanData = $presensi->calculateKecocokan();
                        @endphp
                        <tr>
                            <td class="text-center align-middle">{{ $index + 1 }}</td>
                            <td class="align-middle">
                                <strong>{{ $presensi->user->nama_lengkap }}</strong>
                                <div class="small text-muted">{{ $presensi->user->kelas?->tingkat }}-{{ $presensi->user->kelas?->nama_kelas }} ({{ $presensi->user->jurusan?->nama_jurusan }})</div>
                            </td>
                            <td class="align-middle">
                                <span class="fw-semibold text-secondary small">
                                    <i class="fas fa-building me-1 text-muted"></i>{{ $presensi->user->mitra?->nama_mitra ?? $presensi->user->mitra_magang ?? '-' }}
                                </span>
                            </td>
                            <td class="text-center align-middle">
                                <span class="fw-bold text-dark"><i class="far fa-clock me-1 text-success"></i>{{ $presensi->jam_masuk ? $presensi->jam_masuk->format('H:i') : '-' }}</span>
                            </td>
                            <td class="text-center align-middle">
                                <span class="fw-bold text-dark"><i class="far fa-clock me-1 text-danger"></i>{{ $presensi->jam_keluar ? $presensi->jam_keluar->format('H:i') : '-' }}</span>
                            </td>
                            <td class="align-middle">
                                @if($presensi->status === 'hadir' && $presensi->latitude_masuk && $presensi->longitude_masuk)
                                    <a href="https://www.google.com/maps?q={{ $presensi->latitude_masuk }},{{ $presensi->longitude_masuk }}" target="_blank" class="btn btn-sm btn-outline-danger w-100 text-start">
                                        <i class="fas fa-map-marker-alt me-1"></i> {{ round($presensi->latitude_masuk, 5) }}, {{ round($presensi->longitude_masuk, 5) }}
                                    </a>
                                @else
                                    <span class="text-muted">-</span>
                                @endif
                            </td>
                            <td class="text-center align-middle">
                                @if($presensi->status === 'hadir')
                                    @if($kecocokanData)
                                        @php
                                            $pct = $kecocokanData['percentage'];
                                            $badgeColor = 'success';
                                            if ($pct < 70) {
                                                $badgeColor = 'danger';
                                            } elseif ($pct < 90) {
                                                $badgeColor = 'warning';
                                            }
                                        @endphp
                                        <span class="badge bg-{{ $badgeColor }} fs-6 py-2 px-3">
                                            {{ $pct }}%
                                        </span>
                                        <div class="small text-muted mt-1" style="font-size: 0.75rem;">Jarak: {{ \App\Helpers\LocationHelper::formatDistance($kecocokanData['distance']) }}</div>
                                    @else
                                        @if(!$presensi->user->latitude || !$presensi->user->longitude)
                                            <span class="badge bg-light text-warning border border-warning" style="font-size: 0.75rem;">Koordinat Belum Diset</span>
                                        @else
                                            <span class="text-muted">-</span>
                                        @endif
                                    @endif
                                @else
                                    <span class="text-muted">-</span>
                                @endif
                            </td>
                            <td class="align-middle">
                                <span class="badge bg-{{ $presensi->status == 'hadir' ? 'success' : ($presensi->status == 'izin' ? 'warning' : ($presensi->status == 'sakit' ? 'info' : 'danger')) }} mb-1">
                                    {{ ucfirst($presensi->status) }}
                                </span>
                                @if($presensi->keterangan)
                                    <div class="small text-secondary fw-semibold">{{ $presensi->keterangan }}</div>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="text-center text-muted py-4">Belum ada siswa yang melakukan presensi hari ini.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

@endsection
