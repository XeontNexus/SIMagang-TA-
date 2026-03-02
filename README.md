# SIMagang - Sistem Informasi Magang

SIMagang adalah aplikasi web untuk manajemen siswa magang/PKL dengan fitur presensi harian dan logbook mingguan.

## Fitur Utama

### 2 Role Pengguna
- **Admin**: Mengelola siswa, melihat laporan presensi dan logbook, approve/reject logbook
- **Student (Siswa)**: Presensi harian (masuk/keluar), mengelola logbook mingguan

### Fitur Admin
- Kelola data siswa magang (CRUD)
- Laporan presensi siswa dengan filter bulan dan siswa
- Laporan logbook mingguan dengan filter status
- Approve/Reject logbook siswa
- Detail statistik per siswa

### Fitur Student
- Presensi harian dengan status (Hadir, Izin, Sakit)
- Riwayat presensi 30 hari terakhir
- Logbook mingguan (CRUD)
- Submit logbook untuk review admin
- Statistik presensi bulanan

## Tech Stack

- **Backend**: PHP 8+ (Native)
- **Database**: MySQL/MariaDB
- **Frontend**: 
  - Tailwind CSS
  - Bootstrap 5
  - Font Awesome Icons
- **Build Tool**: Vite

## Instalasi

### 1. Clone/Download Project
```bash
cd C:\xampp\htdocs\projek
# Copy project ke folder TA SIMagang
```

### 2. Setup Database
```bash
# Buka phpMyAdmin (http://localhost/phpmyadmin)
# Buat database baru: simagang
# Import file: database/schema.sql
```
Atau jalankan query dari `database/schema.sql`

### 3. Install Dependencies (Opsional untuk Development)
```bash
cd "TA SIMagang"
npm install
npm run dev
```

### 4. Konfigurasi
Edit file `config/config.php` jika perlu mengubah:
- Database connection settings
- Base URL aplikasi

Default database config:
- Host: localhost
- User: root
- Password: (kosong)
- Database: simagang

### 5. Akses Aplikasi
Buka browser dan akses:
```
http://localhost/projek/TA%20SIMagang/
```

## Akun Demo

| Role | Username | Password |
|------|----------|----------|
| Admin | admin | password |
| Student | student1 | password |

## Struktur Folder

```
TA SIMagang/
├── assets/
│   └── js/
│       ├── app.js          # Entry point Vite
│       └── app.css         # Tailwind + custom styles
├── config/
│   └── config.php          # Database & app config
├── database/
│   └── schema.sql          # Database schema
├── includes/
│   ├── header.php          # Sidebar & header template
│   └── footer.php          # Footer template
├── index.php               # Redirect to login/dashboard
├── login.php               # Login page
├── logout.php              # Logout handler
├── dashboard.php           # Dashboard admin/student
├── presensi.php            # Presensi student
├── logbook.php             # Logbook student
├── students.php            # Kelola siswa (admin)
├── presensi_report.php     # Laporan presensi (admin)
├── logbook_report.php      # Laporan logbook (admin)
├── student_detail.php      # Detail siswa (admin)
├── profile.php             # Profil pengguna
├── package.json            # NPM dependencies
├── tailwind.config.js      # Tailwind config
├── vite.config.js          # Vite config
└── postcss.config.js       # PostCSS config
```

## Screenshots Fitur

### Login Page
Halaman login dengan design modern menggunakan gradient background.

### Dashboard Admin
- Statistik total siswa, siswa aktif, presensi hari ini, logbook pending
- Tabel siswa terbaru

### Dashboard Student
- Statistik presensi dan logbook pribadi
- Status presensi hari ini
- Quick actions untuk presensi dan logbook

### Presensi
- Check-in dengan pilihan status (Hadir, Izin, Sakit)
- Check-out
- Riwayat 30 hari terakhir
- Statistik bulanan

### Logbook
- CRUD logbook mingguan
- Status: Draft → Submitted → Approved/Rejected
- Form lengkap: kegiatan, deskripsi, hasil, kendala, solusi

### Admin - Kelola Siswa
- Tambah/Edit/Hapus siswa
- Set periode magang
- Status: Active, Inactive, Completed

### Admin - Laporan
- Filter by siswa dan bulan (presensi)
- Filter by siswa dan status (logbook)
- Print laporan
- Approve/Reject logbook

## Keamanan

- Password di-hash menggunakan bcrypt
- Session-based authentication
- Role-based access control
- Prepared statements untuk SQL queries (anti SQL injection)
- XSS protection dengan htmlspecialchars

## Pengembangan Selanjutnya

Beberapa fitur yang bisa ditambahkan:
- Upload foto profile
- Geolocation untuk presensi
- Export PDF laporan
- Notifikasi email
- Multi-level admin
- Konfigurasi shift/jam kerja

## Lisensi

Open Source - bebas digunakan untuk keperluan pembelajaran dan pengembangan.

---

Dibuat dengan ❤️ untuk memudahkan manajemen magang.
