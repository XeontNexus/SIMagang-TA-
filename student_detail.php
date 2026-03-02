<?php
require_once 'config/config.php';
requireAdmin();

$db = new Database();

if(!isset($_GET['id'])) {
    redirect(base_url('students.php'));
}

$studentId = $_GET['id'];

// Get student data
$db->query('SELECT * FROM users WHERE id = :id AND role = "student"');
$db->bind(':id', $studentId);
$student = $db->single();

if(!$student) {
    redirect(base_url('students.php'));
}

// Get presensi summary
$db->query('SELECT 
    COUNT(*) as total,
    SUM(CASE WHEN status = "hadir" THEN 1 ELSE 0 END) as hadir,
    SUM(CASE WHEN status = "izin" THEN 1 ELSE 0 END) as izin,
    SUM(CASE WHEN status = "sakit" THEN 1 ELSE 0 END) as sakit,
    SUM(CASE WHEN status = "alpha" THEN 1 ELSE 0 END) as alpha
    FROM presensi WHERE user_id = :user_id');
$db->bind(':user_id', $studentId);
$presensiStats = $db->single();

// Get presensi history
$db->query('SELECT * FROM presensi WHERE user_id = :user_id ORDER BY tanggal DESC LIMIT 30');
$db->bind(':user_id', $studentId);
$presensiHistory = $db->resultSet();

// Get logbook summary
$db->query('SELECT 
    COUNT(*) as total,
    SUM(CASE WHEN status = "draft" THEN 1 ELSE 0 END) as draft,
    SUM(CASE WHEN status = "submitted" THEN 1 ELSE 0 END) as submitted,
    SUM(CASE WHEN status = "approved" THEN 1 ELSE 0 END) as approved,
    SUM(CASE WHEN status = "rejected" THEN 1 ELSE 0 END) as rejected
    FROM logbook WHERE user_id = :user_id');
$db->bind(':user_id', $studentId);
$logbookStats = $db->single();

// Get logbook history
$db->query('SELECT * FROM logbook WHERE user_id = :user_id ORDER BY minggu_ke DESC');
$db->bind(':user_id', $studentId);
$logbookHistory = $db->resultSet();

include 'includes/header.php';
?>

<div class="container-fluid py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3 mb-0 text-gray-800">Detail Siswa</h1>
        <a href="students.php" class="btn btn-secondary">
            <i class="fas fa-arrow-left me-2"></i>Kembali
        </a>
    </div>

    <?php if(isset($_SESSION['success'])): ?>
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                showPopup('<?= addslashes($_SESSION['success']) ?>', 'success');
            });
        </script>
        <?php unset($_SESSION['success']); ?>
    <?php endif; ?>

    <div class="row">
        <div class="col-lg-4">
            <!-- Student Profile Card -->
            <div class="card shadow mb-4">
                <div class="card-body text-center">
                    <div class="user-avatar mx-auto mb-3" style="width: 100px; height: 100px; font-size: 2.5rem;">
                        <?= strtoupper(substr($student['nama_lengkap'], 0, 1)) ?>
                    </div>
                    <h5 class="card-title"><?= htmlspecialchars($student['nama_lengkap']) ?></h5>
                    <p class="card-text text-muted"><?= htmlspecialchars($student['username']) ?></p>
                    <span class="badge bg-<?= $student['status'] === 'active' ? 'success' : ($student['status'] === 'completed' ? 'info' : 'secondary') ?>">
                        <?= ucfirst($student['status']) ?>
                    </span>
                </div>
            </div>
            
            <!-- Contact Info -->
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Informasi Kontak</h6>
                </div>
                <div class="card-body">
                    <ul class="list-group list-group-flush">
                        <li class="list-group-item">
                            <i class="fas fa-envelope me-2 text-primary"></i>
                            <?= htmlspecialchars($student['email']) ?>
                        </li>
                        <li class="list-group-item">
                            <i class="fas fa-phone me-2 text-primary"></i>
                            <?= htmlspecialchars($student['no_hp'] ?: '-') ?>
                        </li>
                        <li class="list-group-item">
                            <i class="fas fa-school me-2 text-primary"></i>
                            <?= htmlspecialchars($student['institusi']) ?>
                        </li>
                        <li class="list-group-item">
                            <i class="fas fa-graduation-cap me-2 text-primary"></i>
                            <?= htmlspecialchars($student['jurusan'] ?: '-') ?>
                        </li>
                    </ul>
                </div>
            </div>
            
            <!-- Internship Period -->
            <div class="card shadow">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Periode Magang</h6>
                </div>
                <div class="card-body">
                    <div class="d-flex justify-content-between mb-2">
                        <span>Mulai:</span>
                        <strong><?= $student['tanggal_mulai'] ? formatTanggalIndo($student['tanggal_mulai']) : '-' ?></strong>
                    </div>
                    <div class="d-flex justify-content-between">
                        <span>Selesai:</span>
                        <strong><?= $student['tanggal_selesai'] ? formatTanggalIndo($student['tanggal_selesai']) : '-' ?></strong>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="col-lg-8">
            <!-- Statistics -->
            <div class="row mb-4">
                <div class="col-md-3 mb-3">
                    <div class="card bg-success text-white">
                        <div class="card-body text-center">
                            <h3><?= $presensiStats['hadir'] ?? 0 ?></h3>
                            <small>Hadir</small>
                        </div>
                    </div>
                </div>
                <div class="col-md-3 mb-3">
                    <div class="card bg-warning text-dark">
                        <div class="card-body text-center">
                            <h3><?= $presensiStats['izin'] ?? 0 ?></h3>
                            <small>Izin</small>
                        </div>
                    </div>
                </div>
                <div class="col-md-3 mb-3">
                    <div class="card bg-danger text-white">
                        <div class="card-body text-center">
                            <h3><?= $presensiStats['sakit'] ?? 0 ?></h3>
                            <small>Sakit</small>
                        </div>
                    </div>
                </div>
                <div class="col-md-3 mb-3">
                    <div class="card bg-info text-white">
                        <div class="card-body text-center">
                            <h3><?= $logbookStats['approved'] ?? 0 ?></h3>
                            <small>Logbook Approved</small>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Presensi History -->
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Riwayat Presensi (30 Terakhir)</h6>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-bordered table-sm">
                            <thead class="table-light">
                                <tr>
                                    <th>Tanggal</th>
                                    <th>Masuk</th>
                                    <th>Keluar</th>
                                    <th>Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach($presensiHistory as $p): ?>
                                <tr>
                                    <td><?= formatTanggalIndo($p['tanggal']) ?></td>
                                    <td><?= $p['jam_masuk'] ?: '-' ?></td>
                                    <td><?= $p['jam_keluar'] ?: '-' ?></td>
                                    <td>
                                        <span class="badge bg-<?= $p['status'] === 'hadir' ? 'success' : ($p['status'] === 'izin' ? 'warning' : ($p['status'] === 'sakit' ? 'danger' : 'secondary')) ?>">
                                            <?= ucfirst($p['status']) ?>
                                        </span>
                                    </td>
                                </tr>
                                <?php endforeach; ?>
                                <?php if(empty($presensiHistory)): ?>
                                <tr>
                                    <td colspan="4" class="text-center text-muted">Belum ada data presensi</td>
                                </tr>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <!-- Logbook History -->
            <div class="card shadow">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Riwayat Logbook</h6>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-bordered table-sm">
                            <thead class="table-light">
                                <tr>
                                    <th>Minggu</th>
                                    <th>Periode</th>
                                    <th>Status</th>
                                    <th>Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach($logbookHistory as $l): ?>
                                <tr>
                                    <td>Minggu <?= $l['minggu_ke'] ?></td>
                                    <td><?= formatTanggalIndo($l['tanggal_mulai']) ?> - <?= formatTanggalIndo($l['tanggal_selesai']) ?></td>
                                    <td>
                                        <span class="badge bg-<?= $l['status'] === 'draft' ? 'secondary' : ($l['status'] === 'submitted' ? 'warning' : ($l['status'] === 'approved' ? 'success' : 'danger')) ?>">
                                            <?= ucfirst($l['status']) ?>
                                        </span>
                                    </td>
                                    <td>
                                        <button type="button" class="btn btn-info btn-sm" data-bs-toggle="modal" data-bs-target="#logbookModal<?= $l['id'] ?>">
                                            <i class="fas fa-eye"></i>
                                        </button>
                                    </td>
                                </tr>
                                <?php endforeach; ?>
                                <?php if(empty($logbookHistory)): ?>
                                <tr>
                                    <td colspan="4" class="text-center text-muted">Belum ada data logbook</td>
                                </tr>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Logbook Detail Modals -->
<?php foreach($logbookHistory as $l): ?>
<div class="modal fade" id="logbookModal<?= $l['id'] ?>" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Detail Logbook - Minggu <?= $l['minggu_ke'] ?></h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div class="mb-3">
                    <h6><strong>Kegiatan:</strong></h6>
                    <p><?= nl2br(htmlspecialchars($l['kegiatan'])) ?></p>
                </div>
                <?php if($l['deskripsi']): ?>
                <div class="mb-3">
                    <h6><strong>Deskripsi:</strong></h6>
                    <p><?= nl2br(htmlspecialchars($l['deskripsi'])) ?></p>
                </div>
                <?php endif; ?>
                <?php if($l['hasil']): ?>
                <div class="mb-3">
                    <h6><strong>Hasil:</strong></h6>
                    <p><?= nl2br(htmlspecialchars($l['hasil'])) ?></p>
                </div>
                <?php endif; ?>
                <?php if($l['kendala']): ?>
                <div class="mb-3">
                    <h6><strong>Kendala:</strong></h6>
                    <p><?= nl2br(htmlspecialchars($l['kendala'])) ?></p>
                </div>
                <?php endif; ?>
                <?php if($l['solusi']): ?>
                <div class="mb-3">
                    <h6><strong>Solusi:</strong></h6>
                    <p><?= nl2br(htmlspecialchars($l['solusi'])) ?></p>
                </div>
                <?php endif; ?>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
            </div>
        </div>
    </div>
</div>
<?php endforeach; ?>

<?php include 'includes/footer.php'; ?>
