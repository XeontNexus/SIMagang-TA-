<?php
require_once 'config/config.php';

// Security: Block direct admin access attempts
if(isset($_GET['admin']) || strpos($_SERVER['REQUEST_URI'], 'admin') !== false) {
    header('HTTP/1.0 403 Forbidden');
    echo '<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Access Denied</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
    <div class="container vh-100 d-flex align-items-center justify-content-center">
        <div class="text-center">
            <div class="card shadow">
                <div class="card-body p-5">
                    <i class="fas fa-shield-alt fa-4x text-danger mb-3"></i>
                    <h1 class="h3 mb-3">Access Denied</h1>
                    <p class="text-muted mb-4">Direct access to admin routes is not allowed for security reasons.</p>
                    <div class="alert alert-warning">
                        <i class="fas fa-exclamation-triangle me-2"></i>
                        <strong>Security Notice:</strong><br>
                        • Please use the main login page<br>
                        • Contact administrator for credentials<br>
                        • Unauthorized access attempts are logged
                    </div>
                    <a href="login.php" class="btn btn-primary">
                        <i class="fas fa-sign-in-alt me-2"></i>Go to Login
                    </a>
                </div>
            </div>
        </div>
    </div>
</body>
</html>';
    exit;
}

if(isLoggedIn()) {
    redirect(base_url('dashboard.php'));
} else {
    redirect(base_url('login.php'));
}
