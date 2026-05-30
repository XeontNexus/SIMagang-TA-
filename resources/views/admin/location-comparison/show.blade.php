@extends('layouts.app')

@section('title', 'Detail Perbandingan Lokasi - ' . $user->nama_lengkap)

@section('content')
<div class="container-fluid">
    <div class="row mb-4">
        <div class="col-md-8">
            <h1 class="page-title">📊 Detail Perbandingan Lokasi</h1>
            <p class="text-muted">{{ $user->nama_lengkap }} ({{ $user->nisn }})</p>
        </div>
        <div class="col-md-4 text-end">
            <a href="{{ route('admin.location-comparison.index') }}" class="btn btn-secondary">
                ← Kembali
            </a>
        </div>
    </div>

    <div class="row">
        {{-- Informasi Siswa --}}
        <div class="col-md-6 mb-4">
            <div class="card">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0">📍 Lokasi Siswa</h5>
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <label class="form-label"><strong>Nama Siswa:</strong></label>
                        <p>{{ $user->nama_lengkap }}</p>
                    </div>
                    <div class="mb-3">
                        <label class="form-label"><strong>NISN:</strong></label>
                        <p>{{ $user->nisn }}</p>
                    </div>
                    <div class="mb-3">
                        <label class="form-label"><strong>Koordinat:</strong></label>
                        <p>
                            <code>{{ $comparison['student_lat'] }}, {{ $comparison['student_lon'] }}</code>
                        </p>
                    </div>
                    <div class="mb-3">
                        <label class="form-label"><strong>Link Google Maps:</strong></label>
                        @if($user->gmap_magang)
                            <a href="{{ $user->gmap_magang }}" target="_blank" class="btn btn-sm btn-primary">
                                🗺️ Buka di Google Maps
                            </a>
                        @else
                            <p class="text-warning">Belum diisi</p>
                        @endif
                    </div>
                    <div class="mb-3">
                        <label class="form-label"><strong>Alamat Magang:</strong></label>
                        <p>{{ $user->alamat_magang ?? '—' }}</p>
                    </div>
                </div>
            </div>
        </div>

        {{-- Informasi Mitra --}}
        <div class="col-md-6 mb-4">
            <div class="card">
                <div class="card-header bg-success text-white">
                    <h5 class="mb-0">🏢 Lokasi Mitra Magang</h5>
                </div>
                <div class="card-body">
                    @if($user->mitra)
                        @php $mitra = $user->mitra; @endphp
                        <div class="mb-3">
                            <label class="form-label"><strong>Nama Mitra:</strong></label>
                            <p>{{ $mitra->nama_mitra }}</p>
                        </div>
                        <div class="mb-3">
                            <label class="form-label"><strong>Alamat:</strong></label>
                            <p>{{ $mitra->alamat ?? '—' }}</p>
                        </div>
                        <div class="mb-3">
                            <label class="form-label"><strong>Kontak:</strong></label>
                            <p>{{ $mitra->kontak ?? '—' }}</p>
                        </div>
                        
                        @if($comparison['has_complete_info'])
                            <div class="mb-3">
                                <label class="form-label"><strong>Koordinat:</strong></label>
                                <p>
                                    <code>{{ $comparison['mitra_lat'] }}, {{ $comparison['mitra_lon'] }}</code>
                                </p>
                            </div>
                            <div class="mb-3">
                                <label class="form-label"><strong>Link Google Maps:</strong></label>
                                @if($mitra->gmap_link)
                                    <a href="{{ $mitra->gmap_link }}" target="_blank" class="btn btn-sm btn-success">
                                        🗺️ Buka di Google Maps
                                    </a>
                                @else
                                    <p class="text-warning">Belum diisi</p>
                                @endif
                            </div>
                        @else
                            <div class="alert alert-warning">
                                ⚠️ Lokasi mitra belum lengkap. Admin perlu mengupdate link Google Maps mitra.
                            </div>
                            
                            <div class="mb-3">
                                <label class="form-label"><strong>Update Link Google Maps:</strong></label>
                                <form id="updateMitraForm" class="d-flex gap-2">
                                    @csrf
                                    <input type="url" name="gmap_link" class="form-control form-control-sm" 
                                        placeholder="Masukkan link Google Maps mitra" required>
                                    <button type="submit" class="btn btn-sm btn-primary">Simpan</button>
                                </form>
                            </div>
                        @endif
                    @else
                        <div class="alert alert-info">
                            ℹ️ Siswa ini belum dihubungkan dengan mitra magang. Harap atur di profil siswa.
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    {{-- Comparison Results --}}
    @if($comparison['has_complete_info'])
    <div class="row">
        <div class="col-md-12 mb-4">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">📈 Hasil Perbandingan</h5>
                </div>
                <div class="card-body">
                    <div class="row text-center">
                        <div class="col-md-4">
                            <div class="mb-3">
                                <h6 class="text-muted">Jarak Antar Lokasi</h6>
                                <h2 class="display-4 text-primary">{{ round($comparison['distance'], 2) }} <small>m</small></h2>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="mb-3">
                                <h6 class="text-muted">Kesamaan Lokasi</h6>
                                @php
                                    $similarity = $comparison['similarity_percentage'];
                                    if ($similarity >= 80) $color = 'success';
                                    elseif ($similarity >= 60) $color = 'warning';
                                    else $color = 'danger';
                                @endphp
                                <h2 class="display-4 text-{{ $color }}">{{ $similarity }}<small>%</small></h2>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="mb-3">
                                <h6 class="text-muted">Status Zone</h6>
                                @php
                                    $zoneMap = ['hijau' => 'success', 'kuning' => 'warning', 'merah' => 'danger'];
                                    $zoneClass = $zoneMap[$comparison['status_zone']] ?? 'secondary';
                                @endphp
                                <h2 class="display-4">
                                    <span class="badge bg-{{ $zoneClass }} fs-4">
                                        {{ strtoupper($comparison['status_zone']) }}
                                    </span>
                                </h2>
                            </div>
                        </div>
                    </div>

                    <hr>

                    <div class="row mt-4">
                        <div class="col-md-12">
                            <h6 class="mb-3">📌 Interpretasi:</h6>
                            <ul>
                                <li><strong>Jarak Antar Lokasi:</strong> Menunjukkan berapa jauh lokasi siswa dari lokasi mitra magang</li>
                                <li><strong>Kesamaan Lokasi (%):</strong> Persentase akurasi lokasi (100% = lokasi sama persis, 0% = jauh)</li>
                                <li>
                                    <strong>Status Zone:</strong>
                                    <ul>
                                        <li><span class="badge bg-success">HIJAU</span> = Siswa berada di lokasi yang sangat dekat dengan mitra (< 30m)</li>
                                        <li><span class="badge bg-warning">KUNING</span> = Siswa berada di area terdekat mitra (30m - 70m)</li>
                                        <li><span class="badge bg-danger">MERAH</span> = Siswa jauh dari lokasi mitra (> 70m)</li>
                                    </ul>
                                </li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    @endif

    {{-- Map Embedding (opsional) --}}
    <div class="row">
        <div class="col-md-12 mb-4">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">🗺️ Visualisasi Peta</h5>
                </div>
                <div class="card-body">
                    <p class="text-muted mb-3">Klik link Google Maps di atas untuk melihat peta interaktif dengan detail lokasi.</p>
                    <div class="alert alert-info">
                        💡 <strong>Tips:</strong> Gunakan Google Maps untuk memverifikasi lokasi siswa dan mitra dengan lebih detail.
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
    .page-title {
        font-size: 1.8rem;
        font-weight: 600;
        margin-bottom: 0.5rem;
    }
    
    .display-4 {
        font-size: 2.5rem;
    }
    
    code {
        background-color: #f5f5f5;
        padding: 0.25rem 0.5rem;
        border-radius: 3px;
        font-family: 'Courier New', monospace;
    }
</style>

<script>
document.getElementById('updateMitraForm')?.addEventListener('submit', async (e) => {
    e.preventDefault();
    
    const gmapLink = document.querySelector('input[name="gmap_link"]').value;
    const mitraId = {{ $user->mitra->id ?? 'null' }};
    
    if (!mitraId) {
        alert('Mitra tidak ditemukan');
        return;
    }
    
    try {
        const response = await fetch(
            `/admin/location-comparison/mitra/${mitraId}/update-location`,
            {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                },
                body: JSON.stringify({ gmap_link: gmapLink })
            }
        );
        
        const data = await response.json();
        
        if (data.success) {
            alert(data.message);
            window.location.reload();
        } else {
            alert('Error: ' + data.message);
        }
    } catch (error) {
        console.error('Error:', error);
        alert('Terjadi kesalahan');
    }
});
</script>
@endsection
