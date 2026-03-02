-- ============================================
-- TABEL JURUSAN DAN UPDATE TABEL USERS
-- Jalankan ini di phpMyAdmin untuk fitur jurusan
-- ============================================

USE TA_SIMagang;

-- 1. Buat tabel jurusan
CREATE TABLE IF NOT EXISTS jurusan (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nama_jurusan VARCHAR(100) NOT NULL,
    keterangan TEXT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- 2. Update tabel users - tambah kolom baru
ALTER TABLE users 
ADD COLUMN IF NOT EXISTS jurusan_id INT NULL,
ADD COLUMN IF NOT EXISTS gmap_link VARCHAR(500) NULL,
ADD COLUMN IF NOT EXISTS pendamping_lapangan VARCHAR(100) NULL,
ADD COLUMN IF NOT EXISTS telp_pendamping VARCHAR(20) NULL,
ADD FOREIGN KEY (jurusan_id) REFERENCES jurusan(id) ON DELETE SET NULL;

-- ============================================
-- SELESAI! Tabel jurusan dan kolom baru telah dibuat.
-- ============================================
