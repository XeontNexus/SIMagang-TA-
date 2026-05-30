@extends('layouts.app')

@section('title', 'Perbandingan Lokasi Siswa & Mitra')

@section('content')
<div class="container-fluid">
    <div class="row mb-4">
        <div class="col-md-8">
            <h1 class="page-title">🗺️ Perbandingan Lokasi Siswa & Mitra</h1>
            <p class="text-muted">Bandingkan lokasi magang siswa dengan lokasi mitra untuk memastikan kesesuaian</p>
        </div>
        <div class="col-md-4 text-end">
            <a href="{{ route('admin.location-comparison.export', request()->query()) }}" class="btn btn-primary">
                📥 Export CSV
            </a>
        </div>
    </div>

    {{-- Filter Section --}}
    <div class="card mb-4">
        <div class="card-header">
            <h5 class="mb-0">⚙️ Filter & Pencarian</h5>
        </div>
        <div class="card-body">
            <form method="GET" class="row g-3">
                <div class="col-md-4">
                    <input type="text" name="search" class="form-control" placeholder="Cari nama atau NISN..." 
                        value="{{ request('search') }}">
                </div>
                <div class="col-md-3">
                    <select name="mitra_id" class="form-select">
                        <option value="">-- Semua Mitra --</option>
                        @foreach($mitras as $mitra)
                            <option value="{{ $mitra->id }}" @selected(request('mitra_id') == $mitra->id)>
                                {{ $mitra->nama_mitra }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-3">
                    <select name="status" class="form-select">
                        <option value="">-- Semua Status --</option>
                        @foreach($statuses as $status)
                            <option value="{{ $status }}" @selected(request('status') == $status)>
                                {{ ucfirst($status) }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-2">
                    <button type="submit" class="btn btn-primary w-100">🔍 Cari</button>
                </div>
            </form>
        </div>
    </div>

    {{-- Data Table --}}
    <div class="card">
        <div class="table-responsive">
            <table class="table table-hover mb-0">
                <thead class="table-light">
                    <tr>
                        <th>Siswa</th>
                        <th>Mitra Magang</th>
                        <th>Lokasi Siswa</th>
                        <th>Lokasi Mitra</th>
                        <th>Jarak</th>
                        <th>Kesamaan</th>
                        <th>Status</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($students as $student)
                        @php $comp = $student->location_comparison; @endphp
                        <tr>
                            <td>
                                <strong>{{ $student->nama_lengkap }}</strong>
                                <br>
                                <small class="text-muted">{{ $student->nisn }}</small>
                            </td>
                            <td>
                                {{ $comp['mitra_name'] ?? '—' }}
                            </td>
                            <td>
                                <small>
                                    @if($student->gmap_magang)
                                        📍 {{ $comp['student_lat'] }}, {{ $comp['student_lon'] }}
                                        <br>
                                        <a href="{{ $student->gmap_magang }}" target="_blank" class="link-secondary">
                                            Buka peta
                                        </a>
                                    @else
                                        <span class="text-warning">Belum diisi</span>
                                    @endif
                                </small>
                            </td>
                            <td>
                                <small>
                                    @if($comp['has_complete_info'])
                                        📍 {{ $comp['mitra_lat'] }}, {{ $comp['mitra_lon'] }}
                                        @if($comp['mitra_gmap'])
                                            <br>
                                            <a href="{{ $comp['mitra_gmap'] }}" target="_blank" class="link-secondary">
                                                Buka peta
                                            </a>
                                        @endif
                                    @else
                                        <span class="text-warning">Belum diisi</span>
                                    @endif
                                </small>
                            </td>
                            <td>
                                @if($comp['distance'])
                                    <strong>{{ round($comp['distance'], 2) }} m</strong>
                                @else
                                    <span class="text-muted">—</span>
                                @endif
                            </td>
                            <td>
                                @php
                                    $similarity = $comp['similarity_percentage'];
                                    if ($similarity >= 80) $badgeClass = 'success';
                                    elseif ($similarity >= 60) $badgeClass = 'warning';
                                    else $badgeClass = 'danger';
                                @endphp
                                <span class="badge bg-{{ $badgeClass }}">
                                    {{ $similarity }}%
                                </span>
                            </td>
                            <td>
                                @php
                                    $zoneMap = ['hijau' => 'success', 'kuning' => 'warning', 'merah' => 'danger'];
                                    $zoneClass = $zoneMap[$comp['status_zone']] ?? 'secondary';
                                @endphp
                                <span class="badge bg-{{ $zoneClass }}">
                                    Zone {{ ucfirst($comp['status_zone']) }}
                                </span>
                            </td>
                            <td>
                                <a href="{{ route('admin.location-comparison.show', $student) }}" 
                                   class="btn btn-sm btn-info">
                                    📊 Detail
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="text-center py-4">
                                <p class="text-muted mb-0">Tidak ada data siswa dengan lokasi lengkap</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    {{-- Pagination --}}
    <div class="d-flex justify-content-center mt-4">
        {{ $students->links() }}
    </div>
</div>

<style>
    .page-title {
        font-size: 1.8rem;
        font-weight: 600;
        margin-bottom: 0.5rem;
    }
    
    .table-hover tbody tr:hover {
        background-color: #f5f5f5;
    }
</style>
@endsection
