@extends('layouts.app')

@section('title', 'Presensi - SIMagang')
@section('page-title', 'Presensi Hari Ini')

@section('content')

<!-- Notification: Pending Location Change Request -->
@if($hasPendingLocationRequest && $pendingLocationRequest)
<div class="alert alert-warning alert-dismissible fade show mb-4" role="alert" id="pendingLocationAlert">
    <h6 class="alert-heading mb-2">
        <i class="fas fa-hourglass-half me-2"></i>
        Permintaan Terkirim
    </h6>
    <p class="mb-2 small">
        <strong>Permintaan ubah titik koordinat lokasi magang telah dikirim ke admin.</strong><br>
        Tunggu persetujuan. Anda akan mendapat notifikasi saat sudah disetujui atau ditolak.
    </p>
    <small class="text-muted">
        <i class="fas fa-info-circle me-1"></i>
        Lokasi lama: <code>{{ round($pendingLocationRequest->old_latitude, 6) }}, {{ round($pendingLocationRequest->old_longitude, 6) }}</code>
    </small>
    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close" onclick="closeNotification('pendingLocationAlert')"></button>
</div>
@endif

<!-- Form Presensi -->
<div class="card shadow mb-4">
    <div class="card-header py-3 bg-success text-white d-flex justify-content-between align-items-center">
        <h6 class="m-0 font-weight-bold"><i class="fas fa-clock me-2"></i>Form Presensi</h6>
        <button type="button" class="btn btn-sm btn-light" data-bs-toggle="modal" data-bs-target="#tutorialPresensiModal" title="Lihat Tutorial">
            <i class="fas fa-question-circle"></i>
        </button>
    </div>
    <div class="card-body">
        <form id="presensiForm" action="{{ route('student.presensi.store') }}" method="POST" enctype="multipart/form-data">
            @csrf
            
            <!-- Hidden fields for server-side data -->
            <input type="hidden" id="tanggal_hidden" name="tanggal">
            <input type="hidden" id="jam_masuk_hidden" name="jam_masuk">
            <input type="hidden" id="zone_confirmed" name="zone_confirmed" value="0">
            
            <!-- Realtime Clock Display -->
            <div class="text-center mb-4 p-3 bg-light rounded shadow-sm">
                <div id="jam_masuk_display" class="display-3 fw-bold text-dark mb-1">00:00:00</div>
                <div id="tanggal_display" class="fs-5 text-dark fw-semibold">Memuat tanggal...</div>
            </div>

            <!-- Wajib: Link Google Maps lokasi magang -->
            <div class="alert {{ $hasGmap ? 'alert-success' : 'alert-warning' }} mb-4" id="gmapRequiredAlert">
                <h5 class="alert-heading mb-2">
                    <i class="fas fa-map-marker-alt me-2"></i>
                    Langkah 1: Lokasi Magang (Wajib)
                </h5>
                <div id="locationStatus">
                    <p class="mb-0 small">Memuat status lokasi...</p>
                </div>
                @unless($hasGmap)
                <div class="mt-3">
                    <button type="button" class="btn btn-primary" onclick="openGmapModal()">
                        <i class="fas fa-plus me-2"></i>Isi Link Google Maps Lokasi Magang
                    </button>
                    <p class="small text-muted mb-0 mt-2">Tombol presensi akan aktif setelah link map disimpan.</p>
                </div>
                @else
                <div class="mt-2">
                    @if($hasPendingLocationRequest)
                        <div class="alert alert-warning py-2 small mb-2">
                            <i class="fas fa-hourglass-half me-1"></i>
                            Permintaan ubah lokasi sedang menunggu persetujuan admin.
                        </div>
                    @else
                        <button type="button" class="btn btn-sm btn-outline-primary" onclick="openGmapModal()">
                            <i class="fas fa-edit me-1"></i>Ubah Titik Koordinat
                        </button>
                    @endif
                </div>
                @endunless
            </div>

            <!-- Geofencing Status Display -->
            <div id="geofence-alert" class="alert alert-secondary text-center mb-4" style="display: none;">
                <h5 class="alert-heading mb-1"><i class="fas fa-map-marker-alt me-2"></i>Status Lokasi</h5>
                <p class="mb-0 fw-bold" id="geofence-status-text">Mengecek lokasi...</p>
                <small id="geofence-distance-text"></small>
            </div>
            
            <input type="hidden" id="latitude_masuk" name="latitude">
            <input type="hidden" id="longitude_masuk" name="longitude">

            <div id="presensiFormSection" class="{{ $hasGmap ? '' : 'opacity-50' }}">
            <p class="text-muted small mb-3" id="presensiStepHint">
                <i class="fas fa-clipboard-check me-1"></i>
                Langkah 2: Pilih status lalu tekan tombol Masuk (setelah lokasi map diisi).
            </p>
            <div class="row justify-content-center">
                <div class="col-md-6 mb-3">
                    <label for="status" class="form-label">Status Presensi <span class="text-danger">*</span></label>
                    <select class="form-select form-select-lg @error('status') is-invalid @enderror" 
                            id="status" name="status" required onchange="toggleKeteranganFoto()">
                        <option value="">-- Pilih Status --</option>
                        <option value="hadir" {{ old('status') == 'hadir' ? 'selected' : '' }}>Hadir</option>
                        <option value="izin" {{ old('status') == 'izin' ? 'selected' : '' }}>Izin</option>
                        <option value="sakit" {{ old('status') == 'sakit' ? 'selected' : '' }}>Sakit</option>
                    </select>
                    @error('status')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
            </div>

            <!-- Keterangan dan Foto - Hanya tampil saat Izin atau Sakit -->
            <div id="keteranganFotoSection" style="display: none;">
                <div class="mb-3">
                    <label for="keterangan" class="form-label">Keterangan *</label>
                    <textarea class="form-control @error('keterangan') is-invalid @enderror" 
                              id="keterangan" name="keterangan" rows="3">{{ old('keterangan') }}</textarea>
                    @error('keterangan')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="mb-3">
                    <label for="bukti_foto" class="form-label">Bukti Foto <span class="text-danger">*</span></label>
                    <input type="file" class="form-control @error('bukti_foto') is-invalid @enderror" 
                           id="bukti_foto" name="bukti_foto" accept="image/jpeg,image/jpg,image/png">
                    <small class="text-muted">Wajib untuk Izin/Sakit. Format: JPG, JPEG, PNG. Maks 2MB</small>
                    @error('bukti_foto')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
            </div>

            <!-- Tombol Masuk dan Keluar Bersebelahan -->
            <div class="row">
                <div class="col-md-6 mb-2">
                    <button type="submit" class="btn btn-success w-100" id="btnMasuk" disabled>
                        <i class="fas fa-sign-in-alt me-2"></i>Masuk
                    </button>
                </div>
                <div class="col-md-6 mb-2">
                    <button type="button" class="btn btn-warning w-100" id="btnKeluar" disabled onclick="submitCheckout()">
                        <i class="fas fa-sign-out-alt me-2"></i>Keluar
                    </button>
                </div>
            </div>
            </div>
        </form>
        
        <!-- Form terpisah untuk checkout -->
        <form id="checkoutForm" action="{{ route('student.presensi.checkout') }}" method="POST" class="d-none">
            @csrf
            <input type="hidden" name="jam_keluar" id="jam_keluar_checkout">
            <input type="hidden" name="latitude" id="latitude_keluar">
            <input type="hidden" name="longitude" id="longitude_keluar">
        </form>
    </div>
</div>

<!-- Tombol Riwayat -->
<div class="row mb-4">
    <div class="col-12">
        <a href="{{ route('student.presensi.riwayat') }}" class="btn btn-primary w-100">
            <i class="fas fa-history me-2"></i>Lihat Riwayat Presensi
        </a>
    </div>
</div>

<script>
    const hasGmap = {{ $hasGmap ? 'true' : 'false' }};
    const hasExistingGmap = hasGmap;
    const alreadyPresensiMasuk = {{ $presensiHariIni ? 'true' : 'false' }};
    const alreadyCheckout = {{ ($presensiHariIni && $presensiHariIni->jam_keluar) ? 'true' : 'false' }};
    const targetLat = {{ $targetLat ?? 'null' }};
    const targetLng = {{ $targetLng ?? 'null' }};
    const radiusHijau = {{ $radiusHijau ?? 30 }};
    const radiusKuning = {{ $radiusKuning ?? 70 }};

    let tanggalPresensi = '';
    let jamMasukPresensi = '';
    let currentZone = 'none';
    let currentDistance = null;

    const namaBulan = [
        'Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni',
        'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'
    ];
    
    function updateRealtimeDateTime() {
        const now = new Date();
        
        // Format tanggal untuk server: YYYY-MM-DD
        const year = now.getFullYear();
        const month = String(now.getMonth() + 1).padStart(2, '0');
        const day = String(now.getDate()).padStart(2, '0');
        const tanggalServer = `${year}-${month}-${day}`;
        
        // Format tanggal untuk tampilan: DD NamaBulan YYYY (contoh: 14 April 2024)
        const dayDisplay = now.getDate();
        const monthDisplay = namaBulan[now.getMonth()];
        const tanggalDisplay = `${dayDisplay} ${monthDisplay} ${year}`;
        
        // Format jam: HH:MM:SS
        const hours = String(now.getHours()).padStart(2, '0');
        const minutes = String(now.getMinutes()).padStart(2, '0');
        const seconds = String(now.getSeconds()).padStart(2, '0');
        const jam = `${hours}:${minutes}:${seconds}`;
        
        // Simpan tanggal dan jam saat halaman diload (tidak bisa diubah)
        if (!tanggalPresensi) {
            tanggalPresensi = tanggalServer;
            jamMasukPresensi = jam;
            
            // Update hidden fields (yang dikirim ke server)
            document.getElementById('tanggal_hidden').value = tanggalPresensi;
            document.getElementById('jam_masuk_hidden').value = jamMasukPresensi;
        }
        
        // Update display fields (yang dilihat user)
        document.getElementById('tanggal_display').innerText = tanggalDisplay;
        document.getElementById('jam_masuk_display').innerText = jam;
        
        // Update jam keluar untuk checkout
        document.getElementById('jam_keluar_checkout').value = jam;
    }
    
    function submitCheckout() {
        document.getElementById('checkoutForm').submit();
    }

    function toggleKeteranganFoto() {
        const status = document.getElementById('status').value;
        const section = document.getElementById('keteranganFotoSection');
        const keterangan = document.getElementById('keterangan');
        const buktiFoto = document.getElementById('bukti_foto');

        if (status === 'izin' || status === 'sakit') {
            section.style.display = 'block';
            keterangan.required = true;
            buktiFoto.required = true;
        } else {
            section.style.display = 'none';
            keterangan.required = false;
            buktiFoto.required = false;
            keterangan.value = '';
            buktiFoto.value = '';
        }
        updatePresensiButtons();
    }

    function calculateDistance(lat1, lon1, lat2, lon2) {
        const R = 6371e3; // metres
        const φ1 = lat1 * Math.PI/180;
        const φ2 = lat2 * Math.PI/180;
        const Δφ = (lat2-lat1) * Math.PI/180;
        const Δλ = (lon2-lon1) * Math.PI/180;

        const a = Math.sin(Δφ/2) * Math.sin(Δφ/2) +
                Math.cos(φ1) * Math.cos(φ2) *
                Math.sin(Δλ/2) * Math.sin(Δλ/2);
        const c = 2 * Math.atan2(Math.sqrt(a), Math.sqrt(1-a));

        return R * c; // in metres
    }

    function updatePresensiButtons() {
        const btnMasuk = document.getElementById('btnMasuk');
        const btnKeluar = document.getElementById('btnKeluar');
        const status = document.getElementById('status').value;

        if (alreadyPresensiMasuk) {
            btnMasuk.disabled = true;
            btnKeluar.disabled = !hasGmap || alreadyCheckout;
            return;
        }

        if (!hasGmap) {
            btnMasuk.disabled = true;
            btnKeluar.disabled = true;
            return;
        }

        btnMasuk.disabled = !status;
        btnKeluar.disabled = true;
    }

    function initGeofence() {
        const alertBox = document.getElementById('geofence-alert');
        const statusText = document.getElementById('geofence-status-text');
        const distanceText = document.getElementById('geofence-distance-text');
        const btnMasuk = document.getElementById('btnMasuk');
        const btnKeluar = document.getElementById('btnKeluar');

        if (!hasGmap || !targetLat || !targetLng) {
            alertBox.style.display = 'block';
            alertBox.className = 'alert alert-warning text-center mb-4';
            statusText.innerText = 'Link Google Maps belum diisi.';
            distanceText.innerHTML = 'Isi <strong>link lokasi magang</strong> terlebih dahulu sebelum presensi.';
            btnMasuk.disabled = true;
            return;
        }

        if (navigator.geolocation) {
            alertBox.style.display = 'block';

            navigator.geolocation.watchPosition(function(position) {
                const currentLat = position.coords.latitude;
                const currentLng = position.coords.longitude;

                document.getElementById('latitude_masuk').value = currentLat;
                document.getElementById('longitude_masuk').value = currentLng;
                document.getElementById('latitude_keluar').value = currentLat;
                document.getElementById('longitude_keluar').value = currentLng;

                currentDistance = Math.round(calculateDistance(currentLat, currentLng, targetLat, targetLng));
                distanceText.innerText = `Jarak Anda: ${currentDistance} meter`;

                if (currentDistance <= radiusHijau) {
                    currentZone = 'hijau';
                    alertBox.className = 'alert alert-success text-center mb-4';
                    statusText.innerHTML = '<i class="fas fa-check-circle me-1"></i> Zona Hijau — presensi langsung diizinkan';
                } else if (currentDistance <= radiusKuning) {
                    currentZone = 'kuning';
                    alertBox.className = 'alert alert-warning text-center mb-4';
                    statusText.innerHTML = '<i class="fas fa-exclamation-triangle me-1"></i> Zona Kuning — peringatan: lebih dekat lebih baik';
                    distanceText.innerText += '. Konfirmasi diperlukan saat absen masuk.';
                } else {
                    currentZone = 'merah';
                    alertBox.className = 'alert alert-danger text-center mb-4';
                    statusText.innerHTML = '<i class="fas fa-times-circle me-1"></i> Zona Merah — presensi ditolak';
                    distanceText.innerText += '. Terlalu jauh dari lokasi magang.';
                }

                updatePresensiButtons();
            }, function() {
                currentZone = 'none';
                alertBox.className = 'alert alert-danger text-center mb-4';
                statusText.innerText = 'GPS Belum Siap';
                distanceText.innerHTML = 'Tunggu hingga lokasi GPS terdeteksi, atau pastikan izin lokasi diaktifkan.';
                
                // Show GPS enable button
                const gpsButtonContainer = document.getElementById('gpsButtonContainer');
                if (gpsButtonContainer) {
                    gpsButtonContainer.style.display = 'block';
                }
                
                btnMasuk.disabled = true;
            }, {
                enableHighAccuracy: true,
                maximumAge: 0
            });
        } else {
            alertBox.style.display = 'block';
            alertBox.className = 'alert alert-danger text-center mb-4';
            statusText.innerText = 'Browser tidak mendukung geolokasi.';
            btnMasuk.disabled = true;
        }
    }

    function handlePresensiSubmit(e) {
        if (!hasGmap) {
            e.preventDefault();
            Swal.fire({
                icon: 'warning',
                title: 'Lokasi Magang Belum Diisi',
                text: 'Anda belum mengisi link Google Maps lokasi magang. Isi terlebih dahulu sebelum presensi.',
                confirmButtonText: 'Isi Sekarang'
            }).then((result) => {
                if (result.isConfirmed) {
                    new bootstrap.Modal(document.getElementById('gmapModal')).show();
                }
            });
            return false;
        }

        const status = document.getElementById('status').value;
        if (!status) {
            e.preventDefault();
            Swal.fire({ icon: 'warning', title: 'Pilih Status', text: 'Silakan pilih status presensi terlebih dahulu.' });
            return false;
        }

        if (status === 'hadir') {
            if (currentZone === 'none') {
                e.preventDefault();
                Swal.fire({
                    icon: 'warning',
                    title: 'GPS Belum Siap',
                    text: 'Tunggu hingga lokasi GPS terdeteksi, atau pastikan izin lokasi diaktifkan.'
                });
                return false;
            }
            if (currentZone === 'merah') {
                e.preventDefault();
                Swal.fire({
                    icon: 'error',
                    title: 'Presensi Ditolak',
                    html: `Anda berada di <strong>zona merah</strong> (${currentDistance ?? '-'} m dari lokasi magang).<br>Mendekat ke lokasi magang diperlukan.`
                });
                return false;
            }

            if (currentZone === 'kuning') {
                e.preventDefault();
                Swal.fire({
                    icon: 'warning',
                    title: 'Zona Kuning',
                    html: `Jarak Anda <strong>${currentDistance} meter</strong> dari lokasi magang.<br><br>Lebih dekat ke lokasi magang lebih baik. Tetap lanjutkan presensi?`,
                    showCancelButton: true,
                    confirmButtonText: 'Ya, Lanjutkan',
                    cancelButtonText: 'Batal'
                }).then((result) => {
                    if (result.isConfirmed) {
                        document.getElementById('zone_confirmed').value = '1';
                        document.getElementById('presensiForm').submit();
                    }
                });
                return false;
            }
        }

        document.getElementById('zone_confirmed').value = '0';
        return true;
    }

    function openGmapModal() {
        if (hasExistingGmap) {
            Swal.fire({
                icon: 'question',
                title: 'Ubah Titik Koordinat?',
                html: 'Permintaan akan dikirim ke <strong>admin</strong> untuk disetujui.<br>Anda akan mendapat notifikasi setelah diproses.',
                showCancelButton: true,
                confirmButtonText: 'Ya, Ajukan Perubahan',
                cancelButtonText: 'Batal'
            }).then((result) => {
                if (result.isConfirmed) {
                    new bootstrap.Modal(document.getElementById('gmapModal')).show();
                }
            });
        } else {
            new bootstrap.Modal(document.getElementById('gmapModal')).show();
        }
    }

    document.addEventListener('DOMContentLoaded', function() {
        updateRealtimeDateTime();
        toggleKeteranganFoto();
        displayLocationStatus();
        initGeofence();
        updatePresensiButtons();

        if (alreadyPresensiMasuk) {
            document.getElementById('btnMasuk').disabled = true;
            document.getElementById('btnMasuk').innerHTML = '<i class="fas fa-check me-2"></i>Sudah Presensi Masuk';
            if (alreadyCheckout) {
                document.getElementById('btnKeluar').disabled = true;
                document.getElementById('btnKeluar').innerHTML = '<i class="fas fa-check me-2"></i>Sudah Checkout';
            }
        }

        document.getElementById('presensiForm').addEventListener('submit', handlePresensiSubmit);
        setInterval(updateRealtimeDateTime, 1000);
    });

    function displayLocationStatus() {
        const userLat = {{ auth()->user()->latitude ?? 'null' }};
        const userLng = {{ auth()->user()->longitude ?? 'null' }};
        const locationDiv = document.getElementById('locationStatus');

        if (!hasGmap) {
            locationDiv.innerHTML = `
                <div class="text-warning mb-0">
                    <i class="fas fa-exclamation-circle me-2"></i>
                    <strong>Belum ada link lokasi magang.</strong> Wajib diisi sebelum absen.
                </div>
            `;
        } else {
            const mapsUrl = 'https://www.google.com/maps?q=' + userLat + ',' + userLng;
            locationDiv.innerHTML = `
                <div class="text-success mb-0">
                    <i class="fas fa-check-circle me-2"></i>
                    <strong>Lokasi magang sudah terdaftar.</strong>
                </div>
                <small class="text-muted d-block mt-1">
                    Koordinat: ${userLat.toFixed(6)}, ${userLng.toFixed(6)}
                    <a href="${mapsUrl}" target="_blank" class="ms-2"><i class="fas fa-external-link-alt"></i> Lihat</a>
                </small>
            `;
        }
    }
</script>

<!-- Modal: Input Google Maps Link -->
<div class="modal fade" id="gmapModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title"><i class="fas fa-map-marker-alt me-2"></i>Isi Link Google Maps</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <form id="gmapFormIndex">
                @csrf
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="gmapLinkIndex" class="form-label">Link Google Maps <span class="text-danger">*</span></label>
                        <input type="url" class="form-control" id="gmapLinkIndex" name="gmap_link" 
                               placeholder="https://www.google.com/maps/place/..." required>
                        <small class="form-text text-muted d-block mt-2">
                            <strong>Cara:</strong>
                            <ol class="mb-0 ms-3 small">
                                <li>Buka <a href="https://maps.google.com" target="_blank">Google Maps</a></li>
                                <li>Cari lokasi magang Anda</li>
                                <li>Klik "Bagikan" → "Salin tautan"</li>
                                <li>Paste link di atas</li>
                            </ol>
                        </small>
                    </div>
                    <div id="gmapAlertIndex" style="display: none;"></div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-secondary btn-sm" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary btn-sm" id="gmapSubmitBtnIndex">
                        <i class="fas fa-check me-2"></i>Simpan
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
    // Close notification manually
    function closeNotification(elementId) {
        const element = document.getElementById(elementId);
        if (element) {
            element.style.display = 'none';
        }
    }

    // Enable GPS - prompt user to allow GPS access
    function enableGPS() {
        if (navigator.geolocation) {
            Swal.fire({
                icon: 'info',
                title: 'Mengaktifkan GPS',
                html: '<p>Sistem meminta izin untuk mengakses lokasi Anda.</p>' +
                      '<p class="small text-muted">Pastikan browser sudah memberi izin lokasi.</p>',
                confirmButtonText: 'Buka Pengaturan GPS',
                showCancelButton: true,
                cancelButtonText: 'Batal'
            }).then((result) => {
                if (result.isConfirmed) {
                    // Guide user based on device type
                    const ua = navigator.userAgent;
                    if (/android/i.test(ua)) {
                        Swal.fire({
                            icon: 'info',
                            title: 'Panduan Android',
                            html: '<ol class="text-start small">' +
                                  '<li>Buka Pengaturan</li>' +
                                  '<li>Tap Lokasi (Location)</li>' +
                                  '<li>Aktifkan (turn ON)</li>' +
                                  '<li>Kembali ke aplikasi</li>' +
                                  '</ol>',
                            confirmButtonText: 'OK'
                        });
                    } else if (/iphone|ipad|ipod/i.test(ua)) {
                        Swal.fire({
                            icon: 'info',
                            title: 'Panduan iOS',
                            html: '<ol class="text-start small">' +
                                  '<li>Buka Pengaturan</li>' +
                                  '<li>Tap Privasi (Privacy)</li>' +
                                  '<li>Tap Lokasi (Location)</li>' +
                                  '<li>Aktifkan (turn ON)</li>' +
                                  '<li>Kembali ke aplikasi</li>' +
                                  '</ol>',
                            confirmButtonText: 'OK'
                        });
                    } else {
                        Swal.fire({
                            icon: 'info',
                            title: 'Aktifkan GPS',
                            html: '<p class="small">' +
                                  'Buka pengaturan perangkat Anda dan aktifkan Lokasi/GPS.<br>' +
                                  'Kemudian izinkan browser/aplikasi mengakses lokasi Anda.' +
                                  '</p>',
                            confirmButtonText: 'OK'
                        });
                    }
                }
            });
        } else {
            Swal.fire({
                icon: 'error',
                title: 'Geolokasi Tidak Didukung',
                text: 'Browser Anda tidak mendukung fitur geolokasi.'
            });
        }
    }

    // Handle submit form Google Maps di index
    document.getElementById('gmapFormIndex').addEventListener('submit', function(e) {
        e.preventDefault();
        
        const gmapLink = document.getElementById('gmapLinkIndex').value;
        const submitBtn = document.getElementById('gmapSubmitBtnIndex');
        const alertDiv = document.getElementById('gmapAlertIndex');
        
        submitBtn.disabled = true;
        submitBtn.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span>Menyimpan...';
        
        fetch('{{ route("student.presensi.update-gmap") }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                'Accept': 'application/json',
            },
            body: JSON.stringify({
                gmap_link: gmapLink,
                is_change_request: hasExistingGmap
            })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                alertDiv.innerHTML = `<div class="alert alert-success alert-sm mb-0" style="font-size: 0.875rem;">
                    <i class="fas fa-check-circle me-2"></i>${data.message}
                </div>`;
                alertDiv.style.display = 'block';
                
                setTimeout(() => {
                    location.reload();
                }, 1500);
            } else {
                alertDiv.innerHTML = `<div class="alert alert-danger alert-sm mb-0" style="font-size: 0.875rem;">
                    <i class="fas fa-exclamation-circle me-2"></i>${data.message}
                </div>`;
                alertDiv.style.display = 'block';
                submitBtn.disabled = false;
                submitBtn.innerHTML = '<i class="fas fa-check me-2"></i>Simpan';
            }
        })
        .catch(error => {
            alertDiv.innerHTML = `<div class="alert alert-danger alert-sm mb-0" style="font-size: 0.875rem;">
                <i class="fas fa-exclamation-circle me-2"></i>Error: ${error.message}
            </div>`;
            alertDiv.style.display = 'block';
            submitBtn.disabled = false;
            submitBtn.innerHTML = '<i class="fas fa-check me-2"></i>Simpan';
        });
    });
</script>


<!-- Modal Tutorial Presensi -->
<div class="modal fade" id="tutorialPresensiModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title"><i class="fas fa-graduation-cap me-2"></i>Tutorial Presensi</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div class="accordion" id="presensiTutorial">
                    <div class="accordion-item">
                        <h2 class="accordion-header">
                            <button class="accordion-button" type="button" data-bs-toggle="collapse" data-bs-target="#tutorial1">
                                <i class="fas fa-map-marker-alt me-2 text-primary"></i> 1. Isi Link Google Maps Lokasi Magang
                            </button>
                        </h2>
                        <div id="tutorial1" class="accordion-collapse collapse show" data-bs-parent="#presensiTutorial">
                            <div class="accordion-body">
                                <p><strong>Langkah:</strong></p>
                                <ol>
                                    <li>Klik tombol "Isi Link Google Maps Lokasi Magang"</li>
                                    <li>Buka Google Maps dan cari alamat tempat magang Anda</li>
                                    <li>Klik tombol Bagikan dan salin link-nya</li>
                                    <li>Paste link di form modal dan simpan</li>
                                </ol>
                                <p class="text-muted small"><i class="fas fa-info-circle me-1"></i> Lokasi ini digunakan untuk verifikasi presensi Anda berada di tempat yang benar.</p>
                            </div>
                        </div>
                    </div>
                    <div class="accordion-item">
                        <h2 class="accordion-header">
                            <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#tutorial2">
                                <i class="fas fa-check-circle me-2 text-success"></i> 2. Melakukan Presensi Masuk
                            </button>
                        </h2>
                        <div id="tutorial2" class="accordion-collapse collapse" data-bs-parent="#presensiTutorial">
                            <div class="accordion-body">
                                <p><strong>Langkah:</strong></p>
                                <ol>
                                    <li>Pastikan GPS sudah aktif di perangkat Anda</li>
                                    <li>Pilih status presensi: Hadir, Izin, atau Sakit</li>
                                    <li>Jika Hadir, sistem akan memeriksa lokasi GPS Anda</li>
                                    <li>Klik tombol "Masuk" untuk submit presensi</li>
                                </ol>
                                <div class="alert alert-warning mb-0">
                                    <i class="fas fa-exclamation-triangle me-1"></i>
                                    <strong>Zona Lokasi:</strong> Hijau (dekat), Kuning (sedang), Merah (jauh)
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="accordion-item">
                        <h2 class="accordion-header">
                            <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#tutorial3">
                                <i class="fas fa-sign-out-alt me-2 text-warning"></i> 3. Checkout (Presensi Keluar)
                            </button>
                        </h2>
                        <div id="tutorial3" class="accordion-collapse collapse" data-bs-parent="#presensiTutorial">
                            <div class="accordion-body">
                                <p><strong>Langkah:</strong></p>
                                <ol>
                                    <li>Klik tombol "Keluar" saat akan pulang</li>
                                    <li>Sistem akan merekam waktu dan lokasi keluar Anda</li>
                                    <li>Riwayat presensi dapat dilihat di "Lihat Riwayat Presensi"</li>
                                </ol>
                                <p class="text-muted small"><i class="fas fa-info-circle me-1"></i> Pastikan melakukan checkout di akhir hari kerja.</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@endsection
