<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'SIMagang')</title>
    
    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <style>
        :root {
            --primary-color: #4e73df;
            --secondary-color: #858796;
            --success-color: #1cc88a;
            --info-color: #36b9cc;
            --warning-color: #f6c23e;
            --danger-color: #e74a3b;
        }
        
        body {
            font-family: 'Nunito', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
            background-color: #f8f9fc;
            height: 100vh;
            overflow: hidden;
        }
        
        .sidebar {
            position: fixed;
            top: 0;
            left: 0;
            height: 100vh;
            width: 250px;
            background: linear-gradient(180deg, #1e3a8a 0%, #1e40af 50%, #1d4ed8 100%);
            transition: transform 0.3s ease-in-out;
            z-index: 1040;
            overflow-y: auto;
            display: flex;
            flex-direction: column;
            padding-right: 15px;
            margin-right: -15px;
            scrollbar-width: none; /* Firefox */
            -ms-overflow-style: none; /* IE and Edge */
        }
        
        /* Hide scrollbar but keep functionality - scrollbar hidden behind sidebar */
        .sidebar::-webkit-scrollbar {
            width: 15px;
        }
        
        .sidebar::-webkit-scrollbar-track {
            background: transparent;
        }
        
        .sidebar::-webkit-scrollbar-thumb {
            background: transparent;
        }
        
        .sidebar::-webkit-scrollbar-thumb:hover {
            background: transparent;
        }

        /* Hide scrollbar on navigation container */
        .sidebar .flex-grow-1.overflow-y-auto {
            padding-right: 15px;
            margin-right: -15px;
            scrollbar-width: none; /* Firefox */
            -ms-overflow-style: none; /* IE and Edge */
        }

        .sidebar .flex-grow-1.overflow-y-auto::-webkit-scrollbar {
            width: 15px;
        }

        .sidebar .flex-grow-1.overflow-y-auto::-webkit-scrollbar-track {
            background: transparent;
        }

        .sidebar .flex-grow-1.overflow-y-auto::-webkit-scrollbar-thumb {
            background: transparent;
        }

        .sidebar .flex-grow-1.overflow-y-auto::-webkit-scrollbar-thumb:hover {
            background: transparent;
        }
        
        .sidebar .nav-link {
            color: rgba(255,255,255,0.8);
            padding: 1rem;
            border-radius: 0;
        }
        
        .sidebar .nav-link:hover,
        .sidebar .nav-link.active {
            color: #fff;
            background-color: rgba(255,255,255,0.1);
        }
        
        .sidebar .nav-link i {
            margin-right: 0.5rem;
        }

        .sidebar .btn-logout-hover:hover {
            background-color: #dc3545 !important;
            border-color: #dc3545 !important;
            color: #fff !important;
        }

        .topbar {
            position: fixed;
            top: 0;
            left: 250px;
            right: 0;
            height: 4.375rem;
            background-color: #fff;
            box-shadow: 0 0.15rem 1.75rem 0 rgba(58,59,69,0.15);
            z-index: 1020;
        }
        
        .main-content {
            margin-left: 250px;
            margin-top: 4.375rem;
            height: calc(100vh - 4.375rem);
            overflow-y: auto;
        }
        
        .border-white-20 {
            border-color: rgba(255, 255, 255, 0.2) !important;
        }
        
        .card-stats {
            border-left: 0.25rem solid;
        }
        
        .card-stats.primary { border-left-color: var(--primary-color); }
        .card-stats.success { border-left-color: var(--success-color); }
        .card-stats.info { border-left-color: var(--info-color); }
        .card-stats.warning { border-left-color: var(--warning-color); }
        .card-stats.danger { border-left-color: var(--danger-color); }
        
        /* Mobile Sidebar */
        @media (max-width: 767.98px) {
            .sidebar {
                transform: translateX(-100%);
            }
            
            .sidebar.show {
                transform: translateX(0);
            }
            
            .sidebar-overlay {
                display: none;
                position: fixed;
                top: 0;
                left: 0;
                width: 100%;
                height: 100%;
                background: rgba(0,0,0,0.5);
                z-index: 1035;
            }
            
            .sidebar-overlay.show {
                display: block;
            }
            
            .topbar {
                left: 0;
            }
            
            .main-content {
                margin-left: 0 !important;
            }
        }
        
        @media (min-width: 768px) {
            .sidebar-toggler {
                display: none;
            }
        }
    </style>
    
    @stack('styles')
</head>
<body>
    <!-- Sidebar Overlay for Mobile -->
    <div class="sidebar-overlay" id="sidebarOverlay" onclick="toggleSidebar()"></div>
    
    <div class="d-flex">
        <!-- Sidebar -->
        @auth
        <div class="sidebar p-0" id="sidebar">
            <div class="d-flex flex-column h-100">
                <!-- Header -->
                <div class="p-3">
                    <div class="d-flex align-items-center justify-content-between">
                        <a href="/" class="text-white text-decoration-none w-100">
                            <div class="d-flex align-items-center">
                                <i class="fas fa-graduation-cap fs-4 me-2"></i>
                                <span class="fs-4 fw-bold">SIMagang</span>
                            </div>
                            <div class="text-white-50 fw-light" style="font-size: 0.7rem; margin-top: -3px; margin-left: 34px; letter-spacing: 0.5px;">SMKN 1 Perhentian Raja</div>
                            <div class="mt-2 d-flex align-items-center text-white" style="margin-left: 34px; font-size: 0.85rem;">
                                <i class="fas fa-user-circle me-2"></i>
                                <span>{{ auth()->user()->username }}</span>
                            </div>
                        </a>
                    </div>
                    <hr class="text-white-50">
                </div>
                
                <!-- Navigation -->
                <div class="flex-grow-1 overflow-y-auto">
                    @if(auth()->user()->isAdmin())
                        @include('layouts.partials.admin-sidebar')
                    @else
                        @include('layouts.partials.student-sidebar')
                    @endif
                </div>
                
                <!-- APK Version & Logout -->
                <!-- APK Version Info - di atas border -->
                <div class="text-center text-white-50 px-3 pt-2 pb-1" style="font-size: 0.8rem;">
                    <i class="fas fa-mobile-alt me-1"></i>
                    Versi APK {{ config('app.apk_version', '1.0.0') }}
                </div>
                
                <!-- Border & Logout Button -->
                <div class="p-3 border-top border-white-20">
                    <!-- Logout Button -->
                    <form id="logout-form" method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button type="button" class="btn btn-outline-light w-100 btn-logout-hover" onclick="confirmLogout()">
                            <i class="fas fa-sign-out-alt me-2"></i>Logout
                        </button>
                    </form>
                </div>
            </div>
        </div>
        @endauth
        
        <!-- Main Content -->
        <div class="flex-grow-1 main-content">
            @auth
            <!-- Topbar -->
            <nav class="topbar navbar navbar-expand navbar-light bg-white static-top">
                <div class="container-fluid">
                    @auth
                    <button class="btn btn-link sidebar-toggler me-2" onclick="toggleSidebar()">
                        <i class="fas fa-bars"></i>
                    </button>
                    @endauth
                    <span class="navbar-brand mb-0 h1">
                        @yield('page-title', 'Dashboard')
                    </span>
                    
                    <ul class="navbar-nav ms-auto">
                        <li class="nav-item">
                            <span class="nav-link">
                                <span class="me-2 d-none d-lg-inline text-gray-600 small">
                                    {{ auth()->user()->nama_lengkap }}
                                </span>
                                <i class="fas fa-user-circle fa-fw fs-5"></i>
                            </span>
                        </li>
                    </ul>
                </div>
            </nav>
            @endauth
            
            <!-- Page Content -->
            <div class="container-fluid p-4">
                @auth
                    @if(!request()->routeIs('admin.dashboard') && !request()->routeIs('student.dashboard'))
                        <div class="d-flex align-items-center mb-3">
                            <button onclick="window.history.length > 1 ? window.history.back() : window.location.href = '/';" class="btn btn-sm btn-light border shadow-sm px-3 py-1 fw-semibold text-secondary">
                                <i class="fas fa-arrow-left me-1"></i> Kembali
                            </button>
                        </div>
                    @endif

                    @if(auth()->user()->isSiswa() && isset($studentNotifications) && $studentNotifications->count() > 0)
                        @foreach($studentNotifications as $notif)
                            <div class="alert alert-{{ $notif->type === 'success' ? 'success' : ($notif->type === 'danger' ? 'danger' : 'info') }} alert-dismissible fade show shadow-sm" role="alert">
                                <h6 class="alert-heading mb-1"><i class="fas fa-bell me-2"></i>{{ $notif->title }}</h6>
                                <p class="mb-2 small">{{ $notif->message }}</p>
                                <form action="{{ route('student.notifications.read', $notif) }}" method="POST" class="d-inline mark-notif-read-form">
                                    @csrf
                                    <button type="submit" class="btn btn-sm btn-outline-dark">Tutup</button>
                                </form>
                            </div>
                        @endforeach
                    @endif
                @endauth
                @yield('content')
            </div>
        </div>
    </div>
    
    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <!-- SweetAlert2 -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    
    @stack('scripts')

    <!-- Session Alert Modal -->
    @if(session('success') || session('error') || session('info'))
    <div class="modal fade" id="sessionAlertModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header {{ session('success') ? 'bg-success' : (session('error') ? 'bg-danger' : 'bg-info') }} text-white">
                    <h5 class="modal-title">
                        <i class="fas {{ session('success') ? 'fa-check-circle' : (session('error') ? 'fa-exclamation-circle' : 'fa-info-circle') }} me-2"></i>
                        {{ session('success') ? 'Berhasil!' : (session('error') ? 'Error!' : 'Informasi') }}
                    </h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body text-center py-4">
                    <i class="fas {{ session('success') ? 'fa-check-circle' : (session('error') ? 'fa-times-circle' : 'fa-info-circle') }} fa-3x mb-3
                       {{ session('success') ? 'text-success' : (session('error') ? 'text-danger' : 'text-info') }}"></i>
                    <p class="mb-0 fs-5">{{ session('success') ?? session('error') ?? session('info') }}</p>
                </div>
                <div class="modal-footer justify-content-center">
                    <button type="button" class="btn {{ session('success') ? 'btn-success' : (session('error') ? 'btn-danger' : 'btn-info') }}" data-bs-dismiss="modal">OK</button>
                </div>
            </div>
        </div>
    </div>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            var modal = new bootstrap.Modal(document.getElementById('sessionAlertModal'));
            modal.show();
        });
    </script>
    @endif

    <script>
        function toggleSidebar() {
            document.getElementById('sidebar').classList.toggle('show');
            document.getElementById('sidebarOverlay').classList.toggle('show');
        }

        function confirmLogout() {
            Swal.fire({
                title: 'Logout?',
                text: 'Apakah Anda yakin ingin keluar dari sistem?',
                icon: 'question',
                showCancelButton: true,
                confirmButtonColor: '#e74a3b',
                cancelButtonColor: '#6c757d',
                confirmButtonText: '<i class="fas fa-sign-out-alt me-1"></i> Ya, Logout!',
                cancelButtonText: '<i class="fas fa-times me-1"></i> Tidak',
                reverseButtons: true
            }).then((result) => {
                if (result.isConfirmed) {
                    document.getElementById('logout-form').submit();
                }
            });
        }
    </script>
</body>
</html>
