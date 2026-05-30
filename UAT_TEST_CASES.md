# Dokumentasi UAT (User Acceptance Testing) - SIMagang

## Test Cases & Skenario Pengujian

---

## 1. TEST CASE: LOGIN

### TC-LOGIN-001: Login Sebagai Admin
**Precondition:** User belum login, akun admin sudah terdaftar
**Langkah Pengujian:**
1. Buka aplikasi di browser
2. Masukkan email/username admin
3. Masukkan password admin
4. Klik tombol "Login"

**Expected Result:** 
- ✅ Admin berhasil login
- ✅ Redirect ke Dashboard Admin
- ✅ Menu Admin visible di sidebar

**Test Status:** [ ] Lulus / [ ] Gagal
**Catatan:** _________________

---

### TC-LOGIN-002: Login Sebagai Siswa
**Precondition:** User belum login, akun siswa sudah terdaftar
**Langkah Pengujian:**
1. Buka aplikasi
2. Masukkan email/username siswa
3. Masukkan password siswa
4. Klik tombol "Login"

**Expected Result:**
- ✅ Siswa berhasil login
- ✅ Redirect ke Dashboard Siswa
- ✅ Menu Siswa visible di sidebar

**Test Status:** [ ] Lulus / [ ] Gagal
**Catatan:** _________________

---

### TC-LOGIN-003: Login dengan Password Salah
**Precondition:** User belum login
**Langkah Pengujian:**
1. Buka aplikasi
2. Masukkan email/username yang benar
3. Masukkan password yang salah
4. Klik tombol "Login"

**Expected Result:**
- ✅ Tampil pesan error "Password salah" atau "Kredensial tidak valid"
- ✅ User tetap di halaman login
- ✅ Form password tidak ter-clear

**Test Status:** [ ] Lulus / [ ] Gagal
**Catatan:** _________________

---

### TC-LOGIN-004: Logout
**Precondition:** User sudah login
**Langkah Pengujian:**
1. Klik menu Profile
2. Klik tombol "Logout" atau "Keluar"
3. Konfirmasi logout jika ada

**Expected Result:**
- ✅ User berhasil logout
- ✅ Redirect ke halaman login
- ✅ Session user dihapus
- ✅ Tombol back tidak bisa akses halaman yang dilindungi

**Test Status:** [ ] Lulus / [ ] Gagal
**Catatan:** _________________

---

## 2. TEST CASE: ADMIN - KELOLA AKUN SISWA

### TC-SISWA-001: Melihat Daftar Siswa
**Precondition:** Admin sudah login
**Langkah Pengujian:**
1. Klik menu "Kelola Akun Siswa"
2. Lihat daftar siswa yang ditampilkan

**Expected Result:**
- ✅ Halaman daftar siswa terbuka
- ✅ Tabel menampilkan data siswa (NIM, Nama, Email, Jurusan, Kelas)
- ✅ Tombol Add/Edit/Delete visible
- ✅ Pagination berfungsi jika ada > 10 data

**Test Status:** [ ] Lulus / [ ] Gagal
**Catatan:** _________________

---

### TC-SISWA-002: Tambah Siswa Baru
**Precondition:** Admin di halaman Kelola Akun Siswa
**Langkah Pengujian:**
1. Klik tombol "Tambah Siswa" atau "+"
2. Isi form:
   - Nama Lengkap: [Input data valid]
   - NIM: [Input data valid]
   - Email: [Input email valid]
   - Password: [Input password min 8 karakter]
   - Jurusan: [Pilih dari dropdown]
   - Kelas: [Pilih dari dropdown]
   - Guru Pembimbing: [Pilih dari dropdown]
   - Mitra Magang: [Pilih dari dropdown]
3. Klik tombol "Simpan"

**Expected Result:**
- ✅ Form dapat diisi dengan benar
- ✅ Validasi field yang wajib diisi
- ✅ Email harus unik (jika sudah ada, tampil error)
- ✅ Password minimal 8 karakter (atau sesuai rule)
- ✅ Setelah klik Simpan, tampil pesan sukses
- ✅ Data siswa baru muncul di daftar

**Test Status:** [ ] Lulus / [ ] Gagal
**Catatan:** _________________

---

### TC-SISWA-003: Edit Data Siswa
**Precondition:** Admin di halaman daftar siswa, ada data siswa
**Langkah Pengujian:**
1. Cari siswa di daftar
2. Klik tombol "Edit" atau ikon pensil
3. Ubah satu atau lebih field (misal: nama, email, jurusan)
4. Klik tombol "Perbarui" atau "Update"

**Expected Result:**
- ✅ Halaman edit terbuka dengan data terisi
- ✅ Dapat mengubah field yang diinginkan
- ✅ Validasi tetap berjalan
- ✅ Email baru tidak boleh duplikat
- ✅ Setelah update, tampil pesan sukses
- ✅ Perubahan data terlihat di daftar

**Test Status:** [ ] Lulus / [ ] Gagal
**Catatan:** _________________

---

### TC-SISWA-004: Hapus Siswa
**Precondition:** Admin di halaman daftar siswa
**Langkah Pengujian:**
1. Cari siswa di daftar
2. Klik tombol "Hapus" atau ikon trash
3. Pada dialog konfirmasi, klik "Ya" atau "Hapus"

**Expected Result:**
- ✅ Dialog konfirmasi tampil sebelum menghapus
- ✅ Pesan konfirmasi jelas: "Yakin ingin menghapus siswa [nama]?"
- ✅ Jika diklik "Ya", data dihapus dan tampil pesan sukses
- ✅ Data siswa tidak lagi muncul di daftar
- ✅ Akun siswa tidak bisa login lagi

**Test Status:** [ ] Lulus / [ ] Gagal
**Catatan:** _________________

---

## 3. TEST CASE: ADMIN - KELOLA DATA

### TC-DATA-001: Kelola Jurusan - Lihat Daftar
**Precondition:** Admin sudah login
**Langkah Pengujian:**
1. Klik menu "Kelola Data"
2. Klik submenu "Jurusan"

**Expected Result:**
- ✅ Halaman daftar jurusan terbuka
- ✅ Tabel menampilkan jurusan yang ada
- ✅ Tombol Tambah/Edit/Hapus visible

**Test Status:** [ ] Lulus / [ ] Gagal
**Catatan:** _________________

---

### TC-DATA-002: Kelola Jurusan - Tambah Jurusan Baru
**Precondition:** Admin di halaman daftar Jurusan
**Langkah Pengujian:**
1. Klik tombol "Tambah Jurusan" atau "+"
2. Isi field "Nama Jurusan": [Input nama jurusan]
3. Klik "Simpan"

**Expected Result:**
- ✅ Form dapat diisi
- ✅ Nama jurusan tidak boleh kosong
- ✅ Nama jurusan tidak boleh duplikat
- ✅ Data baru muncul di daftar setelah disimpan

**Test Status:** [ ] Lulus / [ ] Gagal
**Catatan:** _________________

---

### TC-DATA-003: Kelola Kelas - Tambah Kelas
**Precondition:** Admin di halaman daftar Kelas
**Langkah Pengujian:**
1. Klik tombol "Tambah Kelas"
2. Isi form:
   - Nama Kelas: [Input nama kelas]
   - Jurusan: [Pilih jurusan dari dropdown]
3. Klik "Simpan"

**Expected Result:**
- ✅ Form dapat diisi dengan benar
- ✅ Nama Kelas tidak boleh kosong
- ✅ Jurusan harus dipilih
- ✅ Data kelas baru tersimpan
- ✅ Kelas muncul di daftar

**Test Status:** [ ] Lulus / [ ] Gagal
**Catatan:** _________________

---

### TC-DATA-004: Kelola Guru Pembimbing - Tambah Guru
**Precondition:** Admin di halaman daftar Guru Pembimbing
**Langkah Pengujian:**
1. Klik tombol "Tambah Guru Pembimbing"
2. Isi form:
   - Nama: [Input nama guru]
   - Email: [Input email guru]
   - No. HP: [Input nomor HP]
   - Jurusan: [Pilih jurusan]
3. Klik "Simpan"

**Expected Result:**
- ✅ Form dapat diisi dengan benar
- ✅ Email harus format valid dan unik
- ✅ No. HP tidak boleh kosong
- ✅ Data guru tersimpan

**Test Status:** [ ] Lulus / [ ] Gagal
**Catatan:** _________________

---

### TC-DATA-005: Kelola Mitra Magang - Tambah Mitra
**Precondition:** Admin di halaman daftar Mitra Magang
**Langkah Pengujian:**
1. Klik tombol "Tambah Mitra Magang"
2. Isi form:
   - Nama Mitra: [Input nama perusahaan]
   - Alamat: [Input alamat lengkap]
   - Kontak: [Input nomor kontak]
3. Klik "Simpan"

**Expected Result:**
- ✅ Form dapat diisi
- ✅ Nama Mitra tidak boleh kosong
- ✅ Data tersimpan dan muncul di daftar

**Test Status:** [ ] Lulus / [ ] Gagal
**Catatan:** _________________

---

## 4. TEST CASE: ADMIN - LAPORAN PRESENSI

### TC-LAPORAN-PRESENSI-001: Melihat Laporan Presensi
**Precondition:** Admin sudah login, ada data presensi siswa
**Langkah Pengujian:**
1. Klik menu "Laporan Presensi"
2. Lihat laporan yang ditampilkan

**Expected Result:**
- ✅ Halaman laporan terbuka
- ✅ Laporan menampilkan data presensi (Tanggal, Siswa, Status, Keterangan)
- ✅ Ada tombol filter jika tersedia
- ✅ Pagination berfungsi untuk data besar

**Test Status:** [ ] Lulus / [ ] Gagal
**Catatan:** _________________

---

### TC-LAPORAN-PRESENSI-002: Filter Laporan Presensi
**Precondition:** Admin di halaman Laporan Presensi
**Langkah Pengujian:**
1. Tentukan filter (tanggal mulai, tanggal akhir, siswa, atau kelas)
2. Klik tombol "Filter" atau "Tampilkan"
3. Lihat hasil laporan

**Expected Result:**
- ✅ Filter dapat diatur
- ✅ Laporan menampilkan data sesuai filter
- ✅ Jika tidak ada data, tampil pesan "Tidak ada data"

**Test Status:** [ ] Lulus / [ ] Gagal
**Catatan:** _________________

---

### TC-LAPORAN-PRESENSI-003: Export Laporan Presensi (Jika Ada)
**Precondition:** Admin di halaman Laporan Presensi
**Langkah Pengujian:**
1. Klik tombol "Export" atau "Download"
2. Pilih format (PDF atau Excel)
3. File akan diunduh

**Expected Result:**
- ✅ Tombol export tersedia
- ✅ File dapat diunduh dengan benar
- ✅ File PDF atau Excel dapat dibuka
- ✅ Data di file sesuai dengan laporan yang ditampilkan

**Test Status:** [ ] Lulus / [ ] Gagal
**Catatan:** _________________

---

## 5. TEST CASE: ADMIN - LAPORAN LOGBOOK

### TC-LAPORAN-LOGBOOK-001: Melihat Laporan Logbook
**Precondition:** Admin sudah login, ada data logbook
**Langkah Pengujian:**
1. Klik menu "Laporan Logbook"
2. Lihat daftar logbook

**Expected Result:**
- ✅ Halaman laporan logbook terbuka
- ✅ Tabel menampilkan logbook (Tanggal, Siswa, Kegiatan, Durasi, Status)
- ✅ Tombol "Lihat Detail" atau preview tersedia

**Test Status:** [ ] Lulus / [ ] Gagal
**Catatan:** _________________

---

### TC-LAPORAN-LOGBOOK-002: Lihat Detail Logbook
**Precondition:** Admin di halaman Laporan Logbook, ada data
**Langkah Pengujian:**
1. Klik "Lihat Detail" pada salah satu logbook
2. Lihat detail kegiatan

**Expected Result:**
- ✅ Detail logbook menampilkan informasi lengkap:
   - Siswa
   - Tanggal
   - Jam mulai dan jam selesai
   - Durasi kegiatan
   - Deskripsi kegiatan
   - Status verifikasi

**Test Status:** [ ] Lulus / [ ] Gagal
**Catatan:** _________________

---

## 6. TEST CASE: ADMIN - TEMPLATE SURAT

### TC-TEMPLATE-001: Melihat Daftar Template Surat
**Precondition:** Admin sudah login
**Langkah Pengujian:**
1. Klik menu "Template Surat"
2. Lihat daftar template

**Expected Result:**
- ✅ Halaman template surat terbuka
- ✅ Tabel menampilkan template (Nama, Tanggal Dibuat, Aksi)
- ✅ Tombol Tambah/Edit/Hapus tersedia

**Test Status:** [ ] Lulus / [ ] Gagal
**Catatan:** _________________

---

### TC-TEMPLATE-002: Tambah Template Surat Baru
**Precondition:** Admin di halaman daftar Template Surat
**Langkah Pengujian:**
1. Klik tombol "Tambah Template"
2. Isi form:
   - Nama Template: [Input nama template]
   - Konten Surat: [Input/paste konten surat]
   - (Gunakan placeholder: {{nama_siswa}}, {{mitra_magang}}, dll)
3. Klik "Simpan"

**Expected Result:**
- ✅ Form dapat diisi
- ✅ Editor untuk konten surat tersedia
- ✅ Nama template tidak boleh kosong
- ✅ Konten tidak boleh kosong
- ✅ Template baru tersimpan dan muncul di daftar

**Test Status:** [ ] Lulus / [ ] Gagal
**Catatan:** _________________

---

### TC-TEMPLATE-003: Edit Template Surat
**Precondition:** Admin di halaman daftar Template, ada data template
**Langkah Pengujian:**
1. Klik tombol "Edit" pada template
2. Ubah nama atau konten template
3. Klik "Perbarui"

**Expected Result:**
- ✅ Form edit terbuka dengan data terisi
- ✅ Dapat mengubah konten
- ✅ Perubahan tersimpan

**Test Status:** [ ] Lulus / [ ] Gagal
**Catatan:** _________________

---

### TC-TEMPLATE-004: Hapus Template Surat
**Precondition:** Admin di halaman daftar Template
**Langkah Pengujian:**
1. Klik tombol "Hapus" pada template
2. Konfirmasi penghapusan

**Expected Result:**
- ✅ Dialog konfirmasi tampil
- ✅ Template dihapus setelah konfirmasi
- ✅ Template tidak lagi muncul di daftar

**Test Status:** [ ] Lulus / [ ] Gagal
**Catatan:** _________________

---

## 7. TEST CASE: SISWA - PRESENSI

### TC-SISWA-PRESENSI-001: Input Presensi Harian
**Precondition:** Siswa sudah login, hari ini belum melakukan presensi
**Langkah Pengujian:**
1. Klik menu "Presensi"
2. Klik tombol "Absen" atau "Input Presensi"
3. Pilih status: Hadir/Izin/Sakit/Alpa
4. Jika Izin/Sakit, upload dokumen (jika diperlukan)
5. Klik "Konfirmasi" atau "Kirim"

**Expected Result:**
- ✅ Halaman presensi terbuka
- ✅ Dapat memilih status kehadiran
- ✅ Jika Izin/Sakit, field untuk upload/keterangan tampil
- ✅ Presensi berhasil disimpan
- ✅ Data presensi muncul di riwayat

**Test Status:** [ ] Lulus / [ ] Gagal
**Catatan:** _________________

---

### TC-SISWA-PRESENSI-002: Melihat Riwayat Presensi
**Precondition:** Siswa sudah login, ada data presensi
**Langkah Pengujian:**
1. Klik menu "Presensi"
2. Lihat tabel riwayat presensi

**Expected Result:**
- ✅ Tabel menampilkan riwayat presensi
- ✅ Kolom: Tanggal, Status, Keterangan
- ✅ Data presensi dapat difilter berdasarkan tanggal (jika ada)

**Test Status:** [ ] Lulus / [ ] Gagal
**Catatan:** _________________

---

## 8. TEST CASE: SISWA - LOGBOOK

### TC-SISWA-LOGBOOK-001: Membuat Logbook Baru
**Precondition:** Siswa sudah login
**Langkah Pengujian:**
1. Klik menu "Logbook"
2. Klik tombol "Tambah Logbook" atau "+"
3. Isi form:
   - Tanggal: [Pilih atau otomatis hari ini]
   - Kegiatan: [Deskripsikan kegiatan dengan detail]
   - Jam Mulai: [Input jam mulai, misal 08:00]
   - Jam Selesai: [Input jam selesai, misal 16:00]
   - Catatan: [Opsional, catatan tambahan]
4. Klik "Simpan"

**Expected Result:**
- ✅ Form dapat diisi dengan benar
- ✅ Tanggal tidak boleh melebihi hari ini
- ✅ Jam selesai harus lebih besar dari jam mulai
- ✅ Deskripsi kegiatan tidak boleh kosong
- ✅ Logbook berhasil disimpan
- ✅ Data muncul di riwayat dengan status "Pending/Draft"

**Test Status:** [ ] Lulus / [ ] Gagal
**Catatan:** _________________

---

### TC-SISWA-LOGBOOK-002: Melihat Riwayat Logbook
**Precondition:** Siswa sudah login, ada data logbook
**Langkah Pengujian:**
1. Klik menu "Logbook"
2. Lihat tabel riwayat logbook
3. Klik "Lihat Detail" pada salah satu logbook

**Expected Result:**
- ✅ Tabel menampilkan riwayat logbook
- ✅ Kolom: Tanggal, Kegiatan, Durasi, Status
- ✅ Detail logbook dapat dilihat
- ✅ Menampilkan informasi lengkap kegiatan

**Test Status:** [ ] Lulus / [ ] Gagal
**Catatan:** _________________

---

### TC-SISWA-LOGBOOK-003: Edit Logbook (Sebelum Diverifikasi)
**Precondition:** Siswa di halaman logbook, ada logbook dengan status "Pending"
**Langkah Pengujian:**
1. Cari logbook yang belum diverifikasi
2. Klik tombol "Edit"
3. Ubah kegiatan atau jam
4. Klik "Perbarui"

**Expected Result:**
- ✅ Hanya logbook yang belum diverifikasi dapat diedit
- ✅ Form edit terbuka dengan data terisi
- ✅ Perubahan tersimpan
- ✅ Logbook yang sudah diverifikasi tidak dapat diedit

**Test Status:** [ ] Lulus / [ ] Gagal
**Catatan:** _________________

---

## 9. TEST CASE: PROFILE

### TC-PROFILE-001: Melihat Profile Pengguna
**Precondition:** User sudah login (admin atau siswa)
**Langkah Pengujian:**
1. Klik menu "Profile"
2. Lihat informasi profile

**Expected Result:**
- ✅ Halaman profile terbuka
- ✅ Menampilkan informasi lengkap pengguna
- ✅ Tombol "Edit Profile" dan "Ubah Password" tersedia

**Test Status:** [ ] Lulus / [ ] Gagal
**Catatan:** _________________

---

### TC-PROFILE-002: Edit Profile
**Precondition:** User di halaman Profile
**Langkah Pengujian:**
1. Klik tombol "Edit Profile"
2. Ubah data (nama, email, no. HP, alamat, dll)
3. Klik "Simpan"

**Expected Result:**
- ✅ Form edit terbuka
- ✅ Dapat mengubah data yang diinginkan
- ✅ Email baru harus unik
- ✅ Perubahan disimpan dengan berhasil
- ✅ Tampil pesan sukses

**Test Status:** [ ] Lulus / [ ] Gagal
**Catatan:** _________________

---

### TC-PROFILE-003: Ubah Password
**Precondition:** User di halaman Profile
**Langkah Pengujian:**
1. Klik tombol "Ubah Password"
2. Isi form:
   - Password Lama: [Input password saat ini]
   - Password Baru: [Input password baru, min 8 karakter]
   - Konfirmasi Password: [Input password baru lagi]
3. Klik "Ubah" atau "Update"

**Expected Result:**
- ✅ Form ubah password terbuka
- ✅ Password lama harus divalidasi
- ✅ Password baru minimal 8 karakter
- ✅ Konfirmasi password harus sama dengan password baru
- ✅ Password berhasil diubah
- ✅ Tampil pesan sukses, user tetap login (atau perlu re-login)

**Test Status:** [ ] Lulus / [ ] Gagal
**Catatan:** _________________

---

## 10. TEST CASE: MENU NAVIGATION

### TC-MENU-001: Menu Admin Sidebar
**Precondition:** Admin sudah login
**Langkah Pengujian:**
1. Lihat menu di sidebar

**Expected Result:**
- ✅ Menu visible di sidebar:
   - Dashboard
   - Kelola Akun Siswa
   - Kelola Data
   - Laporan Presensi
   - Laporan Logbook
   - Template Surat
   - Profile
- ✅ Menu "Rekomendasi Surat" TIDAK TAMPIL (sudah dihapus)
- ✅ Semua menu dapat diklik dan berfungsi

**Test Status:** [ ] Lulus / [ ] Gagal
**Catatan:** _________________

---

### TC-MENU-002: Menu Siswa Sidebar
**Precondition:** Siswa sudah login
**Langkah Pengujian:**
1. Lihat menu di sidebar

**Expected Result:**
- ✅ Menu visible di sidebar:
   - Dashboard
   - Presensi
   - Logbook
   - Profile
- ✅ Semua menu dapat diklik dan berfungsi

**Test Status:** [ ] Lulus / [ ] Gagal
**Catatan:** _________________

---

## 11. TEST CASE: VALIDASI & ERROR HANDLING

### TC-VALIDASI-001: Validasi Email
**Precondition:** User di form yang memerlukan input email
**Langkah Pengujian:**
1. Input email dengan format tidak valid (misal: "abc", "abc@", "abc@com")
2. Coba submit form

**Expected Result:**
- ✅ Tampil error: "Format email tidak valid"
- ✅ Form tidak ter-submit
- ✅ Fokus kembali ke field email

**Test Status:** [ ] Lulus / [ ] Gagal
**Catatan:** _________________

---

### TC-VALIDASI-002: Validasi Password
**Precondition:** User membuat akun atau ubah password
**Langkah Pengujian:**
1. Input password kurang dari 8 karakter
2. Coba submit

**Expected Result:**
- ✅ Tampil error: "Password minimal 8 karakter"
- ✅ Form tidak ter-submit

**Test Status:** [ ] Lulus / [ ] Gagal
**Catatan:** _________________

---

### TC-VALIDASI-003: Field Wajib Diisi
**Precondition:** User di form apapun
**Langkah Pengujian:**
1. Kosongkan field yang wajib diisi
2. Klik tombol submit

**Expected Result:**
- ✅ Tampil error: "Field [nama field] harus diisi"
- ✅ Form tidak ter-submit
- ✅ Fokus pada field yang kosong

**Test Status:** [ ] Lulus / [ ] Gagal
**Catatan:** _________________

---

## 12. TEST CASE: RESPONSIVENESS

### TC-RESPONSIVE-001: Tampilan Desktop
**Precondition:** Browser di resolusi desktop (1920x1080 atau lebih)
**Langkah Pengujian:**
1. Buka aplikasi
2. Navigasi ke berbagai halaman
3. Lihat tampilan layout

**Expected Result:**
- ✅ Semua elemen tampil dengan baik
- ✅ Sidebar terlihat di sebelah kiri
- ✅ Konten dapat dibaca
- ✅ Tombol-tombol accessible

**Test Status:** [ ] Lulus / [ ] Gagal
**Catatan:** _________________

---

### TC-RESPONSIVE-002: Tampilan Tablet
**Precondition:** Browser di resolusi tablet (768x1024)
**Langkah Pengujian:**
1. Buka aplikasi di tablet atau resize browser
2. Navigasi ke berbagai halaman

**Expected Result:**
- ✅ Layout responsif di tablet
- ✅ Sidebar mungkin bersembunyi (hamburger menu muncul)
- ✅ Konten dapat dibaca dan diakses

**Test Status:** [ ] Lulus / [ ] Gagal
**Catatan:** _________________

---

## 13. TEST CASE: PERFORMANCE

### TC-PERFORMANCE-001: Loading Dashboard
**Precondition:** User login
**Langkah Pengujian:**
1. Klik menu Dashboard
2. Ukur waktu loading

**Expected Result:**
- ✅ Dashboard loading dalam waktu < 3 detik

**Test Status:** [ ] Lulus / [ ] Gagal
**Waktu Loading:** _________ detik

---

### TC-PERFORMANCE-002: Loading Daftar dengan Data Besar
**Precondition:** Ada daftar dengan data > 100 item
**Langkah Pengujian:**
1. Buka halaman dengan daftar besar
2. Ukur waktu loading
3. Coba pagination/scroll

**Expected Result:**
- ✅ Loading dalam waktu < 5 detik
- ✅ Pagination/scroll berfungsi smooth
- ✅ Tidak ada lag saat navigasi

**Test Status:** [ ] Lulus / [ ] Gagal
**Catatan:** _________________

---

## KESIMPULAN & CATATAN UMUM

### Total Test Cases: 31 TC

**Test Summary:**
- Test Cases Passed: _____
- Test Cases Failed: _____
- Test Cases Pending: _____

**Critical Issues:** ___________________________________________

**Minor Issues:** ___________________________________________

**Recommendations:** ___________________________________________

**Tester Name:** _________________________
**Date:** _________________________
**Signature:** _________________________

---

**Dokumen ini dapat digunakan untuk melakukan User Acceptance Testing (UAT) aplikasi SIMagang.**

---

*Catatan: Centang ✅ semua test cases yang berhasil, dan catat detail untuk test cases yang gagal.*
