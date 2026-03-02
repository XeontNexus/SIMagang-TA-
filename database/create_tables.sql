-- ============================================
-- CEK DAN BUAT TABEL KELAS & JURUSAN
-- Jalankan ini di phpMyAdmin
-- ============================================

USE TA_SIMagang;

-- Buat tabel kelas kalau belum ada
CREATE TABLE IF NOT EXISTS kelas (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nama_kelas VARCHAR(100) NOT NULL,
    keterangan TEXT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Buat tabel jurusan kalau belum ada
CREATE TABLE IF NOT EXISTS jurusan (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nama_jurusan VARCHAR(100) NOT NULL,
    keterangan TEXT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Insert data contoh (opsional)
INSERT INTO kelas (nama_kelas, keterangan) VALUES 
('XII RPL 1', 'Rekayasa Perangkat Lunak 1'),
('XII RPL 2', 'Rekayasa Perangkat Lunak 2');

INSERT INTO jurusan (nama_jurusan, keterangan) VALUES 
('Rekayasa Perangkat Lunak', 'RPL'),
('Teknik Komputer Jaringan', 'TKJ'),
('Multimedia', 'MM');
