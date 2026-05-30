# Dokumentasi Fitur: Presensi Berbasis Lokasi GPS

**Tanggal Implementasi:** 20 Mei 2026
**Status:** ✅ AKTIF

---

## 📋 Daftar Isi

1. [Pendahuluan](#pendahuluan)
2. [Fitur Utama](#fitur-utama)
3. [Setup & Konfigurasi](#setup--konfigurasi)
4. [Panduan Admin](#panduan-admin)
5. [Panduan Siswa](#panduan-siswa)
6. [Teknis & Troubleshooting](#teknis--troubleshooting)

---

## 🎯 Pendahuluan

**Fitur Presensi Berbasis Lokasi GPS** memungkinkan sistem untuk memvalidasi lokasi siswa saat melakukan presensi. Sistem ini menggunakan **Haversine Formula** untuk menghitung jarak antara lokasi GPS siswa dengan lokasi magang yang tersimpan di profil.

### Keuntungan:
✅ Validasi lokasi otomatis untuk presensi  
✅ Jarak dapat dikonfigurasi oleh admin  
✅ Mencegah siswa presensi dari lokasi yang tidak sesuai  
✅ Laporan presensi mencakup data lokasi GPS  
✅ Integrasi dengan Google Maps untuk tampilan lokasi  

---

## 🚀 Fitur Utama

### 1. **Sistem Validasi Jarak**
- Admin dapat mengatur jarak maksimal presensi (contoh: 500 meter)
- Jarak dihitung otomatis dari GPS siswa ke lokasi magang
- Siswa hanya bisa presensi jika berada dalam radius yang ditentukan

### 2. **GPS Tracking**
- Sistem otomatis mendeteksi lokasi GPS siswa saat membuka form presensi
- Menampilkan jarak real-time dari lokasi magang
- Integrasi dengan Google Maps untuk lihat lokasi

### 3. **Pengaturan Admin**
- Menu khusus admin untuk mengatur jarak maksimal presensi
- Dapat diaktifkan/nonaktifkan sesuai kebutuhan
- Satuan jarak: meter atau kilometer

### 4. **Laporan Lokasi**
- Riwayat presensi menampilkan koordinat GPS siswa
- Tombol untuk membuka lokasi di Google Maps
- Data lengkap untuk audit dan verifikasi

---

## ⚙️ Setup & Konfigurasi

### File yang Dibuat/Dimodifikasi:

**Models:**
- `app/Models/PresenceDistanceSetting.php` - Model untuk pengaturan jarak

**Controllers:**
- `app/Http/Controllers/Admin/PresenceDistanceSettingController.php` - Controller admin
- `app/Http/Controllers/Student/PresensiController.php` - Updated dengan validasi jarak

**Helpers:**
- `app/Helpers/LocationHelper.php` - Helper untuk hitung jarak Haversine

**Views:**
- `resources/views/admin/presence/distance-setting.blade.php` - Form pengaturan
- `resources/views/student/presensi/create.blade.php` - Updated dengan GPS
- `resources/views/student/presensi/riwayat.blade.php` - Updated dengan lokasi

**Routes:**
- `routes/web.php` - Routes untuk admin presence distance

**Migrations:**
- `database/migrations/2026_05_20_000001_create_presence_distance_settings_table.php`

### Database Schema:

```sql
CREATE TABLE presence_distance_settings (
    id BIGINT PRIMARY KEY AUTO_INCREMENT,
    jarak_maksimal DOUBLE DEFAULT 500 -- Jarak dalam meter
    satuan VARCHAR(10) DEFAULT 'meter' -- 'meter' atau 'km'
    deskripsi TEXT NULL,
    aktif BOOLEAN DEFAULT true,
    created_at TIMESTAMP,
    updated_at TIMESTAMP
);
```

**Default Value:**
- Jarak Maksimal: **500 meter**
- Satuan: **meter**
- Status: **Aktif**

---

## 👨‍💼 Panduan Admin

### 1. Akses Menu Pengaturan Jarak Presensi

**Path:** `Admin Dashboard > Pengaturan Jarak Presensi`

Atau buka URL: `/admin/presence-distance`

### 2. Konfigurasi Jarak Presensi

**Langkah-langkah:**

1. Klik menu "**Pengaturan Jarak Presensi**" di sidebar admin
2. Anda akan melihat form dengan field:
   - **Jarak Maksimal:** Input angka (default: 500)
   - **Satuan:** Dropdown (meter / km)
   - **Deskripsi:** Catatan tambahan (opsional)
   - **Aktifkan:** Checkbox untuk mengaktifkan/nonaktifkan

3. **Ubah Nilai Jarak:**
   - Ketik nilai baru di field "Jarak Maksimal"
   - Pilih satuan (meter atau km)
   - Contoh: 500 meter atau 1 km

4. **Tambah Deskripsi (opsional):**
   - Contoh: "Jarak maksimal untuk kantor pusat"
   
5. **Aktivasi/Nonaktivasi:**
   - Centang checkbox jika ingin aktif
   - Unchecked jika ingin nonaktif (bypass validasi jarak)

6. **Simpan:**
   - Klik tombol "**Simpan Pengaturan**"
   - Sistem akan menampilkan pesan sukses

### 3. Rekomendasi Jarak

| Lokasi | Rekomendasi |
|--------|-------------|
| Kantor kecil/rumah | 300-500 meter |
| Kantor menengah | 500-1000 meter |
| Pabrik/lokasi luas | 1-2 km |
| Multi-lokasi | 2-5 km |

### 4. Pengaruh Pengaturan

**Jika Diaktifkan:**
- ✅ Validasi jarak berjalan otomatis
- ⚠️ Siswa tidak bisa presensi jika di luar jarak
- 📊 Laporan presensi menampilkan data lokasi

**Jika Dinonaktifkan:**
- ✅ Semua siswa bisa presensi tanpa validasi jarak
- ❌ Fitur GPS masih bekerja tapi tidak memblokir presensi
- 📊 Lokasi tetap tercatat di laporan

---

## 👨‍🎓 Panduan Siswa

### 1. Persyaratan GPS

**Perangkat yang Digunakan:**
- Laptop/PC dengan **browser modern** (Chrome, Firefox, Safari, Edge)
- Smartphone dengan **GPS aktif**
- Koneksi internet **stabil**

**Izin yang Diperlukan:**
- Izinkan akses lokasi saat diminta browser
- Jika ditolak, buka pengaturan browser dan aktifkan lokasi

### 2. Langkah Presensi dengan GPS

#### A. Persiapan Sebelum Presensi:

1. **Update Profil Lokasi Magang:**
   - Klik menu "Profile"
   - Pastikan lokasi magang sudah diisi dengan koordinat GPS atau link Google Maps
   - Jika belum, update segera sebelum presensi

2. **Aktifkan GPS di Perangkat:**
   - Smartphone: Settings > Location > ON
   - Laptop: Izinkan browser akses lokasi

#### B. Proses Presensi:

1. **Buka Menu Presensi:**
   - Klik "**Presensi**" di menu sidebar siswa
   - Klik tombol "**Input Presensi**" atau "**Buat Presensi Baru**"

2. **Sistem Mendeteksi GPS:**
   - Halaman form akan membuka
   - Sistem otomatis mendeteksi lokasi GPS
   - Jika berhasil, akan muncul info lokasi magang

3. **Lihat Informasi Lokasi:**
   - **Lokasi Magang:** Koordinat dan tombol buka Google Maps
   - **Jarak dari Magang:** Real-time jarak dalam meter
   - **Peringatan Jarak (jika terlalu jauh):** Pesan merah dengan jarak actual vs maksimal

4. **Isi Form Presensi:**
   - **Status:** Pilih Hadir/Izin/Sakit
   - **Keterangan:** Deskripsi (opsional)
   - **Bukti Foto:** Upload foto (opsional)

5. **Konfirmasi Presensi:**
   - Klik tombol "**Konfirmasi Presensi**"
   - **Jika dalam jarak:** Presensi berhasil tersimpan
   - **Jika di luar jarak:** Sistem akan menolak dan tampil error

#### C. Jika GPS Tidak Terdeteksi:

1. Sistem akan menampilkan pesan: "GPS belum terdeteksi"
2. Cek:
   - Apakah browser sudah diberi izin akses lokasi?
   - Apakah GPS aktif?
   - Apakah koneksi internet stabil?
3. Klik tombol "**Deteksi GPS Ulang**" untuk mencoba lagi
4. Jika tetap gagal, hubungi admin

#### D. Jika Jarak Terlalu Jauh:

1. Sistem akan tampilkan peringatan merah:
   - "Lokasi Anda terlalu jauh dari lokasi magang"
   - Menampilkan jarak actual vs maksimal

2. Solusi:
   - Pindah ke lokasi magang yang sebenarnya
   - Tunggu sampai dalam jarak yang diizinkan
   - Jika memang tidak bisa hadir, pilih status "Izin" atau "Sakit"

### 3. Lihat Riwayat Presensi

1. Klik "**Presensi**" > "**Riwayat Presensi**"
2. Tabel menampilkan:
   - Tanggal, Jam Masuk/Keluar, Status
   - **Lokasi GPS:** Koordinat saat presensi
   - **Tombol Peta:** Buka lokasi di Google Maps

3. Verifikasi lokasi:
   - Klik "**Lihat Peta**" untuk membuka di Google Maps
   - Verifikasi lokasi presensi Anda

---

## 🔧 Teknis & Troubleshooting

### 1. Cara Kerja Sistem

**Alur Validasi Presensi:**

```
Siswa Presensi (Status = Hadir)
        ↓
Sistem Ambil GPS Lokasi Siswa
        ↓
Cek Setting: Validasi Jarak Aktif?
        ↓
  YA          TIDAK
  ↓            ↓
Hitung     Terima
Jarak      Presensi
  ↓
Dalam Jarak?
  ↓         ↓
YA        TIDAK
↓          ↓
Terima   Tolak +
Presensi  Error Msg
```

**Formula Jarak (Haversine):**

```
R = 6,371,000 meter (Radius Bumi)
a = sin²(Δφ/2) + cos φ1 ⋅ cos φ2 ⋅ sin²(Δλ/2)
c = 2 ⋅ atan2( √a, √(1−a) )
d = R ⋅ c
```

Dimana:
- φ = latitude
- λ = longitude

### 2. Troubleshooting

#### ❌ GPS Tidak Terdeteksi

**Penyebab:**
- Browser tidak memiliki izin lokasi
- GPS perangkat belum diaktifkan
- Koneksi internet tidak stabil
- Browser tidak support Geolocation API

**Solusi:**
1. Cek izin browser: Settings > Privacy > Location > Allow
2. Aktifkan GPS di perangkat: Settings > Location > ON
3. Gunakan browser modern: Chrome, Firefox, Safari, Edge
4. Cek koneksi internet
5. Klik tombol "Deteksi GPS Ulang"

#### ❌ "Lokasi Terlalu Jauh"

**Penyebab:**
- Siswa benar-benar di luar jarak yang diizinkan
- Koordinat GPS salah atau tidak akurat
- Lokasi magang di profil tidak tepat

**Solusi:**
1. Pindah ke lokasi magang
2. Tunggu sampai dalam radius yang diizinkan
3. Verifikasi lokasi magang di profil (harus akurat)
4. Jika tidak bisa hadir: pilih status "Izin" atau "Sakit"

#### ❌ "Lokasi Magang Belum Diatur"

**Penyebab:**
- Siswa tidak punya koordinat GPS di profil

**Solusi:**
1. Buka menu "Profile"
2. Edit dan isi kolom "Lokasi Magang" dengan:
   - Koordinat GPS (latitude, longitude)
   - Atau link Google Maps lokasi magang
3. Simpan profil
4. Coba presensi lagi

#### ✅ GPS Akurat Tapi Tetap Ditolak

**Penyebab:**
- Toleransi jarak terlalu kecil
- Akurasi GPS terbatas (±10 meter)

**Solusi Admin:**
- Naikkan jarak maksimal di pengaturan
- Dari 500m menjadi 600m atau 700m

### 3. Akurasi GPS

**Faktor yang Mempengaruhi Akurasi:**

| Faktor | Akurasi |
|--------|---------|
| GPS di outdoor (terbuka) | ±5-10 meter |
| GPS di sekitar gedung | ±10-20 meter |
| GPS di dalam gedung | ±20-50 meter |
| GPS dengan assisted GPS | ±5 meter |

**Tips Meningkatkan Akurasi:**
- Presensi di outdoor atau dekat jendela
- Tunggu 30 detik untuk GPS stabil
- Gunakan smartphone dengan GPS baru
- Jangan di bawah bangunan atau pohon rindang

### 4. Konversi Jarak

```
1 km = 1,000 m
500 m = 0.5 km
100 m = 0.1 km
```

**Contoh Pengaturan:**
- 300 meter → Kantor kecil
- 500 meter → Kantor sedang
- 1 km → Pabrik
- 2 km → Multi-lokasi

---

## 📊 Laporan & Monitoring

### 1. Data yang Tercatat

Setiap presensi mencatat:
- ✅ Tanggal & Waktu
- ✅ Status (Hadir/Izin/Sakit)
- ✅ Latitude masuk
- ✅ Longitude masuk
- ✅ Latitude keluar (jika ada)
- ✅ Longitude keluar (jika ada)
- ✅ Bukti foto

### 2. Audit & Verifikasi

Admin dapat:
1. Lihat laporan presensi dengan data lokasi
2. Buka lokasi di Google Maps untuk verifikasi
3. Cek jarak actual vs maksimal
4. Identifikasi presensi anomali

---

## 🔒 Keamanan & Privacy

### Data Privacy
- Lokasi siswa hanya disimpan saat presensi
- Data lokasi protected dengan authentication
- Hanya admin dan siswa yang bersangkutan bisa lihat
- Koordinat tidak dibagikan ke pihak ketiga

### Compliance
- Sesuai dengan kebijakan privasi sekolah
- Pastikan memberitahu siswa tentang tracking lokasi
- Minimalkan penyimpanan data lokasi

---

## 📝 Catatan Penting

- ⚠️ GPS hanya berfungsi di perangkat dengan GPS hardware
- ⚠️ Akurasi GPS terbatas, toleransi jarak harus realistis
- ⚠️ Sistem tergantung pada izin browser untuk lokasi
- ⚠️ Presensi dengan status izin/sakit tidak perlu validasi jarak
- ⚠️ Admin harus mengatur jarak secara realistis

---

## 📞 Support & Bantuan

Jika mengalami masalah:
1. Baca section **Troubleshooting** di atas
2. Hubungi admin sekolah
3. Berikan informasi:
   - Screenshot error/warning
   - Jenis perangkat & browser
   - Lokasi saat presensi
   - Waktu kejadian

---

**Dokumen ini terakhir diperbarui: 20 Mei 2026**
**Versi: 1.0**
