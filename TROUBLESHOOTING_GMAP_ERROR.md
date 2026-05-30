# Troubleshooting: Google Maps Link Error

**Waktu Update:** 24 Mei 2026

---

## 🆘 **Jika Masih Mendapat Error: "Link tidak valid..."**

### **Step 1: Verifikasi Link yang Anda Gunakan**

Cek apakah link Anda memenuhi semua ini:

```
✅ Checklist:
□ Link dari google.com atau maps.google.com
□ Link BUKAN dari search bar (harus click lokasi)
□ Link punya PIN/marker saat dibuka di browser
□ Link punya koordinat dalam URL (lihat di URL bar)
```

**Cara verifikasi:**
1. Copy link Anda
2. Buka tab browser baru
3. Paste link tersebut
4. Pastikan peta terbuka dengan PIN/marker terlihat

---

### **Step 2: Gunakan "Bagikan" dari Google Maps (Cara Paling Tepat)**

**Jangan:** Copy manual dari URL bar  
**Gunakan:** Fitur "Bagikan" di Google Maps

#### **Desktop:**
1. Buka https://maps.google.com
2. **Cari lokasi** (ketik nama perusahaan/alamat)
3. **Klik hasil pencarian** untuk buka detail lokasi
4. **Klik tombol Share** (⬈ icon di pojok kanan bawah)
5. **Klik "Copy link"**
6. **Paste** di form presensi

#### **Mobile App:**
1. Buka Google Maps app
2. Cari & buka lokasi
3. **Scroll ke atas** (iOS) atau **tap detail** (Android)
4. **Tap Share** button
5. **Tap "Copy link"**
6. **Paste** di form

---

### **Step 3: Format Link yang Diterima**

Gunakan **salah satu** format ini:

| Format | Contoh | Status |
|--------|--------|--------|
| **Place dengan @** (PALING UMUM) | `https://www.google.com/maps/place/Jakarta/@-6.175392,106.827153,17z` | ✅ Paling baik |
| Query parameter | `https://www.google.com/maps?q=-6.175392,106.827153` | ✅ Baik |
| Short URL | `https://maps.app.goo.gl/abc123def456` | ✅ Support |
| LL parameter | `https://maps.google.com/?ll=-6.175392,106.827153` | ✅ Baik |

---

### **Step 4: Link yang TIDAK Akan Diterima**

❌ **Jangan gunakan:**
```
https://maps.google.com/                    ← Tidak ada lokasi
https://www.google.com/maps                 ← Search page, bukan place
https://maps.google.com/search=abc          ← Search, bukan lokasi
https://www.openstreetmap.org/...           ← Bukan Google Maps
https://goo.gl/maps/                        ← Incomplete short URL
```

---

## 🔧 **Untuk Admin: Debug Tool**

Jika siswa lapor error, admin bisa gunakan debug tool:

### **Akses Debug Tool:**
1. Login sebagai Admin
2. Sidebar → "Debug Gmap URL"
3. Atau URL: `/admin/debug/gmap-url`

### **Cara Debug:**
1. Minta siswa kasih link yang error
2. Paste link di form test
3. Klik "Test Extract"
4. Tool akan show:
   - ✅ Koordinat berhasil di-extract → link valid
   - ❌ Gagal extract → ada yang salah dengan format link

### **Lihat Log Detail:**
```bash
# Check Laravel logs untuk debug info
tail -f storage/logs/laravel.log | grep "coordinates"
```

---

## 📋 **Contoh-Contoh Link yang Bekerja**

### **✅ Contoh Valid:**

**1. Jakarta, Indonesia**
```
https://www.google.com/maps/place/Jakarta/@-6.195,106.822,17z
```

**2. Bandung dengan nama perusahaan**
```
https://www.google.com/maps/place/PT+Sinar+Jaya/@-6.915,107.608,15z
```

**3. Short URL**
```
https://maps.app.goo.gl/XkLmNoPqRsTuVwXyZ
```

**4. Dengan alamat lengkap**
```
https://www.google.com/maps/place/Jl.+Merdeka+No.+123,+Jakarta/@-6.175,106.827,17z
```

---

## ❌ **Contoh-Contoh Link yang TIDAK Bekerja**

**1. Cuma domain, tanpa lokasi**
```
https://maps.google.com/
```

**2. Search page**
```
https://www.google.com/maps/search/jakarta
```

**3. Dari sumber lain**
```
https://www.openstreetmap.org/...
https://www.mapbox.com/...
```

**4. Incomplete short URL**
```
https://goo.gl/maps/
```

---

## 🚨 **Error Message & Solusi**

### **Error: "Link tidak valid. Pastikan link berisi koordinat..."**

| Penyebab | Solusi |
|---------|--------|
| Link tidak punya koordinat | Gunakan "Bagikan" → "Salin Tautan" dari Google Maps |
| Copy paste dari URL bar | Jangan! Gunakan tombol Share resmi |
| Link dari sumber lain | Pastikan dari google.com/maps saja |
| Link terpotong/tidak lengkap | Copy lagi, pastikan link full |
| Belum click detail lokasi | Click lokasi di maps dulu, baru bagikan |

---

## 📞 **Hubungi Dukungan Jika:**

1. **Link sudah dari Google Maps tapi masih error**
   - Admin bisa test di debug tool
   - Admin bisa check logs

2. **Error message berbeda dari di atas**
   - Screenshot error message
   - Hubungi admin dengan screenshot

3. **Masih ragu tentang format link**
   - Baca: `GUIDE_VALID_GMAP_LINK.md`
   - Atau hubungi admin

---

## 🎯 **Quick Checklist Sebelum Submit**

- [ ] Link dari `google.com/maps` atau `maps.google.com`
- [ ] Sudah click lokasi spesifik (bukan search)
- [ ] Gunakan tombol "Bagikan" → "Salin Tautan"
- [ ] Paste link langsung dari clipboard (jangan manual edit)
- [ ] Test buka link di tab baru → pastikan ada PIN/marker
- [ ] URL punya koordinat atau format yang dikenal

**Jika semua ✅ → Link akan diterima!**

---

## 🔍 **Deep Debug (untuk developer/admin):**

### **Manual test extraction:**

```php
php artisan tinker

> $url = 'https://www.google.com/maps/place/Jakarta/@-6.175392,106.827153,17z'
> \App\Helpers\LocationHelper::extractCoordinatesFromGoogleMapsUrl($url)

Output should be:
=> [
     'latitude' => -6.175392,
     'longitude' => 106.827153
   ]
```

### **Check logs:**
```bash
tail -50 storage/logs/laravel.log
```

Cari line dengan "Attempting to extract" atau "Failed to extract"

---

## 📊 **Supported URL Patterns (Technical)**

| Pattern | Regex | Example |
|---------|-------|---------|
| Query param | `?q=lat,lon` | `?q=-6.17,106.82` |
| Place mark | `/@lat,lon` | `/@-6.17,106.82` |
| LL param | `?ll=lat,lon` | `?ll=-6.17,106.82` |
| Data param | `!3dlat!4dlon` | `!3d-6.17!4d106.82` |
| Alt data | `3d=lat&4d=lon` | `3d=-6.17&4d=106.82` |

**Validasi:**
- Latitude: -90 to +90
- Longitude: -180 to +180
- Koordinat harus valid secara geografis

---

## 💡 **Tips Tambahan**

1. **Jika short URL tidak ter-expand**
   - Server perlu internet connection yang baik
   - Coba gunakan full URL dari "Share" button

2. **Untuk URL dengan special characters**
   - Sistem otomatis decode URL-encoded characters
   - Contoh: `%2C` → `,`

3. **Jika suspicious link ditolak**
   - Validasi koordinat ada
   - Koordinat di range yang valid
   - Format sesuai salah satu pattern

---

## 📚 **Referensi Lengkap**

- User guide: `GUIDE_VALID_GMAP_LINK.md`
- Technical docs: `FITUR_PERBANDINGAN_LOKASI.md`
- Admin tool: `/admin/debug/gmap-url`

---

**Pertanyaan? Hubungi Admin atau Check dokumentasi di atas!**
