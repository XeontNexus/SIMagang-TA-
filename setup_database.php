<?php
/**
 * Database Setup Script
 * Run this file to create database and tables automatically
 * Access: http://localhost/projek/TA%20SIMagang/setup_database.php
 */

// Database credentials
$host = 'localhost';
$user = 'root';
$pass = '';
$dbname = 'simagang';

try {
    // Connect to MySQL server (without database)
    $pdo = new PDO("mysql:host=$host;charset=utf8mb4", $user, $pass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    echo "<h2>SIMagang Database Setup</h2>";
    echo "<hr>";
    
    // Create database
    $pdo->exec("CREATE DATABASE IF NOT EXISTS $dbname CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci");
    echo "✅ Database '$dbname' created successfully<br><br>";
    
    // Use database
    $pdo->exec("USE $dbname");
    
    // Create users table
    $pdo->exec("CREATE TABLE IF NOT EXISTS users (
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
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4");
    echo "✅ Table 'users' created successfully<br>";
    
    // Create presensi table
    $pdo->exec("CREATE TABLE IF NOT EXISTS presensi (
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
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4");
    echo "✅ Table 'presensi' created successfully<br>";
    
    // Create logbook table
    $pdo->exec("CREATE TABLE IF NOT EXISTS logbook (
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
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4");
    echo "✅ Table 'logbook' created successfully<br><br>";
    
    // Insert default admin
    $adminPassword = password_hash('password', PASSWORD_DEFAULT);
    $stmt = $pdo->prepare("INSERT IGNORE INTO users (username, password, nama_lengkap, email, role, status) 
                           VALUES (:username, :password, :nama, :email, :role, :status)");
    $stmt->execute([
        ':username' => 'admin',
        ':password' => $adminPassword,
        ':nama' => 'Administrator',
        ':email' => 'admin@simagang.com',
        ':role' => 'admin',
        ':status' => 'active'
    ]);
    echo "✅ Default admin account created<br>";
    
    // Insert sample student
    $studentPassword = password_hash('password', PASSWORD_DEFAULT);
    $stmt = $pdo->prepare("INSERT IGNORE INTO users (username, password, nama_lengkap, email, role, no_hp, institusi, jurusan, tanggal_mulai, tanggal_selesai, status) 
                           VALUES (:username, :password, :nama, :email, :role, :hp, :institusi, :jurusan, :mulai, :selesai, :status)");
    $stmt->execute([
        ':username' => 'student1',
        ':password' => $studentPassword,
        ':nama' => 'Budi Santoso',
        ':email' => 'budi@email.com',
        ':role' => 'student',
        ':hp' => '081234567890',
        ':institusi' => 'SMK Negeri 1 Jakarta',
        ':jurusan' => 'Rekayasa Perangkat Lunak',
        ':mulai' => '2026-01-01',
        ':selesai' => '2026-03-31',
        ':status' => 'active'
    ]);
    echo "✅ Sample student account created<br><br>";
    
    echo "<hr>";
    echo "<h3 style='color: green;'>✨ Database setup completed successfully!</h3>";
    echo "<p>You can now access the application:</p>";
    echo "<ul>";
    echo "<li><strong>URL:</strong> <a href='http://localhost/projek/TA%20SIMagang/'>http://localhost/projek/TA%20SIMagang/</a></li>";
    echo "<li><strong>Admin:</strong> admin / password</li>";
    echo "<li><strong>Student:</strong> student1 / password</li>";
    echo "</ul>";
    echo "<p><strong>⚠️ Delete this file (setup_database.php) after setup for security reasons.</strong></p>";
    
} catch(PDOException $e) {
    echo "<h2 style='color: red;'>❌ Error</h2>";
    echo "<p>" . $e->getMessage() . "</p>";
    echo "<hr>";
    echo "<p><strong>Make sure:</strong></p>";
    echo "<ul>";
    echo "<li>XAMPP MySQL is running</li>";
    echo "<li>MySQL credentials are correct (default: root / empty password)</li>";
    echo "</ul>";
}
?>
