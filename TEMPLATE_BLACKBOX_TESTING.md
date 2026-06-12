# TEMPLATE PENGUJIAN BLACK BOX (KOSONGAN)
# APLIKASI SIMAGANG - SMKN 1 PERHENTIAN RAJA

Dokumen ini digunakan oleh tester/dosen penguji untuk melakukan pengujian fungsionalitas sistem informasi monitoring PKL (SIMagang) menggunakan metode **Black Box Testing**.

---

### **Identifikasi Pengujian**
*   **Nama Tester / Penguji:** _________________________________
*   **Hari / Tanggal:** _________________________________
*   **Perangkat Pengujian (OS & Browser):** _________________________________
*   **Status Pengujian:** [ ] Berjalan / [ ] Selesai

---

### **Instruksi Pengujian:**
1. Lakukan langkah-langkah pengujian sesuai instruksi di setiap baris.
2. Centang **Lulus** jika hasil aktual sesuai dengan hasil yang diharapkan.
3. Centang **Gagal** jika sistem menunjukkan kesalahan/bug.
4. Tulis catatan tambahan jika ditemukan kendala pada kolom **Catatan**.

---

## **Tabel Skenario Pengujian Black Box**

| ID Test Case | Fitur / Skenario | Langkah Pengujian | Hasil yang Diharapkan | Status Pengujian | Catatan |
| :--- | :--- | :--- | :--- | :---: | :--- |
| **1. AUTENTIKASI** | | | | | |
| **TC-LOGIN-001** | Login Sebagai Admin | 1. Masukkan username/email admin.<br>2. Masukkan password admin.<br>3. Klik tombol "Login". | 1. Berhasil masuk ke sistem.<br>2. Diarahkan ke Dashboard Admin.<br>3. Menu Admin tampil di sidebar. | [ ] Lulus<br>[ ] Gagal | |
| **TC-LOGIN-002** | Login Sebagai Siswa | 1. Masukkan username/email siswa.<br>2. Masukkan password siswa.<br>3. Klik tombol "Login". | 1. Berhasil masuk ke sistem.<br>2. Diarahkan ke Dashboard Siswa.<br>3. Menu Siswa tampil di sidebar. | [ ] Lulus<br>[ ] Gagal | |
| **TC-LOGIN-003** | Login dengan Sandi Salah | 1. Masukkan email/username valid.<br>2. Masukkan sandi acak/salah.<br>3. Klik tombol "Login". | 1. Tampil pesan error kredensial.<br>2. Tetap di halaman login. | [ ] Lulus<br>[ ] Gagal | |
| **TC-LOGIN-004** | Logout (Keluar) | 1. Klik menu profil.<br>2. Klik tombol "Logout".<br>3. Konfirmasi keluar. | 1. Keluar dari sistem.<br>2. Diarahkan kembali ke login.<br>3. Tombol 'kembali' browser tidak bisa memicu akses kembali. | [ ] Lulus<br>[ ] Gagal | |
| **2. KELOLA SISWA (ADMIN)** | | | | | |
| **TC-SISWA-001** | Melihat Daftar Siswa | 1. Klik menu "Kelola Akun Siswa" di sidebar admin. | 1. Halaman berisi daftar siswa PKL terbuka.<br>2. Menampilkan kolom NIM, Nama, Kelas, Jurusan. | [ ] Lulus<br>[ ] Gagal | |
| **TC-SISWA-002** | Tambah Siswa Baru | 1. Klik tombol "+ Tambah Siswa".<br>2. Isi Nama Lengkap, Username, Email, dan Password.<br>3. Klik "Simpan". | 1. Data siswa baru tersimpan.<br>2. Tampil pesan sukses.<br>3. Siswa baru muncul di daftar.<br>4. Email berisi info login dikirim ke email siswa. | [ ] Lulus<br>[ ] Gagal | |
| **TC-SISWA-003** | Edit Data Siswa | 1. Klik ikon edit pada salah satu siswa.<br>2. Ubah data (misal nama/kelas).<br>3. Klik "Perbarui". | 1. Form terisi data lama.<br>2. Perubahan tersimpan.<br>3. Data baru ter-update di tabel. | [ ] Lulus<br>[ ] Gagal | |
| **TC-SISWA-004** | Hapus Data Siswa | 1. Klik ikon hapus pada salah satu siswa.<br>2. Konfirmasi hapus di popup. | 1. Muncul dialog konfirmasi.<br>2. Data terhapus dan hilang dari tabel. | [ ] Lulus<br>[ ] Gagal | |
| **3. KELOLA DATA MASTER (ADMIN)** | | | | | |
| **TC-DATA-001** | Lihat Daftar Jurusan | 1. Klik "Kelola Data" -> "Jurusan". | 1. Halaman daftar jurusan terbuka dengan benar. | [ ] Lulus<br>[ ] Gagal | |
| **TC-DATA-002** | Tambah Jurusan Baru | 1. Klik tombol "Tambah Jurusan".<br>2. Isi nama jurusan baru.<br>3. Klik "Simpan". | 1. Jurusan baru tersimpan.<br>2. Muncul di daftar pilihan jurusan. | [ ] Lulus<br>[ ] Gagal | |
| **TC-DATA-003** | Tambah Kelas Baru | 1. Klik "Kelola Data" -> "Kelas".<br>2. Klik tambah kelas.<br>3. Pilih Jurusan, klik "Simpan". | 1. Kelas baru tersimpan di database. | [ ] Lulus<br>[ ] Gagal | |
| **TC-DATA-004** | Tambah Guru Pembimbing | 1. Klik "Kelola Data" -> "Guru Pembimbing".<br>2. Tambah data guru baru.<br>3. Klik "Simpan". | 1. Guru pembimbing baru tersimpan.<br>2. Data kontak dan jurusan guru valid. | [ ] Lulus<br>[ ] Gagal | |
| **4. LAPORAN PRESENSI (ADMIN)** | | | | | |
| **TC-LAP-PRES-001**| Melihat Rekap Presensi | 1. Klik menu "Laporan Presensi". | 1. Menampilkan tabel kehadiran siswa secara keseluruhan. | [ ] Lulus<br>[ ] Gagal | |
| **TC-LAP-PRES-002**| Filter Laporan Presensi | 1. Pilih rentang tanggal/siswa/kelas.<br>2. Klik tombol "Filter". | 1. Menampilkan data kehadiran yang hanya sesuai kriteria filter. | [ ] Lulus<br>[ ] Gagal | |
| **TC-LAP-PRES-003**| Export Rekap Presensi | 1. Klik tombol "Export CSV". | 1. Berhasil mengunduh berkas laporan dalam format CSV (.csv). | [ ] Lulus<br>[ ] Gagal | |
| **5. LAPORAN LOGBOOK (ADMIN)** | | | | | |
| **TC-LAP-LOG-001** | Melihat Daftar Logbook | 1. Klik menu "Laporan Logbook". | 1. Menampilkan semua kiriman jurnal harian siswa PKL. | [ ] Lulus<br>[ ] Gagal | |
| **TC-LAP-LOG-002** | Verifikasi & Detail Logbook| 1. Klik tombol "Detail" pada salah satu logbook.<br>2. Klik "Approve" atau "Reject". | 1. Menampilkan deskripsi detail kegiatan.<br>2. Status logbook berubah sesuai keputusan admin. | [ ] Lulus<br>[ ] Gagal | |
| **6. PRESENSI SISWA (STUDENT)** | | | | | |
| **TC-SIS-PRES-001**| Input Kehadiran GPS | 1. Siswa masuk menu "Presensi".<br>2. Sistem mendeteksi koordinat GPS.<br>3. Klik "Check-in" di radius valid. | 1. Presensi berhasil tersimpan.<br>2. Koordinat latitude & longitude presensi terekam. | [ ] Lulus<br>[ ] Gagal | |
| **TC-SIS-PRES-002**| Melihat Riwayat Presensi | 1. Lihat daftar di bawah menu "Riwayat Presensi" 30 hari terakhir. | 1. Menampilkan tanggal presensi, waktu, dan status (Hadir/Izin/Sakit). | [ ] Lulus<br>[ ] Gagal | |
| **7. LOGBOOK SISWA (STUDENT)** | | | | | |
| **TC-SIS-LOG-001** | Membuat Jurnal Baru | 1. Buka menu "Logbook".<br>2. Klik "+ Tambah Logbook".<br>3. Isi kegiatan & jam, klik "Simpan". | 1. Logbook tersimpan dengan status awal *Pending/Draft*. | [ ] Lulus<br>[ ] Gagal | |
| **TC-SIS-LOG-002** | Melihat Riwayat Logbook | 1. Buka tabel di menu "Logbook". | 1. Menampilkan riwayat logbook mingguan beserta status verifikasinya. | [ ] Lulus<br>[ ] Gagal | |
| **TC-SIS-LOG-003** | Edit Logbook Sebelum Valid| 1. Klik ikon "Edit" pada logbook status *Pending*.<br>2. Lakukan pengubahan, klik simpan. | 1. Perubahan tersimpan.<br>2. Logbook yang sudah disetujui admin tidak bisa diedit. | [ ] Lulus<br>[ ] Gagal | |
| **8. PROFIL & KATA SANDI** | | | | | |
| **TC-PROF-001** | Lihat Profil Pengguna | 1. Klik menu "Profile" di sidebar. | 1. Menampilkan informasi akun (NIM/NIP, Nama, Email, Detail Magang). | [ ] Lulus<br>[ ] Gagal | |
| **TC-PROF-002** | Edit Informasi Profil | 1. Klik "Edit Profile".<br>2. Ubah data diri (NISN, No. HP, Jurusan, Kelas).<br>3. Klik "Simpan". | 1. Informasi profil terbarui secara real-time. | [ ] Lulus<br>[ ] Gagal | |
| **TC-PROF-003** | Mengisi Data Mitra Magang Mandiri | 1. Klik "Edit Profile".<br>2. Isi Nama Perusahaan/Mitra, Alamat Magang, Nama & No. HP Pembimbing Lapangan, serta pilih Guru Pembimbing.<br>3. Klik "Simpan". | 1. Data Mitra Magang dan Pembimbing terupdate dengan benar.<br>2. No. HP Guru Pembimbing terisi otomatis setelah Guru Pembimbing dipilih. | [ ] Lulus<br>[ ] Gagal | |
| **TC-PROF-004** | Mengubah Password | 1. Masukkan password lama.<br>2. Masukkan password baru dan konfirmasi.<br>3. Klik "Ubah Password". | 1. Password berhasil diperbarui.<br>2. Harus login ulang jika password berubah. | [ ] Lulus<br>[ ] Gagal | |
| **9. NAVIGASI SIDEBAR** | | | | | |
| **TC-NAV-001** | Sidebar Menu Admin | 1. Periksa sidebar menu setelah login sebagai Admin. | 1. Sidebar menampilkan link Dashboard, Kelola Akun Siswa, Daftar List Siswa, Kelola Data, Permintaan Lokasi, Laporan Presensi, Laporan Logbook, Profile. | [ ] Lulus<br>[ ] Gagal | |
| **TC-NAV-002** | Sidebar Menu Siswa | 1. Periksa sidebar menu setelah login sebagai Siswa. | 1. Sidebar menampilkan link Dashboard, Presensi, Logbook, Profile, Hubungi Admin. | [ ] Lulus<br>[ ] Gagal | |
| **10. VALIDASI & EROR HANDLING**| | | | | |
| **TC-VAL-001** | Validasi Email Salah | 1. Isi email dengan format salah (misal: "emailtanpa-at"). | 1. Sistem memblokir submit dan memunculkan error format email. | [ ] Lulus<br>[ ] Gagal | |
| **TC-VAL-002** | Validasi Panjang Password | 1. Buat user baru dengan password $< 8$ karakter. | 1. Sistem menolak dan menampilkan pesan minimal 8 karakter. | [ ] Lulus<br>[ ] Gagal | |
| **TC-VAL-003** | Form Kolom Kosong | 1. Kosongkan kolom yang wajib diisi (required), lalu submit. | 1. Form gagal dikirim.<br>2. Kolom kosong disorot warna merah dengan notifikasi wajib diisi. | [ ] Lulus<br>[ ] Gagal | |
| **11. RESPONSIVITAS ELEMEN** | | | | | |
| **TC-RESP-001** | Tampilan Desktop | 1. Akses aplikasi pada browser PC dengan resolusi $\ge 1024px$. | 1. Layout melebar sempurna, sidebar terlihat statis di kiri. | [ ] Lulus<br>[ ] Gagal | |
| **TC-RESP-002** | Tampilan Tablet/HP | 1. Akses aplikasi pada tablet/HP dengan resolusi $< 768px$. | 1. Tampilan responsif, menu sidebar bersembunyi menjadi hamburger menu. | [ ] Lulus<br>[ ] Gagal | |
| **12. UJI PERFORMA HALAMAN** | | | | | |
| **TC-PERF-001** | Waktu Muat Dashboard | 1. Klik menu Dashboard dan catat waktu rendering. | 1. Dashboard selesai memuat semua widget statistik dalam waktu $< 3$ detik. | [ ] Lulus<br>[ ] Gagal | |
| **TC-PERF-002** | Loading Data Besar | 1. Buka laporan dengan jumlah catatan $> 100$ baris. | 1. Halaman termuat tanpa lag dalam waktu $< 5$ detik dengan pagination. | [ ] Lulus<br>[ ] Gagal | |

---

### **Lembar Catatan Pengujian (Evaluasi Akhir)**
*   **Total Kasus Uji:** 35 Skenario
*   **Jumlah Kasus Uji Lulus (Passed):** ________ Kasus
*   **Jumlah Kasus Uji Gagal (Failed):** ________ Kasus
*   **Persentase Keberhasilan:** ________ %

**Catatan Khusus Tester:**
____________________________________________________________________________________________________________________
____________________________________________________________________________________________________________________

**Tanda Tangan Penguji / Dosen:**
<br><br><br>
( ___________________________________ )
