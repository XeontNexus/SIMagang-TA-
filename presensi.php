<?php
require_once 'config/config.php';
requireStudent();

$db = new Database();
$user_id = $_SESSION['user_id'];
$tanggal = date('Y-m-d');
$waktu = date('H:i:s');

// Check today's presensi
$db->query('SELECT * FROM presensi WHERE user_id = :user_id AND tanggal = :tanggal');
$db->bind(':user_id', $user_id);
$db->bind(':tanggal', $tanggal);
$todayPresensi = $db->single();

// Handle presensi submission
if($_SERVER['REQUEST_METHOD'] == 'POST') {
    $action = $_POST['action'];
    $status = $_POST['status'] ?? 'hadir';
    $keterangan = $_POST['keterangan'] ?? '';
    
    if($action === 'masuk') {
        // Check if already checked in
        if($todayPresensi && $todayPresensi['jam_masuk']) {
            $error = 'Anda sudah melakukan presensi masuk hari ini!';
        } else {
            // Insert presensi masuk
            $db->query('INSERT INTO presensi (user_id, tanggal, jam_masuk, status, keterangan) VALUES (:user_id, :tanggal, :jam_masuk, :status, :keterangan)');
            $db->bind(':user_id', $user_id);
            $db->bind(':tanggal', $tanggal);
            $db->bind(':jam_masuk', $waktu);
            $db->bind(':status', $status);
            $db->bind(':keterangan', $keterangan);
            
            if($db->execute()) {
                $_SESSION['success'] = 'Presensi masuk berhasil dicatat pada jam ' . date('H:i') . '!';
                redirect(base_url('presensi.php'));
            } else {
                $error = 'Terjadi kesalahan saat menyimpan presensi!';
            }
        }
    } elseif($action === 'keluar') {
        // Check if already checked in
        if(!$todayPresensi || !$todayPresensi['jam_masuk']) {
            $error = 'Anda belum melakukan presensi masuk hari ini!';
        } elseif($todayPresensi['jam_keluar']) {
            $error = 'Anda sudah melakukan presensi keluar hari ini!';
        } else {
            // Update presensi keluar
            $db->query('UPDATE presensi SET jam_keluar = :jam_keluar WHERE id = :id');
            $db->bind(':jam_keluar', $waktu);
            $db->bind(':id', $todayPresensi['id']);
            
            if($db->execute()) {
                $_SESSION['success'] = 'Presensi keluar berhasil dicatat pada jam ' . date('H:i') . '!';
                redirect(base_url('presensi.php'));
            } else {
                $error = 'Terjadi kesalahan saat menyimpan presensi!';
            }
        }
    }
}

// Get presensi history
$db->query('SELECT * FROM presensi WHERE user_id = :user_id ORDER BY tanggal DESC LIMIT 30');
$db->bind(':user_id', $user_id);
$presensiHistory = $db->resultSet();

include 'includes/header.php';
?>

<style>
    /* Sticky header */
    .card-header.sticky-top {
        position: sticky;
        top: 0;
        z-index: 100;
        background: white;
    }
    
    /* Button spacing */
    .presensi-btn-group {
        display: flex;
        gap: 1rem;
        margin-top: 1.5rem;
    }
    
    .presensi-btn-group .btn {
        flex: 1;
        padding: 1rem;
        font-size: 1.1rem;
        min-height: 60px;
    }
    
    /* Status cards */
    .status-card {
        border: none;
        box-shadow: 0 2px 8px rgba(0,0,0,0.1);
        transition: transform 0.2s;
    }
    
    .status-card:hover {
        transform: translateY(-2px);
    }
    
    .status-card .card-body {
        padding: 1.5rem;
    }
    
    .status-badge {
        font-size: 1.25rem;
        padding: 0.5rem 1rem;
    }
</style>

<div class="container-fluid py-4">
    <h1 class="h3 mb-4 text-gray-800">Presensi Harian</h1>

    <?php if(isset($_SESSION['success'])): ?>
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                showPopup('<?= addslashes($_SESSION['success']) ?>', 'success');
            });
        </script>
        <?php unset($_SESSION['success']); ?>
    <?php endif; ?>
    
    <?php if(isset($error)): ?>
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                showPopup('<?= addslashes($error) ?>', 'error');
            });
        </script>
    <?php endif; ?>

    <!-- Presensi Card -->
    <div class="row">
        <div class="col-lg-8">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">
                        <i class="fas fa-clock me-2"></i>Presensi Hari Ini - <?= formatTanggalIndo($tanggal) ?>
                    </h6>
                </div>
                <div class="card-body">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <div class="card bg-light status-card">
                                <div class="card-body text-center">
                                    <h5 class="card-title">
                                        <i class="fas fa-sign-in-alt text-success me-2"></i>Presensi Masuk
                                    </h5>
                                    <p class="card-text">
                                        <?php if($todayPresensi && $todayPresensi['jam_masuk']): ?>
                                            <span class="badge bg-success status-badge"><?= $todayPresensi['jam_masuk'] ?></span>
                                        <?php else: ?>
                                            <span class="text-muted">Belum presensi</span>
                                        <?php endif; ?>
                                    </p>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="card bg-light status-card">
                                <div class="card-body text-center">
                                    <h5 class="card-title">
                                        <i class="fas fa-sign-out-alt text-info me-2"></i>Presensi Keluar
                                    </h5>
                                    <p class="card-text">
                                        <?php if($todayPresensi && $todayPresensi['jam_keluar']): ?>
                                            <span class="badge bg-info status-badge"><?= $todayPresensi['jam_keluar'] ?></span>
                                        <?php else: ?>
                                            <span class="text-muted">Belum presensi</span>
                                        <?php endif; ?>
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <hr class="my-4">

                    <!-- Presensi Buttons -->
                    <div class="presensi-btn-group">
                        <div class="col-md-6">
                            <?php if(!$todayPresensi || !$todayPresensi['jam_masuk']): ?>
                                <button type="button" class="btn btn-success btn-lg w-100" data-bs-toggle="modal" data-bs-target="#presensiMasukModal">
                                    <i class="fas fa-sign-in-alt me-2"></i>Presensi Masuk
                                </button>
                            <?php else: ?>
                                <button class="btn btn-success btn-lg w-100" disabled>
                                    <i class="fas fa-check me-2"></i>Sudah Presensi Masuk
                                </button>
                            <?php endif; ?>
                        </div>
                        <div class="col-md-6">
                            <?php if($todayPresensi && $todayPresensi['jam_masuk'] && !$todayPresensi['jam_keluar']): ?>
                                <button type="button" class="btn btn-info btn-lg w-100 text-white" data-bs-toggle="modal" data-bs-target="#presensiKeluarModal">
                                    <i class="fas fa-sign-out-alt me-2"></i>Presensi Keluar
                                </button>
                            <?php elseif($todayPresensi && $todayPresensi['jam_keluar']): ?>
                                <button class="btn btn-info btn-lg w-100 text-white" disabled>
                                    <i class="fas fa-check me-2"></i>Sudah Presensi Keluar
                                </button>
                            <?php else: ?>
                                <button class="btn btn-info btn-lg w-100 text-white" disabled>
                                    <i class="fas fa-sign-out-alt me-2"></i>Presensi Keluar
                                </button>
                                <small class="text-muted d-block mt-2 text-center">Silakan presensi masuk terlebih dahulu</small>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Statistics -->
        <div class="col-lg-4">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">
                        <i class="fas fa-chart-pie me-2"></i>Statistik Bulan Ini
                    </h6>
                </div>
                <div class="card-body">
                    <?php
                    $currentMonth = date('Y-m');
                    $db->query('SELECT 
                        COUNT(*) as total,
                        SUM(CASE WHEN status = "hadir" THEN 1 ELSE 0 END) as hadir,
                        SUM(CASE WHEN status = "izin" THEN 1 ELSE 0 END) as izin,
                        SUM(CASE WHEN status = "sakit" THEN 1 ELSE 0 END) as sakit,
                        SUM(CASE WHEN status = "alpha" THEN 1 ELSE 0 END) as alpha
                        FROM presensi 
                        WHERE user_id = :user_id AND DATE_FORMAT(tanggal, "%Y-%m") = :month');
                    $db->bind(':user_id', $user_id);
                    $db->bind(':month', $currentMonth);
                    $stats = $db->single();
                    ?>
                    <div class="d-flex justify-content-between mb-2">
                        <span>Hadir:</span>
                        <span class="badge bg-success"><?= $stats['hadir'] ?? 0 ?></span>
                    </div>
                    <div class="d-flex justify-content-between mb-2">
                        <span>Izin:</span>
                        <span class="badge bg-warning"><?= $stats['izin'] ?? 0 ?></span>
                    </div>
                    <div class="d-flex justify-content-between mb-2">
                        <span>Sakit:</span>
                        <span class="badge bg-danger"><?= $stats['sakit'] ?? 0 ?></span>
                    </div>
                    <div class="d-flex justify-content-between">
                        <span>Alpha:</span>
                        <span class="badge bg-secondary"><?= $stats['alpha'] ?? 0 ?></span>
                    </div>
                    <hr>
                    <div class="d-flex justify-content-between">
                        <strong>Total:</strong>
                        <strong><?= $stats['total'] ?></strong>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Presensi History -->
    <div class="card shadow">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">
                <i class="fas fa-history me-2"></i>Riwayat Presensi (30 Hari Terakhir)
            </h6>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered table-hover">
                    <thead class="table-light">
                        <tr>
                            <th>Tanggal</th>
                            <th>Jam Masuk</th>
                            <th>Jam Keluar</th>
                            <th>Status</th>
                            <th>Keterangan</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach($presensiHistory as $presensi): ?>
                        <tr>
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
                        <?php if(empty($presensiHistory)): ?>
                        <tr>
                            <td colspan="5" class="text-center text-muted">Belum ada data presensi</td>
                        </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<!-- Presensi Masuk Modal -->
<div class="modal fade" id="presensiMasukModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <form method="POST" action="">
                <div class="modal-header">
                    <h5 class="modal-title"><i class="fas fa-sign-in-alt text-success me-2"></i>Presensi Masuk</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <input type="hidden" name="action" value="masuk">
                    
                    <div class="mb-3">
                        <label class="form-label">Status Kehadiran</label>
                        <select name="status" class="form-select" required>
                            <option value="hadir">Hadir</option>
                            <option value="izin">Izin</option>
                            <option value="sakit">Sakit</option>
                        </select>
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label">Keterangan (Opsional)</label>
                        <textarea name="keterangan" class="form-control" rows="2" placeholder="Tambahkan keterangan jika diperlukan..."></textarea>
                    </div>
                    
                    <div class="alert alert-info">
                        <i class="fas fa-info-circle me-2"></i>
                        Waktu saat ini: <strong><?= date('H:i:s') ?></strong>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-success">
                        <i class="fas fa-check me-2"></i>Konfirmasi Presensi
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Presensi Keluar Modal -->
<div class="modal fade" id="presensiKeluarModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <form method="POST" action="">
                <div class="modal-header">
                    <h5 class="modal-title"><i class="fas fa-sign-out-alt text-info me-2"></i>Presensi Keluar</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <input type="hidden" name="action" value="keluar">
                    
                    <div class="alert alert-warning">
                        <i class="fas fa-exclamation-triangle me-2"></i>
                        Pastikan Anda telah menyelesaikan seluruh kegiatan magang hari ini.
                    </div>
                    
                    <div class="alert alert-info">
                        <i class="fas fa-info-circle me-2"></i>
                        Waktu saat ini: <strong><?= date('H:i:s') ?></strong>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-info text-white">
                        <i class="fas fa-check me-2"></i>Konfirmasi Presensi Keluar
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<?php include 'includes/footer.php'; ?>
