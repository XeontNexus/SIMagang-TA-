-- ============================================
-- Update Schema Logbook untuk TA_SIMagang
-- Modifikasi struktur logbook sesuai kebutuhan baru
-- ============================================

USE TA_SIMagang;

-- Hapus tabel logbook lama dan buat yang baru dengan struktur baru
DROP TABLE IF EXISTS logbook;

-- Buat tabel logbook dengan struktur baru
CREATE TABLE logbook (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    tahun INT NOT NULL,
    bulan INT NOT NULL,
    minggu_ke INT NOT NULL, -- 1-5 tergantung jumlah minggu dalam bulan
    tanggal_mulai DATE NOT NULL,
    tanggal_selesai DATE NOT NULL,
    
    -- Field baru sesuai request
    rencana TEXT,          -- Rencana kegiatan
    hasil TEXT,            -- Hasil kegiatan
    hambatan TEXT,         -- Hambatan/kendala
    perbaikan TEXT,        -- Perbaikan/solusi
    
    -- Program kegiatan (otomatis dari institusi user)
    program_kegiatan VARCHAR(255),
    
    -- Evidence file
    evidence_file VARCHAR(255), -- Path ke file yang diupload
    evidence_type VARCHAR(50),  -- jenis file: image, pdf, word, dll
    
    status ENUM('draft', 'submitted', 'approved', 'rejected') NOT NULL DEFAULT 'draft',
    catatan_admin TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Tambah komentar untuk dokumentasi
ALTER TABLE logbook COMMENT = 'Tabel logbook mingguan dengan struktur baru: tahun, bulan, minggu_ke, rencana, hasil, hambatan, perbaikan, evidence';

-- SELESAI!
