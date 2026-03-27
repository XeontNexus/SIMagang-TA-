<?php
require_once 'config/config.php';

if(isLoggedIn()) {
    redirect(base_url('dashboard.php'));
}

$db = new Database();
$view = $_GET['view'] ?? 'login';

// Handle Login
if($_SERVER['REQUEST_METHOD'] == 'POST' && $view === 'login') {
    $username = trim($_POST['username']);
    $password = trim($_POST['password']);
    
    if(empty($username) || empty($password)) {
        $error = 'Username dan password harus diisi!';
    } else {
        // Cari user berdasarkan username (tanpa filter status)
        $db->query('SELECT * FROM users WHERE username = :username');
        $db->bind(':username', $username);
        $user = $db->single();
        
        if($user && password_verify($password, $user['password'])) {
            // Cek status akun
            if($user['status'] === 'pending') {
                $error = 'Akun Anda belum di-approve oleh admin. Silakan tunggu persetujuan atau hubungi admin.';
            } elseif($user['status'] === 'active') {
                $_SESSION['user_id'] = $user['id'];
                $_SESSION['username'] = $user['username'];
                $_SESSION['nama_lengkap'] = $user['nama_lengkap'];
                $_SESSION['role'] = $user['role'];
                $_SESSION['email'] = $user['email'];
                
                redirect(base_url('dashboard.php'));
            } else {
                $error = 'Akun Anda tidak aktif. Silakan hubungi admin.';
            }
        } else {
            $error = 'Username atau password salah!';
        }
    }
}

// Handle Registration
if($_SERVER['REQUEST_METHOD'] == 'POST' && $view === 'register') {
    $username = trim($_POST['username'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';
    $confirm_password = $_POST['confirm_password'] ?? '';
    $nama_lengkap = trim($_POST['nama_lengkap'] ?? '');
    
    if(empty($username) || empty($email) || empty($password) || empty($nama_lengkap)) {
        $error = 'Semua field harus diisi!';
    } elseif(strlen($username) < 4) {
        $error = 'Username minimal 4 karakter!';
    } elseif(!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = 'Format email tidak valid!';
    } elseif(strlen($password) < 6) {
        $error = 'Password minimal 6 karakter!';
    } elseif($password !== $confirm_password) {
        $error = 'Password dan konfirmasi password tidak cocok!';
    } else {
        $db->query('SELECT id FROM users WHERE username = :username');
        $db->bind(':username', $username);
        if($db->single()) {
            $error = 'Username sudah digunakan!';
        } else {
            $db->query('SELECT id FROM users WHERE email = :email');
            $db->bind(':email', $email);
            if($db->single()) {
                $error = 'Email sudah terdaftar!';
            } else {
                $hashed_password = password_hash($password, PASSWORD_DEFAULT);
                $db->query('INSERT INTO users (username, password, nama_lengkap, email, role, status) 
                           VALUES (:username, :password, :nama_lengkap, :email, "student", "pending")');
                $db->bind(':username', $username);
                $db->bind(':password', $hashed_password);
                $db->bind(':nama_lengkap', $nama_lengkap);
                $db->bind(':email', $email);
                
                if($db->execute()) {
                    $success = 'Pendaftaran berhasil! Akun Anda menunggu persetujuan admin.';
                    $view = 'login';
                } else {
                    $error = 'Terjadi kesalahan saat mendaftar.';
                }
            }
        }
    }
}

// Handle Forgot Password
if($_SERVER['REQUEST_METHOD'] == 'POST' && $view === 'forgot') {
    $username = trim($_POST['username'] ?? '');
    $email = trim($_POST['email'] ?? '');
    
    if(empty($username) || empty($email)) {
        $error = 'Username dan email harus diisi!';
    } else {
        $db->query('SELECT * FROM users WHERE username = :username AND email = :email');
        $db->bind(':username', $username);
        $db->bind(':email', $email);
        $user = $db->single();
        
        if($user) {
            $success = 'Verifikasi berhasil! Silakan hubungi admin via WhatsApp.';
            $show_wa = true;
        } else {
            $error = 'Username dan email tidak cocok!';
        }
    }
}

$admin_whatsapp = '6281234567890'; // Ganti dengan nomor WA admin
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - <?= APP_NAME ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <style>
        body {
            background: linear-gradient(135deg, #1565C0 0%, #0D47A1 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .login-card {
            background: white;
            border-radius: 20px;
            overflow: hidden;
            width: 100%;
            max-width: 450px;
        }
        .login-header {
            background: linear-gradient(135deg, #1565C0 0%, #0D47A1 100%);
            color: white;
            padding: 2rem;
            text-align: center;
        }
        .login-header h1 {
            margin: 0;
            font-size: 2rem;
            font-weight: 700;
        }
        .login-header p {
            margin: 0.5rem 0 0 0;
            opacity: 0.9;
        }
        .login-body {
            padding: 2rem;
        }
        .form-floating {
            margin-bottom: 1rem;
        }
        .btn-login {
            background: linear-gradient(135deg, #1565C0 0%, #0D47A1 100%);
            border: none;
            width: 100%;
            padding: 0.75rem;
            font-size: 1.1rem;
            font-weight: 600;
        }
        .btn-login:hover {
            opacity: 0.9;
        }
        .password-toggle {
            cursor: pointer;
            position: absolute;
            right: 15px;
            top: 50%;
            transform: translateY(-50%);
            z-index: 10;
            color: #6c757d;
        }
        .password-toggle:hover {
            color: #1565C0;
        }
        .form-floating>.form-control {
            padding-right: 40px;
        }
        .auth-links {
            text-align: center;
            margin-top: 1.5rem;
        }
        .auth-links a {
            color: #1565C0;
            text-decoration: none;
        }
        .auth-links a:hover {
            text-decoration: underline;
        }
        .divider {
            text-align: center;
            margin: 1.5rem 0;
            position: relative;
        }
        .divider::before {
            content: '';
            position: absolute;
            top: 50%;
            left: 0;
            right: 0;
            height: 1px;
            background: #e0e0e0;
        }
        .divider span {
            background: white;
            padding: 0 1rem;
            position: relative;
            color: #666;
            font-size: 0.9rem;
        }
        .wa-button {
            background: #25D366;
            color: white;
            border: none;
            padding: 0.75rem 1.5rem;
            border-radius: 8px;
            font-weight: 600;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            margin-top: 1rem;
        }
        .wa-button:hover {
            background: #128C7E;
            color: white;
        }
        .back-link {
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            color: #666;
            text-decoration: none;
            margin-bottom: 1rem;
        }
        .back-link:hover {
            color: #1565C0;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-5">
                <div class="login-card">
                    <div class="login-header">
                        <h1><i class="fas fa-briefcase me-2"></i>SIMagang</h1>
                        <p>Sistem Informasi Magang</p>
                    </div>
                    <div class="login-body">
                        <?php if(isset($error)): ?>
                            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                                <i class="fas fa-exclamation-circle me-2"></i><?= $error ?>
                                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                            </div>
                        <?php endif; ?>
                        
                        <?php if(isset($success)): ?>
                            <div class="alert alert-success alert-dismissible fade show" role="alert">
                                <i class="fas fa-check-circle me-2"></i><?= $success ?>
                                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                            </div>
                        <?php endif; ?>
                        
                        <?php if($view === 'login'): ?>
                            <form method="POST" action="">
                                <div class="form-floating">
                                    <input type="text" class="form-control" id="username" name="username" placeholder="Username" required>
                                    <label for="username"><i class="fas fa-user me-2"></i>Username</label>
                                </div>
                                <div class="form-floating position-relative">
                                    <input type="password" class="form-control" id="password" name="password" placeholder="Password" required>
                                    <label for="password"><i class="fas fa-lock me-2"></i>Password</label>
                                    <span class="password-toggle" onclick="togglePassword('password', 'toggleIcon')">
                                        <i class="fas fa-eye" id="toggleIcon"></i>
                                    </span>
                                </div>
                                <button type="submit" class="btn btn-primary btn-login">
                                    <i class="fas fa-sign-in-alt me-2"></i>Login
                                </button>
                            </form>
                            
                            <div class="auth-links">
                                <a href="?view=forgot"><i class="fas fa-key me-1"></i>Lupa Password?</a>
                            </div>
                            
                            <div class="divider">
                                <span>ATAU</span>
                            </div>
                            
                            <div class="text-center">
                                <p class="text-muted mb-2">Belum punya akun?</p>
                                <a href="?view=register" class="btn btn-outline-primary w-100">
                                    <i class="fas fa-user-plus me-2"></i>Daftar Sekarang
                                </a>
                            </div>
                            
                            <hr class="my-4">
                            <div class="text-center">
                                <small class="text-muted">
                                    <strong>Demo Account:</strong><br>
                                    Admin: <code>admin</code> / <code>password</code><br>
                                    Student: <code>student1</code> / <code>password</code>
                                </small>
                            </div>
                        
                        <?php elseif($view === 'register'): ?>
                            <a href="?view=login" class="back-link">
                                <i class="fas fa-arrow-left"></i> Kembali ke Login
                            </a>
                            
                            <h4 class="text-center mb-4"><i class="fas fa-user-plus me-2 text-primary"></i>Daftar Akun</h4>
                            
                            <form method="POST" action="?view=register">
                                <div class="form-floating">
                                    <input type="text" class="form-control" id="nama_lengkap" name="nama_lengkap" placeholder="Nama Lengkap" required>
                                    <label for="nama_lengkap"><i class="fas fa-id-card me-2"></i>Nama Lengkap</label>
                                </div>
                                <div class="form-floating">
                                    <input type="text" class="form-control" id="username" name="username" placeholder="Username" required minlength="4">
                                    <label for="username"><i class="fas fa-user me-2"></i>Username</label>
                                </div>
                                <div class="form-floating">
                                    <input type="email" class="form-control" id="email" name="email" placeholder="Email Gmail" required>
                                    <label for="email"><i class="fas fa-envelope me-2"></i>Email Gmail</label>
                                </div>
                                <div class="form-floating position-relative">
                                    <input type="password" class="form-control" id="password" name="password" placeholder="Password" required minlength="6">
                                    <label for="password"><i class="fas fa-lock me-2"></i>Password</label>
                                    <span class="password-toggle" onclick="togglePassword('password', 'toggleIcon1')">
                                        <i class="fas fa-eye" id="toggleIcon1"></i>
                                    </span>
                                </div>
                                <div class="form-floating position-relative">
                                    <input type="password" class="form-control" id="confirm_password" name="confirm_password" placeholder="Konfirmasi Password" required>
                                    <label for="confirm_password"><i class="fas fa-lock me-2"></i>Konfirmasi Password</label>
                                    <span class="password-toggle" onclick="togglePassword('confirm_password', 'toggleIcon2')">
                                        <i class="fas fa-eye" id="toggleIcon2"></i>
                                    </span>
                                </div>
                                <button type="submit" class="btn btn-primary btn-login">
                                    <i class="fas fa-user-plus me-2"></i>Daftar
                                </button>
                            </form>
                            
                            <div class="alert alert-info mt-3">
                                <small><i class="fas fa-info-circle me-1"></i>Akun akan aktif setelah disetujui admin.</small>
                            </div>
                        
                        <?php elseif($view === 'forgot'): ?>
                            <a href="?view=login" class="back-link">
                                <i class="fas fa-arrow-left"></i> Kembali ke Login
                            </a>
                            
                            <h4 class="text-center mb-4"><i class="fas fa-key me-2 text-primary"></i>Lupa Password</h4>
                            
                            <?php if(!isset($show_wa)): ?>
                                <form method="POST" action="?view=forgot">
                                    <div class="form-floating">
                                        <input type="text" class="form-control" id="username" name="username" placeholder="Username" required>
                                        <label for="username"><i class="fas fa-user me-2"></i>Username</label>
                                    </div>
                                    <div class="form-floating">
                                        <input type="email" class="form-control" id="email" name="email" placeholder="Email" required>
                                        <label for="email"><i class="fas fa-envelope me-2"></i>Email yang terdaftar</label>
                                    </div>
                                    <button type="submit" class="btn btn-primary btn-login">
                                        <i class="fas fa-search me-2"></i>Verifikasi Akun
                                    </button>
                                </form>
                                
                                <div class="divider">
                                    <span>ATAU</span>
                                </div>
                                
                                <div class="text-center">
                                    <p class="text-muted">Hubungi admin langsung via WhatsApp</p>
                                    <a href="https://wa.me/<?= $admin_whatsapp ?>?text=Halo%20Admin%20SIMagang,%20saya%20lupa%20password%20dan%20ingin%20reset%20password.%0A%0AUsername:%20[isi%20username]%0AEmail:%20[isi%20email]" 
                                       target="_blank" class="wa-button">
                                        <i class="fab fa-whatsapp fa-lg"></i>Hubungi Admin via WA
                                    </a>
                                </div>
                            <?php else: ?>
                                <div class="text-center">
                                    <i class="fas fa-check-circle text-success fa-3x mb-3"></i>
                                    <h5>Verifikasi Berhasil!</h5>
                                    <p class="text-muted">Silakan hubungi admin via WhatsApp untuk reset password Anda.</p>
                                    <a href="https://wa.me/<?= $admin_whatsapp ?>?text=Halo%20Admin%20SIMagang,%0ASaya%20sudah%20verifikasi%20untuk%20reset%20password.%0A%0AUsername:%20<?= urlencode($username) ?>%0AEmail:%20<?= urlencode($email) ?>" 
                                       target="_blank" class="wa-button">
                                        <i class="fab fa-whatsapp fa-lg"></i>Hubungi Admin via WA
                                    </a>
                                </div>
                            <?php endif; ?>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        function togglePassword(inputId, iconId) {
            const passwordInput = document.getElementById(inputId);
            const toggleIcon = document.getElementById(iconId);
            if (passwordInput.type === 'password') {
                passwordInput.type = 'text';
                toggleIcon.classList.remove('fa-eye');
                toggleIcon.classList.add('fa-eye-slash');
            } else {
                passwordInput.type = 'password';
                toggleIcon.classList.remove('fa-eye-slash');
                toggleIcon.classList.add('fa-eye');
            }
        }
    </script>
</body>
</html>
