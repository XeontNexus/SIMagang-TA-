-- Fix missing columns in existing database
-- Run this in phpMyAdmin if you get "Unknown column 'status'" error

USE simagang;

-- Add status column to users table if not exists
ALTER TABLE users 
ADD COLUMN IF NOT EXISTS status ENUM('active', 'inactive', 'completed') NOT NULL DEFAULT 'active' AFTER tanggal_selesai;

-- Add foto_profile column if not exists
ALTER TABLE users 
ADD COLUMN IF NOT EXISTS foto_profile VARCHAR(255) AFTER status;

-- Set default status for existing users
UPDATE users SET status = 'active' WHERE status IS NULL;
