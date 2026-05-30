@extends('layouts.app')

@section('title', 'Presensi - SIMagang')
@section('page-title', 'Presensi Hari Ini')

@section('content')
<div class="row justify-content-center">
    <div class="col-md-8">
        <!-- Alert Messages -->
        @if ($errors->any())
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <i class="fas fa-exclamation-circle"></i> <strong>Error!</strong>
                @foreach ($errors->all() as $error)
                    <div>{{ $error }}</div>
                @endforeach
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        <div class="card shadow">
            <div class="card-header bg-success text-white">
                <h5 class="mb-0"><i class="fas fa-clipboard-check me-2"></i>Form Presensi</h5>
            </div>
            <div class="card-body">
                <form method="POST" action="{{ route('student.presensi.store') }}" enctype="multipart/form-data" id="presensiForm">
                    @csrf

                    <!-- Tanggal -->
                    <div class="mb-3">
                        <label class="form-label"><i class="fas fa-calendar"></i> Tanggal</label>
                        <input type="text" class="form-control" value="{{ date('d F Y') }}" disabled>
                    </div>

                    <!-- Lokasi Magang Info -->
                    <div class="alert alert-info mb-3">
                        <h6 class="alert-heading mb-2"><i class="fas fa-map-pin"></i> Informasi Lokasi Magang</h6>
                        <div id="companyLocationInfo" class="small">
                            <p><strong>Loading informasi lokasi...</strong></p>
                        </div>
                        <div id="gmapButtonContainer" style="margin-top: 12px; display: none;">
                            <button type="button" class="btn btn-sm btn-primary" data-bs-toggle="modal" data-bs-target="#gmapModal">
                                <i class="fas fa-edit"></i> Isi Link Google Maps
                            </button>
                        </div>
                    </div>

                    <!-- GPS Status -->
                    <div class="alert alert-warning alert-dismissible fade show" id="gpsWarning" role="alert" style="display: none;">
                        <i class="fas fa-map-marker-alt"></i> <strong>GPS belum terdeteksi!</strong><br>
                        Pastikan GPS aktif dan izinkan akses lokasi. Klik tombol di bawah untuk mencoba lagi.
                        <button type="button" class="btn btn-sm btn-info mt-2" onclick="requestLocation()">
                            <i class="fas fa-sync"></i> Deteksi GPS Ulang
                        </button>
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>

                    <!-- Distance Info -->
                    <div class="alert alert-success mb-3" id="distanceAlert" style="display: none;">
                        <i class="fas fa-check-circle"></i> <strong>Jarak dari lokasi magang:</strong> <span id="distanceValue">--</span> m
                    </div>

                    <!-- Distance Warning -->
                    <div class="alert alert-danger mb-3" id="distanceWarning" style="display: none;">
                        <i class="fas fa-times-circle"></i> <strong>⚠️ Peringatan Jarak!</strong><br>
                        Lokasi Anda terlalu jauh dari lokasi magang. <br>
                        <span id="distanceWarningText"></span>
                    </div>

                    <!-- Status -->
                    <div class="mb-3">
                        <label for="status" class="form-label"><i class="fas fa-clipboard"></i> Status Kehadiran *</label>
                        <select class="form-select @error('status') is-invalid @enderror" id="status" name="status" required>
                            <option value="">Pilih Status</option>
                            <option value="hadir" {{ old('status') == 'hadir' ? 'selected' : '' }}>Hadir</option>
                            <option value="izin" {{ old('status') == 'izin' ? 'selected' : '' }}>Izin</option>
                            <option value="sakit" {{ old('status') == 'sakit' ? 'selected' : '' }}>Sakit</option>
                        </select>
                        @error('status')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <!-- Keterangan -->
                    <div class="mb-3">
                        <label for="keterangan" class="form-label"><i class="fas fa-pen"></i> Keterangan</label>
                        <textarea class="form-control @error('keterangan') is-invalid @enderror" id="keterangan" 
                                  name="keterangan" rows="3" placeholder="Masukkan keterangan (opsional)">{{ old('keterangan') }}</textarea>
                        @error('keterangan')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <!-- Bukti Foto -->
                    <div class="mb-3">
                        <label for="bukti_foto" class="form-label"><i class="fas fa-camera"></i> Bukti Foto</label>
                        <input type="file" class="form-control @error('bukti_foto') is-invalid @enderror" 
                               id="bukti_foto" name="bukti_foto" accept="image/*">
                        <small class="form-text text-muted">Format: JPG, PNG (Max: 2MB) - Opsional</small>
                        @error('bukti_foto')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <!-- Hidden Lokasi Fields -->
                    <input type="hidden" name="latitude" id="latitude">
                    <input type="hidden" name="longitude" id="longitude">

                    <!-- Action Buttons -->
                    <div class="d-grid gap-2 mt-4">
                        <button type="submit" class="btn btn-success btn-lg" id="submitBtn">
                            <i class="fas fa-check-circle me-2"></i>Konfirmasi Presensi
                        </button>
                        <a href="{{ route('student.presensi.index') }}" class="btn btn-outline-secondary">
                            <i class="fas fa-arrow-left me-2"></i>Kembali
                        </a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- Load Google Maps -->
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>

<!-- Modal: Input Google Maps Link -->
<div class="modal fade" id="gmapModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title"><i class="fas fa-map-marker-alt me-2"></i>Isi Link Google Maps Tempat Magang</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <form id="gmapForm">
                @csrf
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="gmapLink" class="form-label">Link Google Maps <span class="text-danger">*</span></label>
                        <input type="url" class="form-control" id="gmapLink" name="gmap_link" placeholder="https://www.google.com/maps/place/..." required>
                        <small class="form-text text-muted d-block mt-2">
                            <strong>Cara mendapatkan link:</strong>
                            <ol class="mb-0 ms-3">
                                <li>Buka <a href="https://maps.google.com" target="_blank" class="text-primary">Google Maps</a></li>
                                <li>Cari alamat tempat magang Anda</li>
                                <li>Klik tombol <strong>"Bagikan"</strong> di bagian atas</li>
                                <li>Pilih tab <strong>"Salin tautan"</strong></li>
                                <li>Paste link di kolom di atas</li>
                            </ol>
                        </small>
                    </div>
                    <div id="gmapAlert" style="display: none;"></div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary" id="gmapSubmitBtn">
                        <i class="fas fa-check me-2"></i>Simpan Link
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

@push('scripts')
<script>
    // Handle submit form Google Maps
    document.getElementById('gmapForm').addEventListener('submit', function(e) {
        e.preventDefault();
        
        const gmapLink = document.getElementById('gmapLink').value;
        const submitBtn = document.getElementById('gmapSubmitBtn');
        const alertDiv = document.getElementById('gmapAlert');
        
        // Disable button and show loading state
        submitBtn.disabled = true;
        submitBtn.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span>Menyimpan...';
        
        fetch('{{ route("student.presensi.update-gmap") }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-Token': document.querySelector('input[name="_token"]').value
            },
            body: JSON.stringify({
                gmap_link: gmapLink
            })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                alertDiv.innerHTML = `<div class="alert alert-success mb-0">
                    <i class="fas fa-check-circle me-2"></i>${data.message}
                </div>`;
                alertDiv.style.display = 'block';
                
                // Reload page setelah 2 detik
                setTimeout(() => {
                    location.reload();
                }, 2000);
            } else {
                alertDiv.innerHTML = `<div class="alert alert-danger mb-0">
                    <i class="fas fa-exclamation-circle me-2"></i>${data.message}
                </div>`;
                alertDiv.style.display = 'block';
                submitBtn.disabled = false;
                submitBtn.innerHTML = '<i class="fas fa-check me-2"></i>Simpan Link';
            }
        })
        .catch(error => {
            alertDiv.innerHTML = `<div class="alert alert-danger mb-0">
                <i class="fas fa-exclamation-circle me-2"></i>Terjadi kesalahan: ${error.message}
            </div>`;
            alertDiv.style.display = 'block';
            submitBtn.disabled = false;
            submitBtn.innerHTML = '<i class="fas fa-check me-2"></i>Simpan Link';
        });
    });

    // Configuration untuk geolocation
    const companyLat = {{ auth()->user()->latitude ?? 'null' }};
    const companyLng = {{ auth()->user()->longitude ?? 'null' }};
    const maxDistanceInMeter = {{ isset($maxDistance) ? $maxDistance : 500 }};

    let currentLat = null;
    let currentLng = null;
    let map = null;
    let userMarker = null;
    let companyMarker = null;

    // Haversine formula untuk menghitung jarak
    function calculateDistance(lat1, lon1, lat2, lon2) {
        const R = 6371000; // Earth's radius in meters
        const φ1 = lat1 * Math.PI / 180;
        const φ2 = lat2 * Math.PI / 180;
        const Δφ = (lat2 - lat1) * Math.PI / 180;
        const Δλ = (lon2 - lon1) * Math.PI / 180;

        const a = Math.sin(Δφ / 2) * Math.sin(Δφ / 2) +
                Math.cos(φ1) * Math.cos(φ2) *
                Math.sin(Δλ / 2) * Math.sin(Δλ / 2);
        const c = 2 * Math.atan2(Math.sqrt(a), Math.sqrt(1 - a));
        const distance = R * c;

        return Math.round(distance);
    }

    // Tampilkan informasi lokasi magang
    function displayCompanyLocation() {
        const info = document.getElementById('companyLocationInfo');
        const buttonContainer = document.getElementById('gmapButtonContainer');
        
        if (!companyLat || !companyLng) {
            info.innerHTML = `
                <div class="text-warning">
                    <i class="fas fa-exclamation-triangle"></i> <strong>Status Lokasi</strong><br>
                    Titik koordinat magang belum diatur.
                </div>
            `;
            buttonContainer.style.display = 'block';
            return;
        }

        const mapsUrl = `https://www.google.com/maps?q=${companyLat},${companyLng}`;
        info.innerHTML = `
            <p class="mb-0">
                <strong><i class="fas fa-map-pin"></i> Koordinat Magang:</strong> ${companyLat.toFixed(6)}, ${companyLng.toFixed(6)}<br>
                <a href="${mapsUrl}" target="_blank" class="btn btn-sm btn-outline-primary mt-2">
                    <i class="fas fa-external-link-alt"></i> Buka di Google Maps
                </a>
            </p>
        `;
        buttonContainer.style.display = 'none';
    }

    // Request lokasi pengguna
    function requestLocation() {
        if (!navigator.geolocation) {
            alert('Browser Anda tidak mendukung fitur geolocation!');
            return;
        }

        document.getElementById('gpsWarning').style.display = 'none';

        navigator.geolocation.getCurrentPosition(
            function(position) {
                currentLat = position.coords.latitude;
                currentLng = position.coords.longitude;
                
                document.getElementById('latitude').value = currentLat;
                document.getElementById('longitude').value = currentLng;

                updateDistanceDisplay();
                showGpsSuccess();
            },
            function(error) {
                showGpsError(error);
            },
            {
                enableHighAccuracy: true,
                timeout: 10000,
                maximumAge: 30000
            }
        );
    }

    // Update tampilan jarak
    function updateDistanceDisplay() {
        if (!currentLat || !currentLng || !companyLat || !companyLng) {
            return;
        }

        const distance = calculateDistance(currentLat, currentLng, companyLat, companyLng);
        const distanceAlert = document.getElementById('distanceAlert');
        const distanceWarning = document.getElementById('distanceWarning');
        const submitBtn = document.getElementById('submitBtn');

        document.getElementById('distanceValue').textContent = distance;

        if (distance > maxDistanceInMeter) {
            distanceAlert.style.display = 'none';
            distanceWarning.style.display = 'block';
            document.getElementById('distanceWarningText').innerHTML = 
                `Jarak Anda: <strong>${distance} m</strong> | Maksimal: <strong>${maxDistanceInMeter} m</strong>`;
            submitBtn.disabled = true;
            submitBtn.classList.add('opacity-50');
        } else {
            distanceAlert.style.display = 'block';
            distanceWarning.style.display = 'none';
            submitBtn.disabled = false;
            submitBtn.classList.remove('opacity-50');
        }
    }

    // Tampilkan pesan sukses GPS
    function showGpsSuccess() {
        const warning = document.getElementById('gpsWarning');
        warning.style.display = 'none';
    }

    // Tampilkan pesan error GPS
    function showGpsError(error) {
        const warning = document.getElementById('gpsWarning');
        warning.style.display = 'block';
        
        let errorMsg = 'Tidak dapat mengakses lokasi.';
        if (error.code === error.PERMISSION_DENIED) {
            errorMsg = 'Akses lokasi ditolak. Silakan ubah izin di pengaturan browser.';
        }
    }

    // Inisialisasi
    document.addEventListener('DOMContentLoaded', function() {
        displayCompanyLocation();
        
        // Request lokasi saat form dimuat
        setTimeout(requestLocation, 500);

        // Request lokasi ulang saat user focus ke form
        document.getElementById('status').addEventListener('focus', function() {
            if (!currentLat || !currentLng) {
                requestLocation();
            }
        }, { once: false });

        // Update jarak setiap kali status berubah
        document.getElementById('status').addEventListener('change', updateDistanceDisplay);
    });
</script>
@endpush
@endsection
