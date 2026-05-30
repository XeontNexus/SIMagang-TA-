-- SIMagang Database Schema
-- Database: simagang

-- Create Database
CREATE DATABASE IF NOT EXISTS simagang CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE simagang;

-- Users Table (Admin & Student)
CREATE TABLE users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    nama_lengkap VARCHAR(100) NOT NULL,
    email VARCHAR(100) NOT NULL UNIQUE,
    role ENUM('admin', 'student') NOT NULL DEFAULT 'student',
    no_hp VARCHAR(20),
    institusi VARCHAR(100),
    jurusan VARCHAR(50),
    tanggal_mulai DATE,
    tanggal_selesai DATE,
    status ENUM('active', 'inactive', 'completed') NOT NULL DEFAULT 'active',
    foto_profile VARCHAR(255),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Presensi (Attendance) Table
CREATE TABLE presensi (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    tanggal DATE NOT NULL,
    jam_masuk TIME,
    jam_keluar TIME,
    status ENUM('hadir', 'izin', 'sakit', 'alpha') NOT NULL DEFAULT 'hadir',
    keterangan TEXT,
    bukti_foto VARCHAR(255),
    latitude_masuk DECIMAL(10, 8),
    longitude_masuk DECIMAL(11, 8),
    latitude_keluar DECIMAL(10, 8),
    longitude_keluar DECIMAL(11, 8),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    UNIQUE KEY unique_presensi_per_day (user_id, tanggal)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Logbook Mingguan (Weekly Logbook) Table
CREATE TABLE logbook (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    minggu_ke INT NOT NULL,
    tanggal_mulai DATE NOT NULL,
    tanggal_selesai DATE NOT NULL,
    kegiatan TEXT NOT NULL,
    deskripsi TEXT,
    hasil TEXT,
    kendala TEXT,
    solusi TEXT,
    status ENUM('draft', 'submitted', 'approved', 'rejected') NOT NULL DEFAULT 'draft',
    catatan_admin TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Insert Default Admin
INSERT INTO users (username, password, nama_lengkap, email, role, status) VALUES 
('admin', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Administrator', 'admin@simagang.com', 'admin', 'active');
-- Password: password

-- Insert Sample Student
INSERT INTO users (username, password, nama_lengkap, email, role, no_hp, institusi, jurusan, tanggal_mulai, tanggal_selesai, status) VALUES 
('student1', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Budi Santoso', 'budi@email.com', 'student', '081234567890', 'SMK Negeri 1 Jakarta', 'Rekayasa Perangkat Lunak', '2026-01-01', '2026-03-31', 'active');
-- Password: password
