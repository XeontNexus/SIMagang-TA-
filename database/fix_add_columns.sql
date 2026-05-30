-- ============================================
-- FIX: Tambah semua kolom yang diperlukan
-- Jalankan ini di phpMyAdmin untuk memperbaiki error
-- ============================================

USE TA_SIMagang;

-- 1. Buat tabel kelas (kalau belum ada)
CREATE TABLE IF NOT EXISTS kelas (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nama_kelas VARCHAR(100) NOT NULL,
    keterangan TEXT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- 2. Buat tabel jurusan (kalau belum ada)
CREATE TABLE IF NOT EXISTS jurusan (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nama_jurusan VARCHAR(100) NOT NULL,
    keterangan TEXT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- 3. Tambah kolom-kolom baru ke tabel users
-- Jalankan satu per satu jika ada error

-- Kolom kelas_id
ALTER TABLE users ADD COLUMN IF NOT EXISTS kelas_id INT NULL;

-- Kolom guru_pembimbing  
ALTER TABLE users ADD COLUMN IF NOT EXISTS guru_pembimbing VARCHAR(100) NULL;

-- Kolom jurusan_id
ALTER TABLE users ADD COLUMN IF NOT EXISTS jurusan_id INT NULL;

-- Kolom gmap_link
ALTER TABLE users ADD COLUMN IF NOT EXISTS gmap_link VARCHAR(500) NULL;

-- Kolom pendamping_lapangan
ALTER TABLE users ADD COLUMN IF NOT EXISTS pendamping_lapangan VARCHAR(100) NULL;

-- Kolom telp_pendamping
ALTER TABLE users ADD COLUMN IF NOT EXISTS telp_pendamping VARCHAR(20) NULL;

-- 4. Foreign keys (bisa di-skip kalau error)
-- ALTER TABLE users ADD FOREIGN KEY (kelas_id) REFERENCES kelas(id) ON DELETE SET NULL;
-- ALTER TABLE users ADD FOREIGN KEY (jurusan_id) REFERENCES jurusan(id) ON DELETE SET NULL;

-- ============================================
-- SELESAI! Semua kolom telah ditambahkan.
-- ============================================
