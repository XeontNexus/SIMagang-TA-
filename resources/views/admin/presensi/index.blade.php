@extends('layouts.app')

@section('title', 'Presensi Hari Ini - SIMagang')
@section('page-title', 'Presensi Siswa Hari Ini')

@section('content')
<div class="card shadow">
    <div class="card-header py-3">
        <h6 class="m-0 font-weight-bold text-primary">Daftar Presensi - {{ now()->format('d F Y') }}</h6>
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-bordered table-hover">
                <thead class="table-light">
                    <tr>
                        <th>No</th>
                        <th>Nama Siswa</th>
                        <th>Jam Masuk</th>
                        <th>Jam Keluar</th>
                        <th>Status</th>
                        <th>Keterangan</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($presensis as $index => $presensi)
                        <tr>
                            <td>{{ $index + 1 }}</td>
                            <td>{{ $presensi->user->nama_lengkap }}</td>
                            <td>{{ $presensi->jam_masuk ? $presensi->jam_masuk->format('H:i') : '-' }}</td>
                            <td>{{ $presensi->jam_keluar ? $presensi->jam_keluar->format('H:i') : '-' }}</td>
                            <td>
                                <span class="badge bg-{{ $presensi->status == 'hadir' ? 'success' : ($presensi->status == 'izin' ? 'warning' : ($presensi->status == 'sakit' ? 'info' : 'danger')) }}">
                                    {{ ucfirst($presensi->status) }}
                                </span>
                            </td>
                            <td>{{ $presensi->keterangan ?? '-' }}</td>
                            <td>
                                <a href="{{ route('admin.presensi.detail', ['student' => $presensi->user_id, 'bulan' => now()->format('Y-m')]) }}" class="btn btn-sm btn-info">
                                    <i class="fas fa-eye"></i>
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="text-center text-muted py-4">Belum ada presensi hari ini</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
