-- ============================================
-- ADD GURU_PEMBIMBING COLUMN TO USERS TABLE
-- Jalankan ini di phpMyAdmin
-- ============================================

USE TA_SIMagang;

-- Tambah kolom guru_pembimbing ke tabel users
ALTER TABLE users 
ADD COLUMN IF NOT EXISTS guru_pembimbing VARCHAR(100) AFTER institusi;

-- ============================================
-- SELESAI! Kolom guru_pembimbing telah ditambahkan.
-- ============================================
