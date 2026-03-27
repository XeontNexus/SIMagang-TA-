<?php
// Add alamat_sekolah column to users table for admin profiles
require_once 'config/config.php';

try {
    $db = new Database();
    
    // Check if column exists
    $db->query("SHOW COLUMNS FROM users LIKE 'alamat_sekolah'");
    $columnExists = $db->single();
    
    if (!$columnExists) {
        $db->query("ALTER TABLE users ADD COLUMN alamat_sekolah TEXT NULL AFTER alamat_magang");
        $db->execute();
        echo "✅ Kolom 'alamat_sekolah' berhasil ditambahkan ke tabel users!<br>";
    } else {
        echo "ℹ️ Kolom 'alamat_sekolah' sudah ada di tabel users.<br>";
    }
    
    echo "<br>Database update selesai! <a href='profile.php'>Kembali ke Profile</a>";
    
} catch (Exception $e) {
    echo "❌ Error: " . $e->getMessage();
}
?>
