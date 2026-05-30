@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <!-- Page Title -->
    <div class="row mb-4">
        <div class="col-md-12">
            <h1 class="h3 mb-0 text-gray-800">Pengaturan Jarak Presensi</h1>
            <p class="text-muted">Atur jarak maksimal lokasi siswa untuk melakukan presensi dari lokasi magang</p>
        </div>
    </div>

    <!-- Alert Messages -->
    @if ($errors->any())
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <h4 class="alert-heading">Error!</h4>
            <ul class="mb-0">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    @if (session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <i class="fas fa-check-circle"></i> {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <!-- Settings Card -->
    <div class="row">
        <div class="col-md-8">
            <div class="card shadow-sm">
                <div class="card-header bg-primary text-white">
                    <h5 class="m-0">
                        <i class="fas fa-map-marked-alt"></i> Konfigurasi Jarak Presensi
                    </h5>
                </div>
                <div class="card-body">
                    <form action="{{ route('admin.presence-distance.update') }}" method="POST">
                        @csrf
                        @method('PUT')

                        <!-- Jarak Maksimal -->
                        <div class="mb-3">
                            <label for="jarak_maksimal" class="form-label fw-bold">
                                Jarak Maksimal Presensi
                                <span class="text-danger">*</span>
                            </label>
                            <div class="input-group">
                                <input 
                                    type="number" 
                                    class="form-control @error('jarak_maksimal') is-invalid @enderror" 
                                    id="jarak_maksimal" 
                                    name="jarak_maksimal" 
                                    value="{{ $setting->jarak_maksimal }}"
                                    min="10"
                                    max="5000"
                                    step="10"
                                    required
                                >
                                <select 
                                    class="form-select @error('satuan') is-invalid @enderror" 
                                    name="satuan"
                                    style="max-width: 150px;"
                                >
                                    <option value="meter" {{ $setting->satuan === 'meter' ? 'selected' : '' }}>Meter (m)</option>
                                    <option value="km" {{ $setting->satuan === 'km' ? 'selected' : '' }}>Kilometer (km)</option>
                                </select>
                            </div>
                            <small class="form-text text-muted d-block mt-2">
                                <i class="fas fa-info-circle"></i> 
                                Siswa hanya bisa melakukan presensi jika lokasinya dalam jarak ini dari lokasi magang.
                                <br>
                                Contoh: Jika diatur 500 meter, siswa harus dalam radius 500m dari lokasi magang untuk presensi.
                            </small>
                            @error('jarak_maksimal')
                                <div class="invalid-feedback d-block">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Deskripsi -->
                        <div class="mb-3">
                            <label for="deskripsi" class="form-label fw-bold">Deskripsi/Catatan</label>
                            <textarea 
                                class="form-control @error('deskripsi') is-invalid @enderror" 
                                id="deskripsi" 
                                name="deskripsi" 
                                rows="3"
                                placeholder="Catatan tambahan tentang pengaturan ini (opsional)"
                            >{{ $setting->deskripsi }}</textarea>
                            @error('deskripsi')
                                <div class="invalid-feedback d-block">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Status -->
                        <div class="mb-3">
                            <div class="form-check form-switch">
                                <input 
                                    class="form-check-input" 
                                    type="checkbox" 
                                    id="aktif" 
                                    name="aktif" 
                                    value="1"
                                    {{ $setting->aktif ? 'checked' : '' }}
                                >
                                <label class="form-check-label fw-bold" for="aktif">
                                    Aktifkan Pengaturan Jarak Presensi
                                </label>
                            </div>
                            <small class="form-text text-muted d-block mt-2">
                                <i class="fas fa-info-circle"></i> 
                                Jika dinonaktifkan, semua siswa bisa presensi tanpa validasi jarak lokasi.
                            </small>
                        </div>

                        <!-- Action Buttons -->
                        <div class="mt-4">
                            <button type="submit" class="btn btn-primary btn-sm">
                                <i class="fas fa-save"></i> Simpan Pengaturan
                            </button>
                            <a href="{{ route('admin.dashboard') }}" class="btn btn-secondary btn-sm">
                                <i class="fas fa-arrow-left"></i> Kembali
                            </a>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <!-- Info Card -->
        <div class="col-md-4">
            <div class="card shadow-sm border-info">
                <div class="card-header bg-info text-white">
                    <h5 class="m-0">
                        <i class="fas fa-lightbulb"></i> Informasi
                    </h5>
                </div>
                <div class="card-body">
                    <p class="mb-3">
                        <strong>Jarak Saat Ini:</strong><br>
                        <code class="text-primary">{{ $setting->jarak_maksimal }} {{ $setting->satuan }}</code>
                    </p>
                    <p class="mb-3">
                        <strong>Status:</strong><br>
                        @if ($setting->aktif)
                            <span class="badge bg-success">Aktif</span>
                        @else
                            <span class="badge bg-danger">Nonaktif</span>
                        @endif
                    </p>
                    <hr>
                    <p><strong>Catatan:</strong></p>
                    <ul class="ps-3 mb-0">
                        <li>Jarak diukur dari lokasi magang siswa (GPS)</li>
                        <li>Lokasi magang diambil dari profil siswa</li>
                        <li>Lokasi presensi diambil dari GPS saat presensi</li>
                        <li>Sistem menggunakan Haversine Formula untuk perhitungan</li>
                    </ul>
                </div>
            </div>

            <!-- Help Card -->
            <div class="card shadow-sm border-warning mt-3">
                <div class="card-header bg-warning text-dark">
                    <h5 class="m-0">
                        <i class="fas fa-question-circle"></i> Panduan
                    </h5>
                </div>
                <div class="card-body">
                    <p class="small mb-2">
                        <strong>Konversi Jarak:</strong><br>
                        • 500 meter = 0.5 km<br>
                        • 1000 meter = 1 km<br>
                        • 5000 meter = 5 km
                    </p>
                    <p class="small mb-0">
                        <strong>Rekomendasi:</strong><br>
                        Untuk kantor/pabrik kecil gunakan 300-500m, untuk lokasi besar gunakan 500-1000m
                    </p>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    // Auto-convert km to meter when satuan changes
    document.querySelector('select[name="satuan"]').addEventListener('change', function() {
        const input = document.querySelector('input[name="jarak_maksimal"]');
        const value = parseFloat(input.value);
        
        if (this.value === 'km' && input.dataset.lastSatuan === 'meter') {
            // Convert meter to km
            input.value = Math.round(value / 1000);
        } else if (this.value === 'meter' && input.dataset.lastSatuan === 'km') {
            // Convert km to meter
            input.value = value * 1000;
        }
        
        input.dataset.lastSatuan = this.value;
    });
</script>
@endsection
