<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $title }} - SIMagang</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.2/font/bootstrap-icons.min.css" rel="stylesheet">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        html, body {
            height: 100%;
            overflow-x: hidden;
        }
        
        body {
            background-color: #f8fafc;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }
        
        .app-container {
            min-height: 100vh;
            display: flex;
            flex-direction: column;
        }
        
        .content-wrapper {
            display: flex;
            flex: 1;
        }
        
        .sidebar {
            background: linear-gradient(180deg, #dc2626 0%, #b91c1c 100%);
            min-height: 100vh;
            position: fixed;
            left: 0;
            top: 0;
            width: 280px;
            padding: 1.5rem 0;
            z-index: 1000;
            transition: transform 0.3s ease;
            box-shadow: 2px 0 10px rgba(0,0,0,0.1);
        }
        
        .main-content {
            margin-left: 280px;
            padding: 2.5rem;
            flex: 1;
            min-height: calc(100vh - 60px);
        }
        
        .footer {
            background: #2c3e50;
            color: white;
            text-align: center;
            padding: 1rem;
            position: relative;
            bottom: 0;
            width: 100%;
        }
        
        .sidebar h3 {
            color: white;
            padding: 0 1.5rem 1.5rem;
            margin: 0;
            font-size: 1.4rem;
            font-weight: 700;
            border-bottom: 1px solid rgba(255,255,255,0.1);
            padding-bottom: 1.5rem;
        }
        
        .user-profile {
            padding: 1.5rem;
            border-bottom: 1px solid rgba(255,255,255,0.1);
            text-align: center;
        }
        
        .user-profile .user-avatar {
            width: 60px;
            height: 60px;
            background: rgba(255,255,255,0.2);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 1rem;
            font-size: 1.5rem;
            color: white;
        }
        
        .user-profile .user-name {
            color: white;
            font-weight: 600;
            margin-bottom: 0.25rem;
        }
        
        .user-profile .user-email {
            color: rgba(255,255,255,0.7);
            font-size: 0.85rem;
        }
        
        .nav-item {
            list-style: none;
            margin: 0.25rem 0;
        }
        
        .nav-link {
            color: white;
            padding: 1rem 1.5rem;
            text-decoration: none;
            display: flex;
            align-items: center;
            transition: all 0.3s ease;
            border-radius: 0.75rem;
            margin: 0.25rem 0.75rem;
            font-size: 0.95rem;
            font-weight: 500;
            position: relative;
            overflow: hidden;
        }
        
        .nav-link:hover {
            background: rgba(255,255,255,0.15);
            padding-left: 2rem;
            transform: translateX(8px);
        }
        
        .nav-link.active {
            background: rgba(255,255,255,0.2);
            border-left: 4px solid #f87171;
            font-weight: 600;
        }
        
        .nav-link.active::before {
            content: '';
            position: absolute;
            left: 0;
            top: 0;
            height: 100%;
            width: 4px;
            background: linear-gradient(180deg, #f87171, #dc2626);
        }
        
        .nav-link i {
            margin-right: 1rem;
            width: 20px;
            font-size: 1.1rem;
            text-align: center;
        }
        
        .nav-link span {
            flex: 1;
        }
        
        .card {
            background: white;
            border-radius: 12px;
            box-shadow: 0 4px 12px rgba(0,0,0,0.08);
            border: none;
            margin-bottom: 1.5rem;
        }
        
        .card-header {
            background: linear-gradient(135deg, #dc2626 0%, #b91c1c 100%);
            color: white;
            border-radius: 12px 12px 0 0 !important;
            border: none;
            padding: 1rem 1.5rem;
        }
        
        .stat-card {
            background: white;
            border-radius: 16px;
            padding: 2rem;
            box-shadow: 0 4px 20px rgba(0,0,0,0.08);
            border: 1px solid rgba(226, 232, 240, 0.5);
            transition: all 0.3s ease;
            text-align: center;
        }
        
        .stat-card:hover {
            transform: translateY(-4px);
            box-shadow: 0 8px 30px rgba(0,0,0,0.12);
        }
        
        .stat-icon {
            width: 60px;
            height: 60px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 1.5rem;
            font-size: 1.5rem;
            color: white;
        }
        
        .stat-icon.primary {
            background: linear-gradient(135deg, #3b82f6, #1e40af);
        }
        
        .stat-icon.success {
            background: linear-gradient(135deg, #10b981, #059669);
        }
        
        .stat-icon.warning {
            background: linear-gradient(135deg, #f59e0b, #d97706);
        }
        
        .stat-icon.danger {
            background: linear-gradient(135deg, #ef4444, #dc2626);
        }
        
        .stat-value {
            font-size: 2rem;
            font-weight: 700;
            color: #1f2937;
            margin-bottom: 0.5rem;
        }
        
        .stat-label {
            color: #64748b;
            font-size: 0.9rem;
            font-weight: 500;
        }
        
        /* Responsive Design */
        @media (max-width: 768px) {
            .sidebar {
                transform: translateX(-100%);
            }
            
            .sidebar.active {
                transform: translateX(0);
            }
            
            .main-content {
                margin-left: 0;
                padding: 1rem;
            }
            
            .footer {
                margin-left: 0;
                width: 100%;
            }
        }
        
        @media (max-width: 480px) {
            .main-content {
                padding: 0.75rem;
            }
        }
        
        /* Mobile Menu Toggle */
        .mobile-menu-toggle {
            display: none;
            position: fixed;
            top: 1rem;
            left: 1rem;
            z-index: 1001;
            background: #dc2626;
            color: white;
            border: none;
            padding: 0.75rem;
            border-radius: 8px;
            cursor: pointer;
            box-shadow: 0 2px 8px rgba(0,0,0,0.2);
        }
        
        @media (max-width: 768px) {
            .mobile-menu-toggle {
                display: block;
            }
        }
        
        .intern-table {
            background: white;
            border-radius: 12px;
            overflow: hidden;
            box-shadow: 0 4px 12px rgba(0,0,0,0.08);
        }
        
        .intern-table .table {
            margin: 0;
        }
        
        .intern-table .table thead {
            background: linear-gradient(135deg, #dc2626 0%, #b91c1c 100%);
            color: white;
        }
        
        .intern-table .table thead th {
            border: none;
            padding: 1rem;
            font-weight: 600;
        }
        
        .intern-table .table tbody td {
            padding: 1rem;
            vertical-align: middle;
            border-color: #f1f5f9;
        }
        
        .badge-status {
            padding: 0.5rem 1rem;
            border-radius: 20px;
            font-size: 0.85rem;
            font-weight: 600;
        }
        
        .badge-active {
            background: #dcfce7;
            color: #166534;
        }
        
        .badge-pending {
            background: #fef3c7;
            color: #92400e;
        }
        
        .badge-completed {
            background: #dbeafe;
            color: #1e40af;
        }
        
        .company-info {
            background: linear-gradient(135deg, #fef2f2 0%, #fee2e2 100%);
            border-radius: 12px;
            padding: 1.5rem;
            margin-bottom: 1.5rem;
            border-left: 4px solid #dc2626;
        }
        
        .company-info h5 {
            color: #dc2626;
            margin-bottom: 1rem;
        }
        
        .company-info .info-item {
            display: flex;
            justify-content: space-between;
            margin-bottom: 0.5rem;
        }
        
        .company-info .info-label {
            font-weight: 600;
            color: #374151;
        }
        
        .company-info .info-value {
            color: #64748b;
        }
    </style>
</head>
<body>
    <!-- Mobile Menu Toggle -->
    <button class="mobile-menu-toggle" onclick="toggleSidebar()">
        <i class="bi bi-list"></i>
    </button>

    <div class="app-container">
        <div class="content-wrapper">
            <!-- Sidebar -->
            <nav class="sidebar" id="sidebar">
                <h3><i class="bi bi-mortarboard-fill"></i> SIMagang</h3>
                
                <!-- User Profile -->
                <div class="user-profile">
                    <div class="user-avatar">
                        <i class="bi bi-building"></i>
                    </div>
                    <div class="user-name">{{ $user->name ?? 'DUDI' }}</div>
                    <div class="user-email">{{ $user->email }}</div>
                </div>
                
                <ul style="padding: 0; margin: 0;">
                    <li class="nav-item">
                        <a href="{{ route('dashboard.dudi') }}" class="nav-link active">
                            <i class="bi bi-speedometer2"></i>
                            <span>Dashboard</span>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="#" class="nav-link">
                            <i class="bi bi-people-fill"></i>
                            <span>Data Magang</span>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="#" class="nav-link">
                            <i class="bi bi-journal-text"></i>
                            <span>Logbook Review</span>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="#" class="nav-link">
                            <i class="bi bi-calendar-check"></i>
                            <span>Presensi Magang</span>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="#" class="nav-link">
                            <i class="bi bi-file-earmark-text"></i>
                            <span>Pengajuan Izin</span>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="#" class="nav-link">
                            <i class="bi bi-award"></i>
                            <span>Penilaian</span>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="#" class="nav-link">
                            <i class="bi bi-graph-up"></i>
                            <span>Laporan</span>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="#" class="nav-link">
                            <i class="bi bi-building"></i>
                            <span>Profil Perusahaan</span>
                        </a>
                    </li>
                    <li class="nav-item" style="border-top: 1px solid rgba(255,255,255,0.1); margin-top: 1rem; padding-top: 1rem;">
                        <a href="{{ route('logout') }}" class="nav-link" onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                            <i class="bi bi-box-arrow-right"></i>
                            <span>Logout</span>
                        </a>
                        <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">
                            @csrf
                        </form>
                    </li>
                </ul>
            </nav>

            <!-- Main Content -->
            <div class="main-content">
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <div>
                        <h1 class="h3 mb-0">Dashboard DUDI</h1>
                        <p class="text-muted mb-0">Selamat datang di SIMagang SMKN 1</p>
                    </div>
                    <div>
                        <span class="badge bg-danger">{{ $user->role ?? 'dudi' }}</span>
                    </div>
                </div>

                <!-- Company Information -->
                <div class="company-info">
                    <h5><i class="bi bi-building me-2"></i>Informasi Perusahaan</h5>
                    <div class="info-item">
                        <span class="info-label">Nama Perusahaan:</span>
                        <span class="info-value">PT. Teknologi Indonesia</span>
                    </div>
                    <div class="info-item">
                        <span class="info-label">Industri:</span>
                        <span class="info-value">Teknologi Informasi</span>
                    </div>
                    <div class="info-item">
                        <span class="info-label">Kuota Magang:</span>
                        <span class="info-value">15 Siswa</span>
                    </div>
                    <div class="info-item">
                        <span class="info-label">Terisi:</span>
                        <span class="info-value">8 Siswa</span>
                    </div>
                </div>

                <!-- Statistics Cards -->
                <div class="row mb-4">
                    <div class="col-lg-3 col-md-6 mb-4">
                        <div class="stat-card">
                            <div class="stat-icon primary">
                                <i class="bi bi-people-fill"></i>
                            </div>
                            <div class="stat-value">8</div>
                            <div class="stat-label">Total Magang</div>
                        </div>
                    </div>
                    <div class="col-lg-3 col-md-6 mb-4">
                        <div class="stat-card">
                            <div class="stat-icon success">
                                <i class="bi bi-journal-text"></i>
                            </div>
                            <div class="stat-value">45</div>
                            <div class="stat-label">Logbook Review</div>
                        </div>
                    </div>
                    <div class="col-lg-3 col-md-6 mb-4">
                        <div class="stat-card">
                            <div class="stat-icon warning">
                                <i class="bi bi-file-earmark-text"></i>
                            </div>
                            <div class="stat-value">3</div>
                            <div class="stat-label">Izin Pending</div>
                        </div>
                    </div>
                    <div class="col-lg-3 col-md-6 mb-4">
                        <div class="stat-card">
                            <div class="stat-icon danger">
                                <i class="bi bi-award"></i>
                            </div>
                            <div class="stat-value">12</div>
                            <div class="stat-label">Penilaian Selesai</div>
                        </div>
                    </div>
                </div>

                <!-- Intern List Table -->
                <div class="card">
                    <div class="card-header">
                        <h5 class="mb-0">Daftar Magang Aktif</h5>
                    </div>
                    <div class="card-body p-0">
                        <div class="intern-table">
                            <table class="table table-hover">
                                <thead>
                                    <tr>
                                        <th>No</th>
                                        <th>Nama Magang</th>
                                        <th>Sekolah</th>
                                        <th>Periode</th>
                                        <th>Status</th>
                                        <th>Aksi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr>
                                        <td>1</td>
                                        <td>
                                            <div class="d-flex align-items-center">
                                                <div class="rounded-circle bg-primary text-white d-flex align-items-center justify-content-center me-2" style="width: 32px; height: 32px; font-size: 0.8rem;">
                                                    TS
                                                </div>
                                                <div>
                                                    <div class="fw-semibold">Test Siswa</div>
                                                    <small class="text-muted">NIS: 12345</small>
                                                </div>
                                            </div>
                                        </td>
                                        <td>SMKN 1 Jakarta</td>
                                        <td>Jan 2024 - Jun 2024</td>
                                        <td><span class="badge-status badge-active">Aktif</span></td>
                                        <td>
                                            <button class="btn btn-sm btn-primary me-1">
                                                <i class="bi bi-eye"></i>
                                            </button>
                                            <button class="btn btn-sm btn-success">
                                                <i class="bi bi-award"></i>
                                            </button>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>2</td>
                                        <td>
                                            <div class="d-flex align-items-center">
                                                <div class="rounded-circle bg-warning text-white d-flex align-items-center justify-content-center me-2" style="width: 32px; height: 32px; font-size: 0.8rem;">
                                                    AS
                                                </div>
                                                <div>
                                                    <div class="fw-semibold">Ahmad Santoso</div>
                                                    <small class="text-muted">NIS: 12346</small>
                                                </div>
                                            </div>
                                        </td>
                                        <td>SMKN 1 Jakarta</td>
                                        <td>Jan 2024 - Jun 2024</td>
                                        <td><span class="badge-status badge-active">Aktif</span></td>
                                        <td>
                                            <button class="btn btn-sm btn-primary me-1">
                                                <i class="bi bi-eye"></i>
                                            </button>
                                            <button class="btn btn-sm btn-success">
                                                <i class="bi bi-award"></i>
                                            </button>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>3</td>
                                        <td>
                                            <div class="d-flex align-items-center">
                                                <div class="rounded-circle bg-info text-white d-flex align-items-center justify-content-center me-2" style="width: 32px; height: 32px; font-size: 0.8rem;">
                                                    SR
                                                </div>
                                                <div>
                                                    <div class="fw-semibold">Siti Rahayu</div>
                                                    <small class="text-muted">NIS: 12347</small>
                                                </div>
                                            </div>
                                        </td>
                                        <td>SMKN 2 Jakarta</td>
                                        <td>Jan 2024 - Jun 2024</td>
                                        <td><span class="badge-status badge-completed">Selesai</span></td>
                                        <td>
                                            <button class="btn btn-sm btn-primary me-1">
                                                <i class="bi bi-eye"></i>
                                            </button>
                                            <button class="btn btn-sm btn-danger">
                                                <i class="bi bi-file-earmark-pdf"></i>
                                            </button>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

                <!-- Recent Activity -->
                <div class="card">
                    <div class="card-header">
                        <h5 class="mb-0">Aktivitas Terkini</h5>
                    </div>
                    <div class="card-body">
                        <div class="list-group list-group-flush">
                            <div class="list-group-item d-flex justify-content-between align-items-center">
                                <div>
                                    <h6 class="mb-1">Logbook Review</h6>
                                    <p class="mb-1 text-muted">Test Siswa - Mengerjakan project web development</p>
                                    <small class="text-muted">2 jam yang lalu</small>
                                </div>
                                <span class="badge bg-primary">Review</span>
                            </div>
                            <div class="list-group-item d-flex justify-content-between align-items-center">
                                <div>
                                    <h6 class="mb-1">Pengajuan Izin</h6>
                                    <p class="mb-1 text-muted">Ahmad Santoso - Izin sakit</p>
                                    <small class="text-muted">5 jam yang lalu</small>
                                </div>
                                <span class="badge bg-warning">Pending</span>
                            </div>
                            <div class="list-group-item d-flex justify-content-between align-items-center">
                                <div>
                                    <h6 class="mb-1">Penilaian Bulanan</h6>
                                    <p class="mb-1 text-muted">Test Siswa - Nilai: 88</p>
                                    <small class="text-muted">Kemarin</small>
                                </div>
                                <span class="badge bg-success">Selesai</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Footer -->
        <footer class="footer">
            <div class="container-fluid">
                <p class="mb-0">&copy; 2024 SIMagang SMKN 1. All rights reserved.</p>
            </div>
        </footer>
    </div>

    <script>
        function toggleSidebar() {
            const sidebar = document.getElementById('sidebar');
            sidebar.classList.toggle('active');
        }

        // Close sidebar when clicking outside on mobile
        document.addEventListener('click', function(event) {
            const sidebar = document.getElementById('sidebar');
            const toggle = document.querySelector('.mobile-menu-toggle');
            
            if (window.innerWidth <= 768 && 
                !sidebar.contains(event.target) && 
                !toggle.contains(event.target) && 
                sidebar.classList.contains('active')) {
                sidebar.classList.remove('active');
            }
        });
    </script>
</body>
</html>
