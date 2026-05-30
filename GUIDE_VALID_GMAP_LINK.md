# Guide: Format Link Google Maps yang Valid ✅

**Update:** 24 Mei 2026

---

## 📌 **Link Google Maps yang VALID (Akan Diterima)**

### ✅ Format 1: Query Parameter dengan Koordinat
```
https://www.google.com/maps?q=-6.175392,106.827153
https://maps.google.com/?q=Jakarta&q=-6.175392,106.827153
```
**Cara dapat:** Buka Google Maps → Klik lokasi → Klik pada koordinat di popup

---

### ✅ Format 2: Place dengan @ Symbol (PALING UMUM)
```
https://www.google.com/maps/place/Jakarta/@-6.175392,106.827153,15z
https://www.google.com/maps/place/Kantor+ABC/@-6.2108,106.8456,17z/data=!3m1!4b1
```
**Cara dapat:** Buka Google Maps → Cari lokasi → Klik "Bagikan" → Salin link

---

### ✅ Format 3: LL Parameter
```
https://maps.google.com/?ll=-6.175392,106.827153
https://maps.google.com/?ll=-6.175392,106.827153&z=15
```

---

### ✅ Format 4: Dengan Data Parameter (!3d !4d)
```
https://www.google.com/maps/place/data=!4m6!3m5!1s0x2e69f3e945e34d01:0x5371bf0fdad9a3a!8m2!3d-6.175392!4d106.827153
```

---

### ✅ Format 5: Short URL (akan otomatis di-expand)
```
https://maps.app.goo.gl/xKvLpQWJ7qR1t5Zs8
https://goo.gl/maps/xKvLpQWJ7qR1t5Zs8
```
**Cara dapat:** Di Google Maps, klik "Bagikan" → Salin short link

---

## ❌ **Link yang TIDAK VALID (Akan Ditolak)**

### ❌ Link tanpa koordinat:
```
https://maps.google.com/              ← SALAH (no location)
https://www.google.com/maps           ← SALAH (no location)
https://maps.google.com/search=...    ← SALAH (search only)
```

### ❌ Link dari sumber lain (bukan Google Maps):
```
https://www.openstreetmap.org/...     ← SALAH (bukan Google Maps)
https://www.mapbox.com/...            ← SALAH (bukan Google Maps)
```

### ❌ Hanya koordinat text:
```
-6.175392, 106.827153                 ← SALAH (bukan URL)
```

### ❌ Link Maps yang sudah expired atau broken:
```
https://maps.google.com/?share=abc123 ← SALAH (invalid format)
```

---

## 🎯 **CARA PALING MUDAH: "Bagikan" dari Google Maps**

### **Step-by-Step untuk Desktop:**

1. **Buka Google Maps**
   - https://maps.google.com

2. **Cari Lokasi Magang Anda**
   - Ketik nama perusahaan atau alamat
   - Tekan Enter atau klik hasil pencarian

3. **Klik Tombol "Bagikan"** (Share button)
   - Di pojok kanan bawah, ada icon berbentuk panah keluar
   - Atau tekan **Ctrl+Shift+S** (Windows) / **Cmd+Shift+S** (Mac)

4. **Pilih "Salin Tautan"** (Copy Link)
   - Jangan pilih "Bagikan ke media sosial"
   - Klik **"Salin Tautan"** atau **"Copy link"**

5. **Paste di Form Presensi**
   - Link sudah otomatis ter-copy ke clipboard
   - Paste di field input Google Maps
   - Klik "Simpan"

### **Video Reference:**
Langkah 3-4 di Google Maps:
```
Dashboard Presensi → Isi Link Google Maps → Modal akan terbuka
→ Paste link dari clipboard → Klik "Simpan"
```

---

## 📱 **Cara dari Google Maps Mobile App**

1. **Buka Google Maps App**

2. **Cari & Buka Lokasi**

3. **Swipe ke atas** (iOS) atau **klik detail lokasi** (Android)

4. **Klik "Bagikan"** (Share)

5. **Pilih "Salin Tautan"**

6. **Paste di Form Presensi** di aplikasi/website SIMagang

---

## 🔍 **Troubleshooting: Mengapa Link Ditolak?**

### **Error: "Link tidak valid. Pastikan link berisi koordinat atau lokasi yang jelas"**

**Penyebab & Solusi:**

| Penyebab | Solusi |
|---------|--------|
| Link dari sumber lain (OpenStreetMap, MapBox, dll) | Gunakan Google Maps, bukan peta lain |
| Link tidak punya koordinat (hanya search) | Pastikan sudah klik lokasi spesifik, bukan hasil search |
| Link sudah expired / broken | Coba ambil link baru |
| Copy paste manual dari URL bar tanpa "Bagikan" | Gunakan fitur "Bagikan" → "Salin Tautan" |
| Link terpotong atau ada karakter aneh | Coba salin lagi, pastikan link lengkap |
| Format koordinat salah | Gunakan "Bagikan" official dari Google Maps |

---

## ✅ **Cara Verifikasi Link SEBELUM Disimpan**

Sebelum paste di form, verifikasi link:

1. **Buka link di browser baru** (untuk pastikan link valid)
   - Klik kanan → "Open link in new tab"
   - Atau copy & paste di tab baru

2. **Pastikan Google Maps membuka dengan lokasi yang benar**
   - Pin/marker harus terlihat
   - Lokasi harus sesuai dengan tempat magang Anda

3. **Cek di URL bar**
   - Pastikan ada koordinat dalam URL
   - Contoh: `/place/.../@-6.175392,106.827153`
   - Atau: `?q=-6.175392,106.827153`

Jika ada marker/pin di peta = link valid ✅

---

## 🎓 **Contoh Link VALID yang Akan Diterima:**

### **Indonesia:**
```
https://www.google.com/maps/place/PT+Maju+Jaya/@-6.2108,106.8456,15z
https://maps.app.goo.gl/abc123def456ghi789
```

### **Jakarta Specific:**
```
https://www.google.com/maps/place/Gedung+Seribu+Jakarta/@-6.195,106.822,17z
```

---

## 💡 **Tips:**

- ✅ Selalu gunakan **"Bagikan" → "Salin Tautan"** dari Google Maps
- ✅ Jika ragu, coba link di tab baru untuk verifikasi
- ✅ Short URL (`maps.app.goo.gl/...`) juga diterima
- ✅ Pastikan GPS aktif saat presensi agar sistem bisa verifikasi jarak
- ✅ Jika masih error, hubungi admin

---

## 🆘 **Hubungi Admin Jika:**

- Link sudah dipastikan dari Google Maps tapi masih ditolak
- Error message berbeda atau tidak jelas
- Link tidak bisa dibuka di tab baru

**Admin bisa debug:** Check file `storage/logs/laravel.log` untuk error detail

---

## 📋 **Checklist Sebelum Submit:**

- [ ] Link dari Google Maps (https://maps.google.com atau https://www.google.com/maps)
- [ ] Link berisi lokasi spesifik (bukan search page)
- [ ] Saat buka link, peta menampilkan pin/marker di lokasi
- [ ] URL mengandung koordinat (lat,lon format seperti -6.xxx, 106.xxx)
- [ ] Link tidak terpotong atau ada spasi aneh
- [ ] Menggunakan fitur "Bagikan" → "Salin Tautan"

Jika semua ✅, link akan diterima!
