<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= APP_NAME ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <style>
        :root {
            --primary-gradient: linear-gradient(135deg, #1565C0 0%, #0D47A1 100%);
            --sidebar-width: 250px;
            --primary-color: #1565C0;
            --primary-dark: #0D47A1;
            --primary-light: #42A5F5;
        }
        
        body {
            font-family: 'Inter', system-ui, sans-serif;
            background-color: #f8f9fc;
        }
        
        .sidebar {
            position: fixed;
            top: 0;
            left: 0;
            width: var(--sidebar-width);
            height: 100vh;
            background: var(--primary-gradient);
            color: white;
            z-index: 1000;
            transition: all 0.3s;
        }
        
        .sidebar-brand {
            padding: 1.5rem;
            text-align: center;
            border-bottom: 1px solid rgba(255,255,255,0.1);
        }
        
        .sidebar-brand h4 {
            margin: 0;
            font-weight: 700;
        }
        
        .nav-menu {
            padding: 1rem 0;
        }
        
        .nav-item {
            margin: 0.25rem 0;
        }
        
        .nav-link {
            display: flex;
            align-items: center;
            padding: 0.75rem 1.5rem;
            color: rgba(255,255,255,0.8);
            text-decoration: none;
            transition: all 0.3s;
        }
        
        .nav-link:hover, .nav-link.active {
            background: rgba(255,255,255,0.1);
            color: white;
        }
        
        .nav-link i {
            width: 24px;
            margin-right: 0.75rem;
        }
        
        .main-content {
            margin-left: var(--sidebar-width);
            min-height: 100vh;
        }
        
        .topbar {
            background: white;
            box-shadow: 0 0.15rem 1.75rem 0 rgba(58, 59, 69, 0.15);
            padding: 1rem 1.5rem;
            display: flex;
            justify-content: space-between;
            align-items: center;
            position: sticky;
            top: 0;
            z-index: 100;
        }
        
        .user-dropdown {
            display: flex;
            align-items: center;
            cursor: pointer;
        }
        
        .user-info {
            text-align: right;
            margin-right: 0.75rem;
        }
        
        .user-info .name {
            font-weight: 600;
            color: #333;
        }
        
        .user-info .role {
            font-size: 0.75rem;
            color: #666;
            text-transform: uppercase;
        }
        
        .user-avatar {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            background: var(--primary-gradient);
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-weight: 600;
        }
        
        .content-wrapper {
            padding: 1.5rem;
        }
        
        .card {
            border: none;
            border-radius: 0.5rem;
            box-shadow: 0 0.15rem 1.75rem 0 rgba(58, 59, 69, 0.15);
        }
        
        .card-header {
            background: white;
            border-bottom: 1px solid #e3e6f0;
            font-weight: 700;
        }
        
        .border-left-primary {
            border-left: 0.25rem solid #4e73df !important;
        }
        
        .border-left-success {
            border-left: 0.25rem solid #1cc88a !important;
        }
        
        .border-left-info {
            border-left: 0.25rem solid #36b9cc !important;
        }
        
        .border-left-warning {
            border-left: 0.25rem solid #f6c23e !important;
        }
        
        .text-gray-300 {
            color: #dddfeb !important;
        }
        
        .text-gray-800 {
            color: #5a5c69 !important;
        }
        
        @media (max-width: 768px) {
            .sidebar {
                transform: translateX(-100%);
            }
            .sidebar.show {
                transform: translateX(0);
            }
            .main-content {
                margin-left: 0;
            }
            .topbar {
                position: sticky;
                top: 0;
                z-index: 100;
            }
            .sidebar-overlay {
                display: none;
                position: fixed;
                top: 0;
                left: 0;
                width: 100%;
                height: 100%;
                background: rgba(0,0,0,0.5);
                z-index: 999;
            }
            .sidebar-overlay.show {
                display: block;
            }
        }
    </style>
</head>
<body>
    <!-- Overlay for mobile sidebar -->
    <div class="sidebar-overlay" id="sidebarOverlay"></div>
    
    <!-- Sidebar -->
    <div class="sidebar" id="sidebar">
        <div class="sidebar-brand">
            <h4><i class="fas fa-briefcase me-2"></i>SIMagang</h4>
            <small>Sistem Informasi Magang</small>
        </div>
        
        <nav class="nav-menu">
            <div class="nav-item">
                <a href="dashboard.php" class="nav-link <?= basename($_SERVER['PHP_SELF']) === 'dashboard.php' ? 'active' : '' ?>">
                    <i class="fas fa-tachometer-alt"></i>
                    <span>Dashboard</span>
                </a>
            </div>
            
            <?php if(isStudent()): ?>
            <div class="nav-item">
                <a href="presensi.php" class="nav-link <?= basename($_SERVER['PHP_SELF']) === 'presensi.php' ? 'active' : '' ?>">
                    <i class="fas fa-clock"></i>
                    <span>Presensi</span>
                </a>
            </div>
            <div class="nav-item">
                <a href="logbook.php" class="nav-link <?= basename($_SERVER['PHP_SELF']) === 'logbook.php' ? 'active' : '' ?>">
                    <i class="fas fa-book"></i>
                    <span>Logbook Mingguan</span>
                </a>
            </div>
            <?php endif; ?>
            
            <?php if(isAdmin()): ?>
            <div class="nav-item">
                <a href="students.php" class="nav-link <?= basename($_SERVER['PHP_SELF']) === 'students.php' ? 'active' : '' ?>">
                    <i class="fas fa-users"></i>
                    <span>Kelola Siswa</span>
                </a>
            </div>
            <div class="nav-item">
                <a href="pending_approvals.php" class="nav-link <?= basename($_SERVER['PHP_SELF']) === 'pending_approvals.php' ? 'active' : '' ?>">
                    <i class="fas fa-user-clock"></i>
                    <span>Persetujuan Pendaftaran</span>
                </a>
            </div>
            <div class="nav-item">
                <a href="master_data.php" class="nav-link <?= in_array(basename($_SERVER['PHP_SELF']), ['master_data.php', 'jurusan.php', 'kelas.php']) ? 'active' : '' ?>">
                    <i class="fas fa-database"></i>
                    <span>Master Data</span>
                </a>
            </div>
            <div class="nav-item">
                <a href="presensi_report.php" class="nav-link <?= basename($_SERVER['PHP_SELF']) === 'presensi_report.php' ? 'active' : '' ?>">
                    <i class="fas fa-calendar-check"></i>
                    <span>Laporan Presensi</span>
                </a>
            </div>
            <div class="nav-item">
                <a href="logbook_report.php" class="nav-link <?= basename($_SERVER['PHP_SELF']) === 'logbook_report.php' ? 'active' : '' ?>">
                    <i class="fas fa-book-open"></i>
                    <span>Laporan Logbook</span>
                </a>
            </div>
            <?php endif; ?>
            
            <div class="nav-item">
                <a href="profile.php" class="nav-link <?= basename($_SERVER['PHP_SELF']) === 'profile.php' ? 'active' : '' ?>">
                    <i class="fas fa-user"></i>
                    <span>Profil</span>
                </a>
            </div>
            
            <div class="nav-item mt-4">
                <a href="logout.php" class="nav-link text-danger">
                    <i class="fas fa-sign-out-alt"></i>
                    <span>Logout</span>
                </a>
            </div>
        </nav>
    </div>
    
    <!-- Main Content -->
    <div class="main-content">
        <div class="topbar">
            <button class="btn btn-link d-md-none" id="sidebarToggle">
                <i class="fas fa-bars"></i>
            </button>
            
            <div class="user-dropdown dropdown">
                <div class="user-info">
                    <div class="name"><?= htmlspecialchars($_SESSION['nama_lengkap']) ?></div>
                    <div class="role"><?= ucfirst($_SESSION['role']) ?></div>
                </div>
                <div class="user-avatar">
                    <?= strtoupper(substr($_SESSION['nama_lengkap'], 0, 1)) ?>
                </div>
            </div>
        </div>
        
        <div class="content-wrapper">
