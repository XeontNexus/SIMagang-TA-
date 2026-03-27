<?php
// Add guru_pembimbing table and update users table
require_once 'config/config.php';

try {
    $db = new Database();
    
    // Check if guru_pembimbing table exists
    $db->query("SHOW TABLES LIKE 'guru_pembimbing'");
    $tableExists = $db->single();
    
    if (!$tableExists) {
        // Create guru_pembimbing table
        $db->query("CREATE TABLE guru_pembimbing (
            id INT AUTO_INCREMENT PRIMARY KEY,
            nama_guru VARCHAR(100) NOT NULL,
            nip VARCHAR(50),
            no_hp VARCHAR(20),
            email VARCHAR(100),
            status ENUM('active', 'inactive') DEFAULT 'active',
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4");
        $db->execute();
        echo "✅ Tabel 'guru_pembimbing' berhasil dibuat!<br>";
    } else {
        echo "ℹ️ Tabel 'guru_pembimbing' sudah ada.<br>";
    }
    
    // Check if guru_pembimbing_id column exists in users table
    $db->query("SHOW COLUMNS FROM users LIKE 'guru_pembimbing_id'");
    $columnExists = $db->single();
    
    if (!$columnExists) {
        $db->query("ALTER TABLE users ADD COLUMN guru_pembimbing_id INT NULL AFTER kelas_id");
        $db->execute();
        echo "✅ Kolom 'guru_pembimbing_id' berhasil ditambahkan ke tabel users!<br>";
    } else {
        echo "ℹ️ Kolom 'guru_pembimbing_id' sudah ada di tabel users.<br>";
    }
    
    echo "<br>Database update selesai! <a href='master_data.php'>Kembali ke Master Data</a>";
    
} catch (Exception $e) {
    echo "❌ Error: " . $e->getMessage();
}
?>
