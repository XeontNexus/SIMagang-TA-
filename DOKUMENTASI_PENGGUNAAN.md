# Dokumentasi Penggunaan Aplikasi SIMagang (Sistem Informasi Magang)

## Daftar Isi
1. [Pendahuluan](#pendahuluan)
2. [Fitur Utama](#fitur-utama)
3. [Panduan Pengguna](#panduan-pengguna)
   - [Login](#login)
   - [Admin](#admin)
   - [Siswa](#siswa)
4. [Fitur Detail](#fitur-detail)
5. [Troubleshooting](#troubleshooting)

---

## Pendahuluan

**SIMagang** adalah Sistem Informasi Magang yang dirancang untuk mengelola seluruh proses magang/PKL (Praktik Kerja Lapangan) di sekolah/institusi pendidikan. Sistem ini memfasilitasi komunikasi dan manajemen data antara Admin, Siswa, Guru Pembimbing, dan Mitra Magang.

### Tujuan Aplikasi:
- Mengelola data siswa yang melakukan magang
- Mengelola absensi/presensi siswa magang
- Mengelola logbook kegiatan harian siswa
- Mengelola surat rekomendasi untuk magang
- Menghasilkan laporan presensi dan logbook
- Manajemen profil pengguna

---

## Fitur Utama

### Untuk Administrator:
- Dashboard untuk melihat ringkasan sistem
- Kelola Akun Siswa
- Kelola Data (Jurusan, Kelas, Guru Pembimbing, Mitra Magang)
- Laporan Presensi
- Laporan Logbook
- Template Surat
- Profile

### Untuk Siswa:
- Dashboard untuk melihat informasi magang
- Presensi (Absensi harian)
- Logbook (Catat kegiatan harian)
- Profile

---

## Panduan Pengguna

### Login

#### Langkah-langkah Login:
1. Buka aplikasi di browser
2. Halaman login akan muncul secara otomatis
3. Masukkan **Email/Username** pada field "Email"
4. Masukkan **Password** pada field "Password"
5. Klik tombol "Login" atau tekan Enter
6. Sistem akan memvalidasi kredensial
7. Jika berhasil, Anda akan diarahkan ke Dashboard sesuai role

#### Catatan:
- Pastikan menginput email/username dan password dengan benar
- Password bersifat case-sensitive
- Jika lupa password, hubungi administrator

---

## Panduan Pengguna

### ADMIN

#### 1. Dashboard Admin
**Lokasi:** Menu "Dashboard" atau default saat login sebagai admin

**Fungsi:**
- Menampilkan ringkasan data sistem
- Menampilkan statistik presensi
- Menampilkan notifikasi penting

**Aksi:**
- Melihat overview sistem
- Mengakses menu navigasi ke halaman lain

---

#### 2. Kelola Akun Siswa
**Lokasi:** Menu "Kelola Akun Siswa" 🚶

**Fungsi:**
- Menampilkan daftar semua akun siswa
- Mengelola data akun siswa
- Menambah/edit/hapus akun siswa
- Menampilkan informasi detail siswa

**Cara Menggunakan:**

**a) Melihat Daftar Siswa:**
- Klik menu "Kelola Akun Siswa"
- Sistem akan menampilkan tabel berisi daftar siswa
- Tabel menampilkan: NIM, Nama, Jurusan, Kelas, Email, dll

**b) Menambah Siswa Baru:**
- Klik tombol "Tambah Siswa" atau "+" (jika ada)
- Isi form dengan data siswa:
  - Nama Lengkap
  - NIM/No. Induk
  - Email
  - Password (minimal 8 karakter)
  - Jurusan
  - Kelas
  - Guru Pembimbing
  - Mitra Magang
- Klik "Simpan" atau "Submit"

**c) Mengedit Data Siswa:**
- Cari siswa di daftar
- Klik tombol "Edit" atau ikon pensil
- Ubah data yang diperlukan
- Klik "Perbarui" atau "Update"

**d) Menghapus Akun Siswa:**
- Cari siswa di daftar
- Klik tombol "Hapus" atau ikon trash
- Konfirmasi penghapusan
- Akun siswa akan dihapus dari sistem

---

#### 3. Kelola Data (Data Master)
**Lokasi:** Menu "Kelola Data" 🗂️

**Fungsi:**
- Mengelola data Jurusan
- Mengelola data Kelas
- Mengelola data Guru Pembimbing
- Mengelola data Mitra Magang

**Cara Menggunakan:**

Sub-menu tersedia untuk masing-masing data:

**a) Kelola Jurusan:**
- Klik "Kelola Data" → "Jurusan"
- Lihat daftar jurusan
- Tambah jurusan baru: Isi form "Nama Jurusan" → Simpan
- Edit/Hapus: Gunakan tombol edit/delete

**b) Kelola Kelas:**
- Klik "Kelola Data" → "Kelas"
- Lihat daftar kelas
- Tambah kelas baru: Isi form "Nama Kelas", "Jurusan" → Simpan
- Edit/Hapus: Gunakan tombol edit/delete

**c) Kelola Guru Pembimbing:**
- Klik "Kelola Data" → "Guru Pembimbing"
- Lihat daftar guru pembimbing
- Tambah guru baru: Isi form "Nama", "Email", "No. HP", "Jurusan" → Simpan
- Edit/Hapus: Gunakan tombol edit/delete

**d) Kelola Mitra Magang:**
- Klik "Kelola Data" → "Mitra Magang"
- Lihat daftar mitra magang
- Tambah mitra baru: Isi form "Nama Mitra", "Alamat", "Kontak" → Simpan
- Edit/Hapus: Gunakan tombol edit/delete

---

#### 4. Laporan Presensi
**Lokasi:** Menu "Laporan Presensi" 📊

**Fungsi:**
- Melihat laporan presensi siswa
- Filter berdasarkan tanggal, siswa, atau kelas
- Export laporan jika tersedia
- Analisis kehadiran siswa

**Cara Menggunakan:**
1. Klik menu "Laporan Presensi"
2. Anda akan melihat laporan presensi dalam bentuk tabel atau grafik
3. **Filter Data (jika tersedia):**
   - Pilih rentang tanggal
   - Pilih siswa/kelas
   - Klik tombol "Filter" atau "Tampilkan"
4. **Export Laporan (jika tersedia):**
   - Klik tombol "Export" atau "Download"
   - Pilih format (PDF/Excel)
   - File akan diunduh
5. **Analisis:**
   - Lihat statistik kehadiran
   - Identifikasi siswa dengan absensi tinggi

---

#### 5. Laporan Logbook
**Lokasi:** Menu "Laporan Logbook" 📖

**Fungsi:**
- Melihat laporan logbook siswa
- Melihat detail kegiatan harian
- Verifikasi/validasi logbook siswa
- Export laporan jika tersedia

**Cara Menggunakan:**
1. Klik menu "Laporan Logbook"
2. Sistem menampilkan daftar logbook yang telah diinput
3. **Melihat Detail Logbook:**
   - Klik pada baris logbook atau tombol "Lihat"
   - Detail kegiatan akan ditampilkan
4. **Verifikasi Logbook (jika ada wewenang):**
   - Klik tombol "Verifikasi" atau "Setujui"
   - Logbook akan divalidasi
5. **Filter/Search (jika tersedia):**
   - Gunakan field pencarian
   - Filter berdasarkan siswa, tanggal, atau kelas
6. **Export (jika tersedia):**
   - Klik "Export" → Pilih format → Download

---

#### 6. Template Surat
**Lokasi:** Menu "Template Surat" 📄

**Fungsi:**
- Mengelola template surat rekomendasi
- Template yang digunakan untuk generate surat siswa
- Membuat/edit/hapus template

**Cara Menggunakan:**
1. Klik menu "Template Surat"
2. Lihat daftar template surat yang tersedia
3. **Tambah Template Baru:**
   - Klik "Tambah Template" atau "+"
   - Isi form:
     - Nama Template
     - Konten Surat (gunakan editor untuk format)
     - Placeholder khusus untuk data dinamis (misal: {{nama_siswa}}, {{mitra_magang}})
   - Klik "Simpan"
4. **Edit Template:**
   - Klik tombol "Edit"
   - Ubah konten
   - Klik "Perbarui"
5. **Hapus Template:**
   - Klik "Hapus"
   - Konfirmasi penghapusan

---

#### 7. Profile
**Lokasi:** Menu "Profile" 👤

**Fungsi:**
- Melihat & mengedit profil admin
- Mengubah password
- Update informasi pribadi

**Cara Menggunakan:**
1. Klik menu "Profile"
2. Lihat informasi profil saat ini
3. **Edit Profil:**
   - Klik tombol "Edit Profil"
   - Ubah data (nama, email, no. HP, dll)
   - Klik "Simpan"
4. **Ubah Password:**
   - Klik "Ubah Password"
   - Masukkan password lama
   - Masukkan password baru (2x untuk konfirmasi)
   - Klik "Ubah"

---

### SISWA

#### 1. Dashboard Siswa
**Lokasi:** Menu "Dashboard" atau default saat login sebagai siswa

**Fungsi:**
- Menampilkan informasi magang siswa
- Menampilkan ringkasan data
- Akses cepat ke fitur utama

**Aksi:**
- Melihat status magang
- Melihat jadwal presensi (jika ada)
- Mengakses menu navigasi

---

#### 2. Presensi (Absensi Harian)
**Lokasi:** Menu "Presensi" ✅

**Fungsi:**
- Input absensi/presensi harian
- Melihat riwayat presensi
- Konfirmasi kehadiran

**Cara Menggunakan:**

**a) Input Presensi Baru:**
1. Klik menu "Presensi"
2. Sistem akan menampilkan tombol "Absen" atau form presensi
3. **Jika ada tombol "Absen":**
   - Klik "Absen" untuk mengabsen hari ini
   - Pilih status: Hadir/Izin/Sakit/Alpa
   - Jika Izin/Sakit: upload dokumen pendukung (surat, foto, dll)
   - Klik "Konfirmasi" atau "Kirim"
4. **Jika ada form manual:**
   - Isi tanggal: pilih atau sistem otomatis hari ini
   - Isi status kehadiran
   - Klik "Simpan"

**b) Melihat Riwayat Presensi:**
- Lihat tabel riwayat presensi
- Gunakan filter tanggal jika tersedia
- Kolom menampilkan: Tanggal, Status, Keterangan, dll

---

#### 3. Logbook (Catat Kegiatan Harian)
**Lokasi:** Menu "Logbook" 📖

**Fungsi:**
- Input laporan kegiatan harian
- Melihat riwayat logbook
- Download laporan logbook

**Cara Menggunakan:**

**a) Membuat Logbook Baru:**
1. Klik menu "Logbook"
2. Klik tombol "Tambah Logbook" atau "+"
3. Isi form:
   - **Tanggal:** Pilih tanggal kegiatan (default hari ini)
   - **Kegiatan:** Deskripsikan kegiatan yang dilakukan
   - **Jam Mulai:** Jam mulai kegiatan
   - **Jam Selesai:** Jam selesai kegiatan
   - **Catatan:** Catatan tambahan (optional)
4. Klik tombol "Simpan"

**b) Melihat Riwayat Logbook:**
- Lihat tabel riwayat logbook
- Tabel menampilkan: Tanggal, Kegiatan, Durasi, Status
- Klik "Lihat Detail" untuk melihat deskripsi lengkap

**c) Edit/Hapus Logbook:**
- Cari logbook di daftar (jika belum diverifikasi)
- Klik "Edit" untuk mengubah
- Klik "Hapus" untuk menghapus

**d) Download Laporan:**
- Jika tersedia tombol "Download" atau "Export"
- Klik untuk mengunduh logbook dalam format PDF

---

#### 4. Profile Siswa
**Lokasi:** Menu "Profile" 👤

**Fungsi:**
- Melihat & mengedit profil siswa
- Mengubah password
- Update informasi pribadi

**Cara Menggunakan:**
1. Klik menu "Profile"
2. **Lihat Informasi Profil:**
   - Nama lengkap, NIM, Email, No. HP, Jurusan, Kelas, dll
3. **Edit Profil:**
   - Klik tombol "Edit Profil"
   - Ubah data yang diperlukan (nama, email, no. HP, alamat, dll)
   - Klik "Simpan"
4. **Ubah Password:**
   - Klik "Ubah Password"
   - Masukkan password lama
   - Masukkan password baru (2x untuk konfirmasi)
   - Klik "Ubah" atau "Update"
5. **Upload Foto Profil (jika tersedia):**
   - Klik "Ubah Foto"
   - Pilih foto dari komputer
   - Klik "Upload"

---

## Fitur Detail

### Status Presensi:
- **Hadir (H):** Siswa hadir pada hari tersebut
- **Izin (I):** Siswa memiliki izin resmi
- **Sakit (S):** Siswa sakit dengan bukti surat keterangan
- **Alpa (A):** Siswa tidak hadir tanpa alasan

### Status Logbook:
- **Pending/Draft:** Belum diverifikasi admin
- **Diverifikasi:** Sudah disetujui admin
- **Revisi:** Perlu diperbaiki

---

## Troubleshooting

### Masalah: Lupa Password
**Solusi:**
1. Hubungi Administrator
2. Admin akan reset password
3. Gunakan password baru yang diberikan

### Masalah: Tidak Bisa Login
**Solusi:**
1. Periksa email/username yang dimasukkan
2. Pastikan password benar (case-sensitive)
3. Bersihkan browser cache (Ctrl+Shift+Del) dan coba lagi
4. Coba browser lain
5. Hubungi Administrator

### Masalah: Laporan Tidak Tampil
**Solusi:**
1. Pastikan filter tanggal sudah benar
2. Pastikan ada data pada periode yang dipilih
3. Refresh halaman (Ctrl+R)
4. Coba format lain (Excel/PDF)

### Masalah: Upload File Gagal
**Solusi:**
1. Periksa ukuran file (harus kurang dari limit yang ditentukan)
2. Gunakan format file yang diizinkan
3. Coba file lain
4. Hubungi Administrator

### Masalah: Error atau Halaman Blank
**Solusi:**
1. Refresh halaman (Ctrl+R atau F5)
2. Logout dan login kembali
3. Bersihkan browser cache
4. Coba browser lain
5. Hubungi Administrator jika masalah berlanjut

---

## Konvensi & Catatan Penting

### Tips Penggunaan:
✅ Selalu logout setelah selesai menggunakan aplikasi
✅ Input data dengan benar dan lengkap
✅ Jangan lupa menyimpan perubahan (klik Simpan)
✅ Gunakan password yang kuat
✅ Backup dokumen penting

### Batasan & Pembatasan:
- Siswa hanya bisa input presensi untuk diri sendiri
- Logbook hanya bisa diedit sebelum diverifikasi
- Template surat hanya bisa dikelola admin

---

## Informasi Teknis

**Platform:** Laravel Web Application
**Browser yang Didukung:** Chrome, Firefox, Safari, Edge (versi terbaru)
**Responsif:** Desktop dan Tablet

---

## Hubungi Administrator

Jika mengalami masalah teknis atau pertanyaan lebih lanjut:

- **Email:** admin@simagang.local
- **No. Telepon:** [Hubungi Admin Sekolah]
- **Jam Operasional:** [Senin-Jumat, 08:00-16:00]

---

**Terakhir Diperbarui:** 2026-05-20
**Versi Dokumentasi:** 1.1

---

**PERUBAHAN:**
- v1.1 (20 Mei 2026): Fitur Permohonan Surat Rekomendasi dihapus dari sistem
- v1.0 (20 Mei 2026): Dokumentasi awal dibuat
