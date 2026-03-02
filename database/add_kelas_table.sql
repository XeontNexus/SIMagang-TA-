-- ============================================
-- TABEL KELAS DAN UPDATE TABEL USERS
-- Jalankan ini di phpMyAdmin untuk fitur kelas
-- ============================================

USE TA_SIMagang;

-- 1. Buat tabel kelas
CREATE TABLE IF NOT EXISTS kelas (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nama_kelas VARCHAR(100) NOT NULL,
    keterangan TEXT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- 2. Tambah kolom kelas_id dan update kolom guru_pembimbing di tabel users
ALTER TABLE users 
ADD COLUMN IF NOT EXISTS kelas_id INT NULL,
ADD COLUMN IF NOT EXISTS guru_pembimbing VARCHAR(100) NULL,
ADD FOREIGN KEY (kelas_id) REFERENCES kelas(id) ON DELETE SET NULL;

-- 3. Insert data kelas contoh (opsional)
-- INSERT INTO kelas (nama_kelas, keterangan) VALUES 
-- ('XII RPL 1', 'Kelas Rekayasa Perangkat Lunak 1'),
-- ('XII RPL 2', 'Kelas Rekayasa Perangkat Lunak 2'),
-- ('XII TKJ 1', 'Kelas Teknik Komputer dan Jaringan 1');

-- ============================================
-- SELESAI! Tabel kelas telah dibuat.
-- ============================================
