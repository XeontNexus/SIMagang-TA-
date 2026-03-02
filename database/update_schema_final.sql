-- ============================================
-- UPDATE SCHEMA - Remove tanggal from logbook, add guru_pembimbing to users
-- Jalankan ini di phpMyAdmin
-- ============================================

USE TA_SIMagang;

-- 1. Tambah kolom guru_pembimbing ke tabel users (jika belum ada)
ALTER TABLE users 
ADD COLUMN IF NOT EXISTS guru_pembimbing VARCHAR(100) AFTER institusi;

-- 2. Hapus kolom tanggal_mulai dan tanggal_selesai dari logbook
-- Pertama, buat tabel logbook baru tanpa kolom tanggal
DROP TABLE IF EXISTS logbook_backup;

-- Backup data lama (opsional)
CREATE TABLE logbook_backup AS SELECT * FROM logbook;

-- Hapus dan buat ulang tabel logbook tanpa kolom tanggal
DROP TABLE IF EXISTS logbook;

CREATE TABLE logbook (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    tahun INT NOT NULL,
    bulan INT NOT NULL,
    minggu_ke INT NOT NULL COMMENT 'Minggu ke-1 sampai 5 dalam bulan',
    
    -- Field baru sesuai request
    rencana TEXT COMMENT 'Rencana kegiatan mingguan',
    hasil TEXT COMMENT 'Hasil kegiatan yang dicapai',
    hambatan TEXT COMMENT 'Hambatan/kendala yang dihadapi',
    perbaikan TEXT COMMENT 'Solusi/perbaikan yang diterapkan',
    
    -- Program kegiatan (otomatis dari institusi user)
    program_kegiatan VARCHAR(255) COMMENT 'Nama institusi tempat magang',
    
    -- Evidence file
    evidence_file VARCHAR(255) COMMENT 'Path file evidence yang diupload',
    evidence_type VARCHAR(50) COMMENT 'Tipe file: image, pdf, word',
    
    status ENUM('draft', 'submitted', 'approved', 'rejected') NOT NULL DEFAULT 'draft',
    catatan_admin TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Hapus tabel backup
DROP TABLE IF EXISTS logbook_backup;

-- ============================================
-- SELESAI! Database telah diperbarui.
-- ============================================
