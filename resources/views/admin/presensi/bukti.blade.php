@extends('layouts.app')

@section('title', 'Bukti Presensi - SIMagang')
@section('page-title', 'Bukti Foto Presensi')

@section('content')
<div class="row justify-content-center">
    <div class="col-lg-8">
        <div class="card shadow">
            <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center">
                <h5 class="mb-0"><i class="fas fa-camera me-2"></i>Bukti Presensi</h5>
                <a href="{{ route('admin.presensi.bukti.download', $presensi) }}" class="btn btn-light btn-sm">
                    <i class="fas fa-download me-1"></i>Unduh Foto
                </a>
            </div>
            <div class="card-body">
                <div class="row mb-3">
                    <div class="col-md-6"><strong>Nama Siswa</strong><br>{{ $presensi->user->nama_lengkap }}</div>
                    <div class="col-md-6"><strong>Tanggal</strong><br>{{ $presensi->tanggal->translatedFormat('d F Y') }}</div>
                    <div class="col-md-6 mt-2"><strong>Status</strong><br>{{ ucfirst($presensi->status) }}</div>
                    <div class="col-md-6 mt-2"><strong>Jam Masuk</strong><br>{{ $presensi->jam_masuk ? \Carbon\Carbon::parse($presensi->jam_masuk)->format('H:i') : '-' }}</div>
                    <div class="col-12 mt-2"><strong>Keterangan</strong><br>{{ $presensi->keterangan ?? '-' }}</div>
                </div>
                <div class="text-center border rounded p-3 bg-light">
                    <img src="{{ $presensi->buktiFotoUrl() }}" alt="Bukti presensi {{ $presensi->user->nama_lengkap }}" class="img-fluid rounded shadow-sm" style="max-height: 500px;">
                </div>
            </div>
            <div class="card-footer">
                <a href="{{ route('admin.presensi.report') }}" class="btn btn-secondary">
                    <i class="fas fa-arrow-left me-1"></i>Kembali ke Laporan
                </a>
            </div>
        </div>
    </div>
</div>
@endsection
