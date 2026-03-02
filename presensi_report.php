<?php
require_once 'config/config.php';
requireAdmin();

$db = new Database();

// Get filter parameters
$filterStudent = $_GET['student'] ?? '';
$filterMonth = $_GET['month'] ?? date('Y-m');

// Build query
$query = 'SELECT p.*, u.nama_lengkap, u.institusi FROM presensi p 
          JOIN users u ON p.user_id = u.id 
          WHERE 1=1';
$params = [];

if($filterStudent) {
    $query .= ' AND p.user_id = :student';
    $params[':student'] = $filterStudent;
}

if($filterMonth) {
    $query .= ' AND DATE_FORMAT(p.tanggal, "%Y-%m") = :month';
    $params[':month'] = $filterMonth;
}

$query .= ' ORDER BY p.tanggal DESC, u.nama_lengkap ASC';

$db->query($query);
foreach($params as $key => $val) {
    $db->bind($key, $val);
}
$presensiList = $db->resultSet();

// Get all students for filter
$db->query('SELECT id, nama_lengkap FROM users WHERE role = "student" AND status = "active" ORDER BY nama_lengkap');
$students = $db->resultSet();

include 'includes/header.php';
?>

<div class="container-fluid py-4">
    <h1 class="h3 mb-4 text-gray-800">Laporan Presensi</h1>

    <?php if(isset($_SESSION['success'])): ?>
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                showPopup('<?= addslashes($_SESSION['success']) ?>', 'success');
            });
        </script>
        <?php unset($_SESSION['success']); ?>
    <?php endif; ?>

    <!-- Filter Card -->
    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">
                <i class="fas fa-filter me-2"></i>Filter Laporan
            </h6>
        </div>
        <div class="card-body">
            <form method="GET" action="" class="row">
                <div class="col-md-4 mb-3">
                    <label class="form-label">Siswa</label>
                    <select name="student" class="form-select">
                        <option value="">Semua Siswa</option>
                        <?php foreach($students as $student): ?>
                        <option value="<?= $student['id'] ?>" <?= $filterStudent == $student['id'] ? 'selected' : '' ?>>
                            <?= htmlspecialchars($student['nama_lengkap']) ?>
                        </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="col-md-4 mb-3">
                    <label class="form-label">Bulan</label>
                    <input type="month" name="month" class="form-control" value="<?= $filterMonth ?>">
                </div>
                <div class="col-md-4 mb-3 d-flex align-items-end">
                    <button type="submit" class="btn btn-primary me-2">
                        <i class="fas fa-search me-2"></i>Tampilkan
                    </button>
                    <a href="presensi_report.php" class="btn btn-secondary">Reset</a>
                </div>
            </form>
        </div>
    </div>

    <!-- Summary Statistics -->
    <?php if($presensiList): ?>
    <?php
    $totalHadir = 0;
    $totalIzin = 0;
    $totalSakit = 0;
    $totalAlpha = 0;
    foreach($presensiList as $p) {
        if($p['status'] === 'hadir') $totalHadir++;
        elseif($p['status'] === 'izin') $totalIzin++;
        elseif($p['status'] === 'sakit') $totalSakit++;
        elseif($p['status'] === 'alpha') $totalAlpha++;
    }
    ?>
    <div class="row mb-4">
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-success shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-success text-uppercase mb-1">Hadir</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800"><?= $totalHadir ?></div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-check-circle fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-warning shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">Izin</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800"><?= $totalIzin ?></div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-user-clock fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-danger shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-danger text-uppercase mb-1">Sakit</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800"><?= $totalSakit ?></div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-procedures fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-secondary shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-secondary text-uppercase mb-1">Alpha</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800"><?= $totalAlpha ?></div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-times-circle fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <?php endif; ?>

    <!-- Presensi List -->
    <div class="card shadow">
        <div class="card-header py-3 d-flex justify-content-between align-items-center">
            <h6 class="m-0 font-weight-bold text-primary">
                <i class="fas fa-list me-2"></i>Data Presensi
            </h6>
            <button class="btn btn-success btn-sm" onclick="window.print()">
                <i class="fas fa-print me-2"></i>Cetak
            </button>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered table-hover">
                    <thead class="table-light">
                        <tr>
                            <th>No</th>
                            <th>Nama Siswa</th>
                            <th>Institusi</th>
                            <th>Tanggal</th>
                            <th>Jam Masuk</th>
                            <th>Jam Keluar</th>
                            <th>Status</th>
                            <th>Keterangan</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php $no = 1; foreach($presensiList as $presensi): ?>
                        <tr>
                            <td><?= $no++ ?></td>
                            <td><?= htmlspecialchars($presensi['nama_lengkap']) ?></td>
                            <td><?= htmlspecialchars($presensi['institusi']) ?></td>
                            <td><?= formatTanggalIndo($presensi['tanggal']) ?></td>
                            <td><?= $presensi['jam_masuk'] ?: '-' ?></td>
                            <td><?= $presensi['jam_keluar'] ?: '-' ?></td>
                            <td>
                                <span class="badge bg-<?= $presensi['status'] === 'hadir' ? 'success' : ($presensi['status'] === 'izin' ? 'warning' : ($presensi['status'] === 'sakit' ? 'danger' : 'secondary')) ?>">
                                    <?= ucfirst($presensi['status']) ?>
                                </span>
                            </td>
                            <td><?= htmlspecialchars($presensi['keterangan'] ?: '-') ?></td>
                        </tr>
                        <?php endforeach; ?>
                        <?php if(empty($presensiList)): ?>
                        <tr>
                            <td colspan="8" class="text-center text-muted">
                                <i class="fas fa-inbox fa-3x mb-3"></i><br>
                                Tidak ada data presensi untuk filter yang dipilih
                            </td>
                        </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<?php include 'includes/footer.php'; ?>
