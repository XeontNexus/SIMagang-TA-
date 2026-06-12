# DOKUMEN PENGUJIAN BLACK BOX — FUNCTIONAL TESTING
# SISTEM INFORMASI MAGANG (SIMAGANG)
# SMKN 1 PERHENTIAN RAJA

---

## 1. Informasi Dokumen

| Item | Keterangan |
|------|------------|
| **Nama Aplikasi** | SIMagang (Sistem Informasi Magang) |
| **Metode Pengujian** | Black Box Testing — Functional Testing |
| **Versi Aplikasi** | Laravel 12.x |
| **Platform Uji** | Web Browser (Desktop & Mobile) |
| **Peran Pengguna** | Admin, Siswa |
| **Tanggal Dokumen** | 23 Mei 2026 |

### 1.1 Tujuan Pengujian
Menguji fungsionalitas sistem dari sudut pandang pengguna akhir **tanpa melihat kode sumber**, memastikan setiap fitur berperilaku sesuai kebutuhan bisnis dan spesifikasi fungsional.

### 1.2 Ruang Lingkup
| Modul | Cakupan |
|-------|---------|
| Autentikasi | Login, logout, ubah password |
| Admin — Kelola Siswa | CRUD akun, import Excel, kirim info WA |
| Admin — Master Data | Jurusan, kelas, guru pembimbing, radius presensi |
| Admin — Presensi | Laporan presensi harian, kecocokan lokasi |
| Admin — Logbook | Approval, reject, data logbook siswa |
| Admin — Lokasi | Permintaan ubah koordinat magang |
| Admin — Export | Excel semua siswa, per guru, per kelas, logbook |
| Siswa — Presensi | GMap, check-in/out, zona hijau/kuning/merah |
| Siswa — Logbook | Input, edit, submit, lihat status |
| Siswa — Profil | Edit data, hubungi admin |
| Notifikasi | Lonceng, badge, tandai sudah dibaca |

### 1.3 Lingkungan Pengujian

| Komponen | Spesifikasi |
|----------|-------------|
| URL Aplikasi | `http://127.0.0.1:8000` |
| Database | MySQL — `simagang_laravel` |
| Browser | Chrome / Edge / Firefox (versi terbaru) |
| Mobile | Android / iOS (opsional) |
| Akun Admin Uji | Username: `admin` |
| Akun Siswa Uji | Username: `siswa001` (atau akun hasil import) |

### 1.4 Identifikasi Penguji

| Field | Isi |
|-------|-----|
| Nama Penguji | _________________________________ |
| Tanggal Pengujian | _________________________________ |
| Perangkat & Browser | _________________________________ |
| Status Pengujian | [ ] Berjalan &nbsp; [ ] Selesai |

### 1.5 Kode Status Pengujian

| Kode | Arti |
|------|------|
| **P** | Passed (Lulus) |
| **F** | Failed (Gagal) |
| **B** | Blocked (Terhalang — fitur/data tidak tersedia) |
| **N/T** | Not Tested (Belum diuji) |

### 1.6 Cara Mengisi
1. Jalankan langkah pengujian sesuai urutan pada kolom **Langkah Pengujian**.
2. Bandingkan hasil aktual dengan **Hasil yang Diharapkan**.
3. Isi kolom **Status** dengan P / F / B / N/T.
4. Tulis temuan bug atau catatan pada kolom **Catatan**.

---

## 2. Matriks Kasus Uji (Functional Test Cases)

### MODUL A — AUTENTIKASI & KEAMANAN

| ID | Fitur / Skenario | Precondition | Langkah Pengujian | Hasil yang Diharapkan | Status | Catatan |
|----|------------------|--------------|-------------------|----------------------|--------|---------|
| **FT-AUTH-001** | Login Admin valid | Akun admin aktif | 1. Buka `/login`<br>2. Isi username admin<br>3. Isi password benar<br>4. Klik **Login** | Redirect ke `/admin/dashboard`; menu sidebar admin tampil | | |
| **FT-AUTH-002** | Login Siswa valid | Akun siswa aktif | 1. Buka `/login`<br>2. Isi username siswa<br>3. Isi password benar<br>4. Klik **Login** | Redirect ke `/student/dashboard`; menu sidebar siswa tampil | | |
| **FT-AUTH-003** | Login password salah | Akun terdaftar | 1. Isi username benar<br>2. Isi password salah<br>3. Klik **Login** | Pesan error kredensial; tetap di halaman login | | |
| **FT-AUTH-004** | Login username kosong | — | 1. Kosongkan username<br>2. Isi password<br>3. Klik **Login** | Validasi form; login ditolak | | |
| **FT-AUTH-005** | Logout | User sudah login | 1. Klik **Logout** di sidebar<br>2. Konfirmasi di popup SweetAlert | Redirect ke halaman login; session berakhir | | |
| **FT-AUTH-006** | Akses halaman admin tanpa login | Belum login | 1. Akses langsung `/admin/dashboard` | Redirect ke halaman login | | |
| **FT-AUTH-007** | Siswa akses halaman admin | Login sebagai siswa | 1. Akses `/admin/dashboard` | Akses ditolak (403 / redirect) | | |
| **FT-AUTH-008** | Admin akses halaman siswa | Login sebagai admin | 1. Akses `/student/dashboard` | Akses ditolak (403 / redirect) | | |
| **FT-AUTH-009** | Ubah password valid | User sudah login | 1. Buka **Edit Username & Password**<br>2. Isi password lama, baru, konfirmasi<br>3. Simpan | Password berubah; bisa login dengan password baru | | |
| **FT-AUTH-010** | Ubah password — konfirmasi tidak cocok | User sudah login | 1. Isi password baru berbeda dengan konfirmasi<br>2. Simpan | Validasi error; password tidak berubah | | |

---

### MODUL B — ADMIN: KELOLA AKUN SISWA

| ID | Fitur / Skenario | Precondition | Langkah Pengujian | Hasil yang Diharapkan | Status | Catatan |
|----|------------------|--------------|-------------------|----------------------|--------|---------|
| **FT-ADM-STU-001** | Lihat daftar akun siswa | Login admin | 1. Klik **Kelola Akun Siswa** | Tabel siswa tampil; urut abjad nama | | |
| **FT-ADM-STU-002** | Tambah siswa manual | Login admin | 1. Klik tambah siswa<br>2. Isi nama, username, NISN, no. WA<br>3. Simpan | Siswa tersimpan; status awal sesuai alur | | |
| **FT-ADM-STU-003** | Edit akun siswa | Ada data siswa | 1. Klik edit pada salah satu siswa<br>2. Ubah no. WA<br>3. Simpan | Data terupdate di tabel | | |
| **FT-ADM-STU-004** | Hapus akun siswa | Ada data siswa | 1. Klik hapus<br>2. Konfirmasi | Siswa terhapus dari daftar | | |
| **FT-ADM-STU-005** | Cari siswa | Ada banyak siswa | 1. Buka **Daftar List Siswa**<br>2. Ketik nama di kolom cari<br>3. Filter | Hanya siswa yang cocok yang ditampilkan | | |
| **FT-ADM-STU-006** | Filter siswa per kelas | Ada data kelas | 1. Pilih filter kelas<br>2. Terapkan | Daftar terfilter sesuai kelas | | |
| **FT-ADM-STU-007** | Download template import akun | Login admin | 1. Buka menu Import<br>2. Download template Excel | File `.xlsx` terunduh | | |
| **FT-ADM-STU-008** | Import akun siswa via Excel | File Excel valid | 1. Upload file template terisi<br>2. Klik import | Siswa masuk ke database; pesan sukses | | |
| **FT-ADM-STU-009** | Kirim info akun via WhatsApp | Siswa punya no. WA; WA aktif | 1. Klik kirim info akun ke siswa | Notifikasi WA terkirim (jika `WHATSAPP_ENABLED=true`) | | |
| **FT-ADM-STU-010** | Lihat detail siswa | Ada data siswa | 1. Klik detail/view siswa | Profil & data magang siswa tampil lengkap | | |

---

### MODUL C — ADMIN: KELOLA DATA MASTER

| ID | Fitur / Skenario | Precondition | Langkah Pengujian | Hasil yang Diharapkan | Status | Catatan |
|----|------------------|--------------|-------------------|----------------------|--------|---------|
| **FT-ADM-MST-001** | Lihat halaman kelola data | Login admin | 1. Klik **Kelola Data** | Tampil tab jurusan, kelas, guru, pengaturan radius | | |
| **FT-ADM-MST-002** | Tambah jurusan | Login admin | 1. Tambah jurusan baru<br>2. Simpan | Jurusan muncul di daftar | | |
| **FT-ADM-MST-003** | Tambah kelas | Ada jurusan | 1. Tambah kelas (tingkat + nama)<br>2. Pilih jurusan<br>3. Simpan | Kelas tersimpan | | |
| **FT-ADM-MST-004** | Tambah guru pembimbing | Login admin | 1. Tambah guru (nama, NIP, no. HP)<br>2. Simpan | Guru muncul di daftar; urut abjad | | |
| **FT-ADM-MST-005** | Edit guru pembimbing | Ada data guru | 1. Edit data guru<br>2. Simpan | Perubahan tersimpan | | |
| **FT-ADM-MST-006** | Hapus jurusan/kelas/guru | Data tidak dipakai siswa | 1. Hapus record<br>2. Konfirmasi | Data terhapus atau pesan error jika masih terpakai | | |
| **FT-ADM-MST-007** | Atur radius presensi | Login admin | 1. Ubah radius hijau & kuning<br>2. Simpan | Pengaturan tersimpan; dipakai di presensi siswa | | |

---

### MODUL D — ADMIN: LAPORAN PRESENSI

| ID | Fitur / Skenario | Precondition | Langkah Pengujian | Hasil yang Diharapkan | Status | Catatan |
|----|------------------|--------------|-------------------|----------------------|--------|---------|
| **FT-ADM-PRE-001** | Lihat laporan presensi hari ini | Ada presensi hari ini | 1. Klik **Laporan Presensi** | Tabel presensi hari ini tampil | | |
| **FT-ADM-PRE-002** | Header tabel presensi | Halaman laporan terbuka | 1. Periksa header kolom tabel | Header rata tengah vertikal; kolom teks rata kiri | | |
| **FT-ADM-PRE-003** | Kolom kecocokan lokasi | Siswa sudah presensi hadir | 1. Lihat kolom **Kecocokan** | Persentase % dan jarak (m) tampil | | |
| **FT-ADM-PRE-004** | Link koordinat presensi | Presensi status hadir | 1. Klik koordinat di tabel | Membuka Google Maps di tab baru | | |
| **FT-ADM-PRE-005** | Filter cari siswa | Ada banyak presensi | 1. Ketik nama siswa di filter<br>2. Klik Filter | Hanya siswa yang cocok ditampilkan | | |
| **FT-ADM-PRE-006** | Detail presensi per siswa | Ada riwayat presensi | 1. Buka detail presensi siswa per bulan | Riwayat bulanan tampil | | |
| **FT-ADM-PRE-007** | Urutan data abjad | Banyak data presensi | 1. Periksa urutan nama di tabel | Nama siswa terurut abjad (A–Z) | | |

---

### MODUL E — ADMIN: KELOLA LOGBOOK

| ID | Fitur / Skenario | Precondition | Langkah Pengujian | Hasil yang Diharapkan | Status | Catatan |
|----|------------------|--------------|-------------------|----------------------|--------|---------|
| **FT-ADM-LOG-001** | Tab approval & data siswa (HP) | Buka di layar HP | 1. Buka **Laporan Logbook**<br>2. Periksa tab menu | Tab **Approval Logbook** dan **Data Logbook Siswa** sejajar kiri–kanan | | |
| **FT-ADM-LOG-002** | Lihat logbook menunggu approval | Ada logbook submitted | 1. Buka tab Approval<br>2. Filter status Submitted | Daftar logbook menunggu tampil | | |
| **FT-ADM-LOG-003** | Approve logbook | Ada logbook submitted | 1. Klik approve pada logbook | Status berubah approved; notifikasi ke siswa | | |
| **FT-ADM-LOG-004** | Reject logbook | Ada logbook submitted | 1. Klik reject<br>2. Isi catatan admin<br>3. Konfirmasi | Status rejected; siswa mendapat notifikasi | | |
| **FT-ADM-LOG-005** | Lihat data logbook disetujui | Ada logbook approved | 1. Buka tab **Data Logbook Siswa** | Logbook yang sudah disetujui tampil per siswa | | |
| **FT-ADM-LOG-006** | Filter logbook | Ada banyak data | 1. Filter status / cari nama | Data terfilter sesuai kriteria | | |
| **FT-ADM-LOG-007** | Notifikasi logbook masuk | Siswa submit logbook baru | 1. Login admin<br>2. Periksa icon lonceng | Badge/notifikasi logbook baru muncul | | |

---

### MODUL F — ADMIN: PERMINTAAN LOKASI

| ID | Fitur / Skenario | Precondition | Langkah Pengujian | Hasil yang Diharapkan | Status | Catatan |
|----|------------------|--------------|-------------------|----------------------|--------|---------|
| **FT-ADM-LOC-001** | Lihat permintaan lokasi | Ada permintaan pending | 1. Klik **Permintaan Lokasi** | Daftar permintaan tampil; urut abjad nama | | |
| **FT-ADM-LOC-002** | Approve ubah lokasi | Ada permintaan pending | 1. Klik approve | Koordinat siswa terupdate; siswa dapat notifikasi | | |
| **FT-ADM-LOC-003** | Reject ubah lokasi | Ada permintaan pending | 1. Klik reject<br>2. Isi alasan | Permintaan ditolak; siswa dapat notifikasi | | |
| **FT-ADM-LOC-004** | Badge permintaan pending | Ada permintaan belum diproses | 1. Periksa sidebar / lonceng | Indikator jumlah permintaan pending tampil | | |

---

### MODUL G — ADMIN: EXPORT DATA EXCEL

| ID | Fitur / Skenario | Precondition | Langkah Pengujian | Hasil yang Diharapkan | Status | Catatan |
|----|------------------|--------------|-------------------|----------------------|--------|---------|
| **FT-ADM-EXP-001** | Export semua siswa | Login admin | 1. Buka **Profile** admin<br>2. Klik **Export Semua Siswa** | File `.xlsx` terunduh berisi seluruh data siswa | | |
| **FT-ADM-EXP-002** | Export per guru pembimbing | Ada guru & siswa bimbingan | 1. Pilih guru dari dropdown<br>2. Klik Export | File Excel hanya berisi siswa bimbingan guru tersebut | | |
| **FT-ADM-EXP-003** | Export per kelas | Ada data kelas | 1. Pilih kelas<br>2. Klik Export | File Excel hanya berisi siswa kelas terpilih | | |
| **FT-ADM-EXP-004** | Export laporan logbook | Ada data logbook | 1. Pilih filter (opsional)<br>2. Klik Export Logbook | File Excel berisi data logbook sesuai filter | | |
| **FT-ADM-EXP-005** | Export tanpa data | Filter tidak cocok | 1. Pilih guru tanpa siswa<br>2. Export | Pesan error / tidak ada data; file tidak terunduh | | |

---

### MODUL H — SISWA: PRESENSI

| ID | Fitur / Skenario | Precondition | Langkah Pengujian | Hasil yang Diharapkan | Status | Catatan |
|----|------------------|--------------|-------------------|----------------------|--------|---------|
| **FT-STU-PRE-001** | Isi link Google Maps pertama kali | Siswa login; belum ada GMap | 1. Buka **Presensi**<br>2. Isi link GMap valid<br>3. Simpan | Koordinat tersimpan; tombol presensi aktif | | |
| **FT-STU-PRE-002** | Presensi tanpa GMap | GMap belum diisi | 1. Buka halaman presensi | Tombol Masuk/Keluar nonaktif; ada peringatan isi GMap | | |
| **FT-STU-PRE-003** | Check-in zona hijau | GMap sudah diisi; GPS dekat lokasi | 1. Izinkan akses lokasi<br>2. Klik **Masuk** di zona hijau | Presensi masuk berhasil tercatat | | |
| **FT-STU-PRE-004** | Check-in zona kuning | GPS di zona kuning | 1. Klik **Masuk** | Muncul konfirmasi sebelum lanjut presensi | | |
| **FT-STU-PRE-005** | Check-in zona merah | GPS jauh dari lokasi | 1. Klik **Masuk** | Presensi ditolak; pesan jarak terlalu jauh | | |
| **FT-STU-PRE-006** | Check-out | Sudah presensi masuk hari ini | 1. Klik **Keluar** | Jam keluar tercatat | | |
| **FT-STU-PRE-007** | Ajukan ubah lokasi | GMap sudah pernah diisi | 1. Ganti link GMap baru<br>2. Submit | Permintaan terkirim ke admin; notifikasi di lonceng siswa | | |
| **FT-STU-PRE-008** | Lihat riwayat presensi | Ada riwayat | 1. Buka riwayat presensi | Daftar presensi sebelumnya tampil | | |
| **FT-STU-PRE-009** | Link GMap tidak valid | — | 1. Masukkan URL bukan Google Maps | Pesan error link tidak valid | | |

---

### MODUL I — SISWA: LOGBOOK

| ID | Fitur / Skenario | Precondition | Langkah Pengujian | Hasil yang Diharapkan | Status | Catatan |
|----|------------------|--------------|-------------------|----------------------|--------|---------|
| **FT-STU-LOG-001** | Lihat halaman logbook | Siswa login | 1. Klik **Logbook** | Halaman logbook & embed Excel (jika dikonfigurasi) tampil | | |
| **FT-STU-LOG-002** | Tambah logbook baru | Profil lengkap | 1. Klik tambah logbook<br>2. Isi minggu, bulan, kegiatan<br>3. Simpan | Logbook tersimpan status submitted | | |
| **FT-STU-LOG-003** | Duplikat minggu & bulan | Logbook minggu sama sudah ada | 1. Buat logbook minggu/bulan yang sama | Error duplikat; data tidak tersimpan ganda | | |
| **FT-STU-LOG-004** | Edit logbook draft/reject | Status bukan approved | 1. Edit logbook<br>2. Simpan | Perubahan tersimpan | | |
| **FT-STU-LOG-005** | Edit logbook approved | Status approved | 1. Coba edit logbook disetujui | Edit ditolak; pesan tidak bisa diedit | | |
| **FT-STU-LOG-006** | Hapus logbook | Status bukan approved | 1. Hapus logbook | Logbook terhapus | | |
| **FT-STU-LOG-007** | Notifikasi hasil approval | Admin approve/reject | 1. Periksa lonceng notifikasi siswa | Notifikasi hasil muncul di dropdown lonceng | | |

---

### MODUL J — SISWA: PROFIL & KOMUNIKASI

| ID | Fitur / Skenario | Precondition | Langkah Pengujian | Hasil yang Diharapkan | Status | Catatan |
|----|------------------|--------------|-------------------|----------------------|--------|---------|
| **FT-STU-PRF-001** | Lihat profil siswa | Siswa login | 1. Klik **Profile** | Data pribadi & data magang tampil | | |
| **FT-STU-PRF-002** | Edit profil siswa | Siswa login | 1. Klik Edit<br>2. Ubah no. HP, data magang<br>3. Simpan | Data terupdate; status siswa sinkron (menunggu/proses/aktif) | | |
| **FT-STU-PRF-003** | Auto-fill no. HP guru pembimbing | Ada data guru | 1. Pilih guru pembimbing di form edit | No. HP guru terisi otomatis | | |
| **FT-STU-PRF-004** | Hubungi admin via WhatsApp | Admin sudah isi no. HP | 1. Klik **Hubungi Admin** di sidebar | WhatsApp terbuka dengan template pesan & nomor admin | | |
| **FT-STU-PRF-005** | Hubungi admin — no. HP kosong | Admin belum isi no. HP | 1. Periksa menu Hubungi Admin | Menu nonaktif / pesan admin belum diatur | | |
| **FT-STU-PRF-006** | Import data siswa via Excel | Siswa login | 1. Download template<br>2. Upload data valid | Data profil terupdate dari Excel | | |

---

### MODUL K — NOTIFIKASI

| ID | Fitur / Skenario | Precondition | Langkah Pengujian | Hasil yang Diharapkan | Status | Catatan |
|----|------------------|--------------|-------------------|----------------------|--------|---------|
| **FT-NOTIF-001** | Badge lonceng siswa | Ada notifikasi belum dibaca | 1. Login siswa<br>2. Periksa icon lonceng | Titik merah kecil muncul di pojok lonceng | | |
| **FT-NOTIF-002** | Badge lonceng admin | Ada logbook/lokasi masuk | 1. Login admin<br>2. Periksa icon lonceng | Angka notifikasi muncul di badge | | |
| **FT-NOTIF-003** | Dropdown notifikasi | Ada notifikasi | 1. Klik icon lonceng | Daftar notifikasi terbaru tampil | | |
| **FT-NOTIF-004** | Sudah lihat semua | Ada notifikasi belum dibaca | 1. Buka dropdown lonceng<br>2. Klik **Sudah lihat semua** | Semua notifikasi ditandai dibaca; badge hilang | | |
| **FT-NOTIF-005** | Halaman semua notifikasi | User login | 1. Klik **Lihat semua notifikasi** | Halaman daftar notifikasi lengkap terbuka | | |
| **FT-NOTIF-006** | Tidak ada banner alert siswa | Siswa punya notifikasi | 1. Buka halaman dashboard/presensi | Tidak ada kotak alert di atas konten; hanya di lonceng | | |

---

### MODUL L — NAVIGASI & RESPONSIVITAS

| ID | Fitur / Skenario | Precondition | Langkah Pengujian | Hasil yang Diharapkan | Status | Catatan |
|----|------------------|--------------|-------------------|----------------------|--------|---------|
| **FT-NAV-001** | Menu sidebar admin | Login admin | 1. Periksa semua item sidebar | Dashboard, Kelola Akun, List Siswa, Kelola Data, Permintaan Lokasi, Laporan Presensi, Laporan Logbook, Profile tampil | | |
| **FT-NAV-002** | Menu sidebar siswa | Login siswa | 1. Periksa sidebar | Dashboard, Presensi, Logbook, Profile, Hubungi Admin tampil | | |
| **FT-NAV-003** | Tombol kembali | Bukan halaman dashboard | 1. Buka sub-halaman<br>2. Klik **Kembali** | Kembali ke halaman sebelumnya | | |
| **FT-NAV-004** | Sidebar mobile | Layar < 768px | 1. Buka aplikasi di HP<br>2. Klik ikon hamburger | Sidebar muncul/tersembunyi dengan benar | | |
| **FT-NAV-005** | Versi APK di sidebar | Login apa pun | 1. Lihat bagian bawah sidebar | Teks versi APK tampil di atas tombol logout | | |

---

### MODUL M — VALIDASI & ERROR HANDLING

| ID | Fitur / Skenario | Precondition | Langkah Pengujian | Hasil yang Diharapkan | Status | Catatan |
|----|------------------|--------------|-------------------|----------------------|--------|---------|
| **FT-VAL-001** | Form wajib diisi kosong | Form dengan field required | 1. Kosongkan field wajib<br>2. Submit | Validasi error; form tidak terkirim | | |
| **FT-VAL-002** | Username duplikat | Username sudah ada | 1. Tambah siswa dengan username sama | Error duplikat username | | |
| **FT-VAL-003** | File import format salah | — | 1. Upload file bukan Excel ke import akun | Pesan error format file | | |
| **FT-VAL-004** | Session expired | Login lama tanpa aktivitas | 1. Biarkan idle hingga session habis<br>2. Akses halaman | Redirect ke login | | |
| **FT-VAL-005** | Pesan sukses setelah aksi | Aksi berhasil (simpan/hapus) | 1. Lakukan aksi simpan data | Modal/popup pesan sukses tampil | | |

---

## 3. Ringkasan Hasil Pengujian

| Modul | Total Kasus | Passed (P) | Failed (F) | Blocked (B) | Not Tested (N/T) |
|-------|-------------|------------|------------|-------------|------------------|
| A — Autentikasi | 10 | | | | |
| B — Kelola Siswa | 10 | | | | |
| C — Master Data | 7 | | | | |
| D — Laporan Presensi | 7 | | | | |
| E — Kelola Logbook | 7 | | | | |
| F — Permintaan Lokasi | 4 | | | | |
| G — Export Excel | 5 | | | | |
| H — Presensi Siswa | 9 | | | | |
| I — Logbook Siswa | 7 | | | | |
| J — Profil Siswa | 6 | | | | |
| K — Notifikasi | 6 | | | | |
| L — Navigasi & Responsif | 5 | | | | |
| M — Validasi & Error | 5 | | | | |
| **TOTAL** | **88** | | | | |

### 3.1 Persentase Keberhasilan

```
Tingkat Keberhasilan (%) = (Jumlah Passed / (Total - Blocked - N/T)) × 100
```

| Metrik | Nilai |
|--------|-------|
| Total Kasus Uji | 88 |
| Lulus (Passed) | ________ |
| Gagal (Failed) | ________ |
| Terhalang (Blocked) | ________ |
| Belum Diuji (N/T) | ________ |
| **Persentase Lulus** | ________ % |

---

## 4. Daftar Bug / Temuan

| No | ID Test Case | Deskripsi Bug | Severity | Status Perbaikan |
|----|--------------|---------------|----------|------------------|
| 1 | | | [ ] Critical [ ] Major [ ] Minor | [ ] Open [ ] Fixed [ ] Closed |
| 2 | | | [ ] Critical [ ] Major [ ] Minor | [ ] Open [ ] Fixed [ ] Closed |
| 3 | | | [ ] Critical [ ] Major [ ] Minor | [ ] Open [ ] Fixed [ ] Closed |
| 4 | | | [ ] Critical [ ] Major [ ] Minor | [ ] Open [ ] Fixed [ ] Closed |
| 5 | | | [ ] Critical [ ] Major [ ] Minor | [ ] Open [ ] Fixed [ ] Closed |

**Keterangan Severity:**
- **Critical** — Sistem tidak bisa digunakan / data hilang
- **Major** — Fitur utama gagal, ada workaround
- **Minor** — Masalah tampilan / teks / UX kecil

---

## 5. Kesimpulan & Rekomendasi

### 5.1 Kesimpulan Pengujian
_______________________________________________________________________________

_______________________________________________________________________________

### 5.2 Rekomendasi
_______________________________________________________________________________

_______________________________________________________________________________

### 5.3 Persetujuan

| Pihak | Nama | Tanda Tangan | Tanggal |
|-------|------|--------------|---------|
| Penguji | | | |
| Pembimbing / Reviewer | | | |
| Pengembang | | | |

---

## 6. Lampiran

### 6.1 Data Uji Contoh

| Role | Username | Password | Keterangan |
|------|----------|----------|------------|
| Admin | `admin` | *(isi saat uji)* | Akun administrator |
| Siswa | `siswa001` | *(isi saat uji)* | Akun siswa aktif |
| Siswa | `siswa002` | *(isi saat uji)* | Akun untuk uji duplikat/logbook |

### 6.2 Link Google Maps Valid (untuk uji presensi)
```
https://www.google.com/maps?q=-0.556230,101.419830
```

### 6.3 Referensi Dokumen Terkait
- `DOKUMENTASI_PENGGUNAAN.md` — Panduan penggunaan fitur
- `GUIDE_VALID_GMAP_LINK.md` — Panduan link Google Maps
- `TEMPLATE_BLACKBOX_TESTING.md` — Template kosongan (versi lama)

---

*Dokumen ini merupakan panduan pengujian Black Box Functional Testing untuk aplikasi SIMagang. Isi kolom Status dan Catatan saat melakukan pengujian manual.*
