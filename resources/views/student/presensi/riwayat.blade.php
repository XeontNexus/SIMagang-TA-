@extends('layouts.app')

@section('title', 'Riwayat Presensi - SIMagang')
@section('page-title', 'Riwayat Presensi')

@section('content')
<div class="row mb-4">
    <div class="col-12">
        <a href="{{ route('student.presensi.index') }}" class="btn btn-secondary">
            <i class="fas fa-arrow-left me-2"></i>Kembali ke Presensi
        </a>
    </div>
</div>

<div class="alert alert-info py-2 small mb-3">
    <i class="fas fa-info-circle me-1"></i>
    Riwayat presensi hanya menampilkan <strong>7 hari terakhir</strong>. Data lebih lama dihapus otomatis.
</div>

<div class="card shadow">
    <div class="card-header py-3">
        <h6 class="m-0 font-weight-bold text-primary"><i class="fas fa-history"></i> Riwayat Presensi</h6>
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-bordered table-hover">
                <thead class="table-light">
                    <tr>
                        <th>Tanggal</th>
                        <th>Jam Masuk</th>
                        <th>Jam Keluar</th>
                        <th>Status</th>
                        <th>Lokasi</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($presensis as $presensi)
                        <tr>
                            <td>{{ $presensi->tanggal->format('d/m/Y') }}</td>
                            <td>{{ $presensi->jam_masuk ? \Carbon\Carbon::parse($presensi->jam_masuk)->format('H:i:s') : '-' }}</td>
                            <td>{{ $presensi->jam_keluar ? \Carbon\Carbon::parse($presensi->jam_keluar)->format('H:i:s') : '-' }}</td>
                            <td>
                                <span class="badge bg-{{ $presensi->status == 'hadir' ? 'success' : ($presensi->status == 'izin' ? 'warning' : ($presensi->status == 'sakit' ? 'info' : 'danger')) }}">
                                    {{ ucfirst($presensi->status) }}
                                </span>
                            </td>
                            <td>
                                @if($presensi->latitude_masuk && $presensi->longitude_masuk)
                                    <small class="text-muted">
                                        {{ round($presensi->latitude_masuk, 4) }},<br>
                                        {{ round($presensi->longitude_masuk, 4) }}
                                    </small>
                                @else
                                    <span class="text-muted">-</span>
                                @endif
                            </td>
                            <td>
                                @if($presensi->latitude_masuk && $presensi->longitude_masuk)
                                    <a href="https://www.google.com/maps?q={{ $presensi->latitude_masuk }},{{ $presensi->longitude_masuk }}" 
                                       target="_blank" class="btn btn-sm btn-outline-primary">
                                        <i class="fas fa-map-marker-alt"></i> Lihat Peta
                                    </a>
                                @else
                                    <span class="text-muted">-</span>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="text-center text-muted py-4">
                                <i class="fas fa-inbox"></i> Belum ada data presensi
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{ $presensis->links() }}
    </div>
</div>
@endsection
