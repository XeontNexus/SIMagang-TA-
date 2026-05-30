-- ============================================
-- UPDATE SCHEMA LOGBOOK - TA_SIMagang
-- Jalankan ini di phpMyAdmin untuk update struktur tabel logbook
-- ============================================

USE TA_SIMagang;

-- Hapus tabel logbook lama dan buat baru dengan struktur baru
DROP TABLE IF EXISTS logbook;

-- Buat tabel logbook dengan struktur baru
CREATE TABLE logbook (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    tahun INT NOT NULL,
    bulan INT NOT NULL,
    minggu_ke INT NOT NULL COMMENT 'Minggu ke-1 sampai 5 dalam bulan',
    tanggal_mulai DATE NOT NULL,
    tanggal_selesai DATE NOT NULL,
    
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

-- ============================================
-- SELESAI! Struktur logbook telah diperbarui.
-- ============================================
