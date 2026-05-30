# SIMagang Laravel - Setup Guide

## Konfigurasi .env

Update file `.env` dengan konfigurasi database:

```env
APP_NAME=SIMagang
APP_ENV=local
APP_KEY=base64:your-app-key
APP_DEBUG=true
APP_URL=http://localhost

DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=simagang
DB_USERNAME=root
DB_PASSWORD=

SESSION_DRIVER=file
SESSION_LIFETIME=120
```

## Langkah Setup

1. **Install Dependencies**
   ```bash
   composer install
   ```

2. **Generate App Key**
   ```bash
   php artisan key:generate
   ```

3. **Jalankan Migration**
   ```bash
   php artisan migrate
   ```

4. **Jalankan Seeder (Admin & Guru Pembimbing)**
   ```bash
   php artisan db:seed
   ```

5. **Buat Storage Link (untuk foto presensi)**
   ```bash
   php artisan storage:link
   ```

6. **Jalankan Server**
   ```bash
   php artisan serve
   ```

## Troubleshooting

- **Database belum ada**: Buat database `simagang` di phpMyAdmin dulu
- **Permission denied**: Jalankan `chmod -R 777 storage bootstrap/cache` (Linux/Mac)
- **Foto tidak tampil**: Pastikan `php artisan storage:link` sudah dijalankan

## Struktur Database

- users (admin & student)
- jurusans
- kelas
- guru_pembimbings
- presensis
- logbooks
- jadwal_presensis

## Login Default

- **Admin**: `admin` / `admin123`
- **Student**: Register via form atau buat manual

## URL Akses

- Login: http://localhost:8000/login
- Register: http://localhost:8000/register
- Admin Dashboard: http://localhost:8000/admin/dashboard
- Student Dashboard: http://localhost:8000/student/dashboard
