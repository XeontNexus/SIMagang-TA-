# Panduan: Fix Google Maps Link & Fitur Perbandingan Lokasi Siswa-Mitra

**Update:** 24 Mei 2026

---

## 📋 Ringkasan Perubahan

### 1. ✅ Fix: Google Maps Link Validation

**Masalah Lama:**
- Link Google Maps sering menunjukkan error "Link tidak valid" padahal link sudah dari Google Maps
- Sistem hanya support 3 format URL tertentu
- Banyak format modern Google Maps tidak bisa di-parse

**Solusi:**
- Enhanced `LocationHelper::extractCoordinatesFromGoogleMapsUrl()` untuk support lebih banyak format:
  - ✅ `https://www.google.com/maps?q=lat,lon` (query parameter)
  - ✅ `https://www.google.com/maps/place/.../@lat,lon,...` (place mark)
  - ✅ `https://maps.google.com/?ll=lat,lon` (LL parameter)
  - ✅ `https://www.google.com/maps/place/Nama/@lat,lon/...` (place dengan nama)
  - ✅ `https://maps.app.goo.gl/xxxxx` (short URL - akan di-expand)
  - ✅ Format dengan `!3d` dan `!4d` parameter (embedded maps)

- Regex pattern diperbaiki untuk lebih fleksibel:
  - Dulu: `/[?&]q=(-?\d+\.\d+),(-?\d+\.\d+)/` (strict, require decimal)
  - Sekarang: `/[?&]q=(-?\d+\.?\d*),(-?\d+\.?\d*)/` (flexible, decimal optional)

- Error handling lebih baik dengan try-catch untuk URL expansion

**File yang Dimodifikasi:**
- `app/Helpers/LocationHelper.php`

---

### 2. ✅ New Feature: Model & Database untuk Mitra

**Yang Ditambahkan:**

#### a. Model Mitra (`app/Models/Mitra.php`)
- Menyimpan informasi mitra magang dengan lokasi
- Fields: `nama_mitra`, `alamat`, `kontak`, `gmap_link`, `latitude`, `longitude`
- Relationship: `hasMany(User)` - satu mitra bisa punya banyak siswa

#### b. Migration (`database/migrations/2026_05_24_000002_create_mitras_table.php`)
- Buat tabel `mitras` dengan struktur lengkap
- Add foreign key `mitra_id` ke tabel `users`
- Secure deletion dengan `onDelete('set null')`

#### c. Update User Model
- Add relationship: `belongsTo(Mitra)`
- Sekarang user bisa referenceable ke Mitra dengan ID (bukan hanya string)

**Database Schema:**
```sql
CREATE TABLE mitras (
    id BIGINT PRIMARY KEY,
    nama_mitra VARCHAR(150),
    alamat VARCHAR(255),
    kontak VARCHAR(20),
    gmap_link TEXT,
    latitude DECIMAL(10,8),
    longitude DECIMAL(11,8),
    created_at TIMESTAMP,
    updated_at TIMESTAMP,
    INDEX idx_nama_mitra (nama_mitra)
);

ALTER TABLE users ADD COLUMN mitra_id BIGINT UNSIGNED NULLABLE;
ALTER TABLE users ADD FOREIGN KEY (mitra_id) REFERENCES mitras(id) ON DELETE SET NULL;
```

---

### 3. ✅ New Feature: Admin Menu untuk Perbandingan Lokasi

**Location Comparison Controller** (`app/Http/Controllers/Admin/LocationComparisonController.php`)

Fitur:
- 📊 **List View**: Daftar semua siswa dengan perbandingan lokasi
  - Filter by mitra, status, search
  - Tampilkan distance dan similarity percentage
  - Pagination

- 🔍 **Detail View**: Detail perbandingan untuk satu siswa
  - Info lokasi siswa (lat/lon, gmap link)
  - Info lokasi mitra (lat/lon, gmap link)
  - Hitung jarak & similarity
  - Opsi update lokasi mitra jika belum ada

- 📥 **Export CSV**: Download semua data untuk analisis

**Algoritma Similarity:**
```
Similarity % = MAX(0, 100 - (distance_in_meters / 20))

Contoh:
- 0m      → 100%
- 20m     → 100% (max)
- 100m    → 95%
- 500m    → 75%
- 1000m   → 50%
- 2000m   → 0%
```

**Zone Classification:**
```
Hijau (Green)  = Jarak ≤ 30m   (Lokasi sangat tepat)
Kuning (Yellow)= Jarak 30-70m  (Lokasi dekat)
Merah (Red)    = Jarak > 70m   (Lokasi jauh)
```

---

## 🚀 Cara Menggunakan

### A. Setup Awal

#### 1. Run Migration
```bash
php artisan migrate
```

Ini akan:
- Create tabel `mitras`
- Add `mitra_id` foreign key ke `users`

#### 2. (Opsional) Import Data Mitra dari User Existing
Jika sudah punya data user dengan `mitra_magang` field yang terisi, bisa import ke tabel mitra:

**Cara Manual:**
1. Buka Admin Panel
2. Kelola Data → Master Data
3. Tambah mitra satu per satu (atau wait untuk bulk import feature)

**Cara Command (coming soon):**
```bash
php artisan mitra:import-from-users
```

---

### B. Untuk Admin: Mengelola Lokasi Mitra

#### Step 1: Akses Menu Perbandingan Lokasi
```
Admin Dashboard → Perbandingan Lokasi Siswa & Mitra
atau
URL: /admin/location-comparison
```

#### Step 2: Update Lokasi Mitra (Jika Belum Ada)
Jika ada siswa dengan mitra yang belum punya koordinat:
1. Klik tombol 📊 Detail pada siswa
2. Di bagian "Lokasi Mitra", ada form untuk input link Google Maps mitra
3. Paste link dari Google Maps
4. Klik "Simpan"
5. Sistem otomatis extract koordinat dan simpan

#### Step 3: Lihat Detail Perbandingan
- Jarak antara lokasi siswa & mitra
- Persentase kesamaan lokasi
- Status zone (hijau/kuning/merah)
- Link Google Maps untuk verifikasi lebih lanjut

#### Step 4: Export Data
- Klik 📥 Export CSV
- Download file untuk analisis di spreadsheet
- Bisa filter by mitra sebelum export

---

### C. Untuk Siswa: Input Google Maps Link

**Link Google Maps yang Valid:**

✅ **Format yang Bekerja:**
```
https://www.google.com/maps?q=-6.175392,106.827153
https://www.google.com/maps/place/Nama+Tempat/@-6.175392,106.827153,17z/...
https://maps.app.goo.gl/xxxxxxxxx
https://maps.google.com/?ll=-6.175392,106.827153
```

❌ **Format yang TIDAK Bekerja:**
```
https://maps.google.com        (tanpa lokasi)
https://google.com/maps        (domain salah)
Just coordinate text: -6.175392, 106.827153
```

**Cara Ambil Link:**
1. Buka Google Maps: https://maps.google.com
2. Cari lokasi magang
3. Klik tombol **Share** (icon bagian kanan atas)
4. Klik **Copy link**
5. Paste di form presensi siswa

---

## 📊 Contoh Use Cases

### Use Case 1: Verifikasi Siswa Benar-benar di Lokasi Magang
**Skenario:**
- Siswa claim presensi di lokasi, tapi suspek tidak benar
- Admin ingin check apakah lokasi siswa (dari GPS) sesuai dengan lokasi mitra

**Solusi:**
1. Buka Detail Perbandingan Lokasi siswa
2. Lihat jarak antara lokasi siswa & mitra
3. Jika zone "Merah" = kemungkinan siswa tidak di lokasi sebenarnya
4. Bisa tolak presensi atau minta konfirmasi

### Use Case 2: Bulk Analisis Semua Siswa
**Skenario:**
- Admin ingin lihat overview semua siswa: siapa aja yang lokasi magang-nya jelas, siapa yang belum

**Solusi:**
1. Buka menu Perbandingan Lokasi
2. Lihat tabel list semua siswa
3. Sort by similarity percentage
4. Export CSV untuk analisis lebih lanjut

### Use Case 3: Update Lokasi Mitra
**Skenario:**
- Admin dapat info bahwa lokasi mitra sudah pindah
- Perlu update koordinat mitra baru

**Solusi:**
1. Klik detail salah satu siswa dari mitra tersebut
2. Di bagian "Lokasi Mitra", input link Google Maps lokasi baru
3. Simpan - sistem otomatis update koordinat
4. Semua siswa dari mitra ini otomatis ter-update data perbandingannya

---

## 🔧 Technical Details

### File yang Dimodifikasi/Ditambah:

**Models:**
- ✅ `app/Models/Mitra.php` (NEW)
- ✅ `app/Models/User.php` (UPDATED - add relationship)

**Controllers:**
- ✅ `app/Http/Controllers/Admin/LocationComparisonController.php` (NEW)

**Migrations:**
- ✅ `database/migrations/2026_05_24_000002_create_mitras_table.php` (NEW)

**Views:**
- ✅ `resources/views/admin/location-comparison/index.blade.php` (NEW)
- ✅ `resources/views/admin/location-comparison/show.blade.php` (NEW)

**Helpers:**
- ✅ `app/Helpers/LocationHelper.php` (UPDATED - enhance URL parsing)

**Routes:**
- ✅ `routes/web.php` (UPDATED - add location-comparison routes)

---

## 🧪 Testing Checklist

- [ ] Run migration tanpa error
- [ ] Import test user dengan mitra
- [ ] Test input Google Maps link siswa (presensi)
- [ ] Verifikasi link ter-extract koordinat dengan benar
- [ ] Buka halaman admin Perbandingan Lokasi
- [ ] Filter by mitra berfungsi
- [ ] Click detail siswa, lihat info
- [ ] Update lokasi mitra (dari admin)
- [ ] Verifikasi distance & similarity calculated benar
- [ ] Export CSV berfungsi
- [ ] Test dengan short URL (maps.app.goo.gl)

---

## ⚠️ Known Issues & Limitations

1. **URL Expansion untuk Short URL**: Membutuhkan cURL aktif di server
2. **API Accuracy**: Sistem hanya extract koordinat dari URL, tidak validate apakah lokasi valid di dunia nyata
3. **Map Visualization**: Views saat ini hanya show link, belum embed peta interaktif (bisa ditambah nanti dengan Google Maps embed)

---

## 🚀 Future Enhancements (Roadmap)

- [ ] Embed interactive Google Maps di halaman detail
- [ ] Bulk update mitra location (import from CSV)
- [ ] Alert notification jika siswa keluar dari zona hijau
- [ ] Integration dengan GPS real-time dari presensi siswa
- [ ] Analytics dashboard untuk similarity trends
- [ ] Support untuk koordinat manual input (jika URL parsing gagal)

---

## 📞 Support

Jika ada error atau pertanyaan:
1. Check laravel logs: `storage/logs/laravel.log`
2. Verify migration berjalan: `php artisan migrate:status`
3. Test LocationHelper: 
   ```php
   php artisan tinker
   > \App\Helpers\LocationHelper::extractCoordinatesFromGoogleMapsUrl("https://...")
   ```
