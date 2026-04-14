<?php
// Setup script untuk membuat tabel jadwal_presensi
require_once __DIR__ . '/../config/config.php';

try {
    $db = new Database();
    
    // Buat tabel jadwal_presensi
    $sql = "CREATE TABLE IF NOT EXISTS jadwal_presensi (
        id INT AUTO_INCREMENT PRIMARY KEY,
        user_id INT NOT NULL,
        hari ENUM('Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu', 'Minggu') NOT NULL,
        jam_masuk TIME NOT NULL DEFAULT '08:00:00',
        jam_keluar TIME NOT NULL DEFAULT '16:00:00',
        bulan_mulai DATE NOT NULL,
        bulan_selesai DATE NOT NULL,
        is_active TINYINT(1) NOT NULL DEFAULT 1,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
        FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
        UNIQUE KEY unique_jadwal (user_id, hari, bulan_mulai, bulan_selesai)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4";
    
    $db->query($sql);
    $db->execute();
    
    echo "✅ Tabel jadwal_presensi berhasil dibuat!\n";
    
} catch(Exception $e) {
    echo "❌ Error: " . $e->getMessage() . "\n";
}
?>
