-- ============================================
-- ADD RESET TOKEN COLUMNS TO USERS TABLE
-- Jalankan ini di phpMyAdmin untuk fitur lupa password
-- ============================================

USE TA_SIMagang;

-- Tambah kolom untuk reset password
ALTER TABLE users 
ADD COLUMN IF NOT EXISTS reset_token VARCHAR(64) NULL,
ADD COLUMN IF NOT EXISTS reset_expires DATETIME NULL;

-- ============================================
-- SELESAI! Kolom reset_token dan reset_expires telah ditambahkan.
-- ============================================
