<?php
require_once 'config/config.php';
requireAdmin();

$db = new Database();

// Get filter parameters
$filterStudent = $_GET['student'] ?? '';
$filterStatus = $_GET['status'] ?? '';
$filterTahun = $_GET['tahun'] ?? date('Y');
$filterBulan = $_GET['bulan'] ?? '';

// Build query
$query = 'SELECT l.*, u.nama_lengkap, u.institusi FROM logbook l 
          JOIN users u ON l.user_id = u.id 
          WHERE l.tahun = :tahun';
$params = [':tahun' => $filterTahun];

if($filterStudent) {
    $query .= ' AND l.user_id = :student';
    $params[':student'] = $filterStudent;
}

if($filterStatus) {
    $query .= ' AND l.status = :status';
    $params[':status'] = $filterStatus;
}

if($filterBulan) {
    $query .= ' AND l.bulan = :bulan';
    $params[':bulan'] = $filterBulan;
}

$query .= ' ORDER BY l.tahun DESC, l.bulan DESC, l.minggu_ke DESC';

$db->query($query);
foreach($params as $key => $val) {
    $db->bind($key, $val);
}
$logbookList = $db->resultSet();

// Handle approval/rejection
if($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['action'])) {
    $action = $_POST['action'];
    $id = $_POST['id'];
    $catatan = $_POST['catatan'] ?? '';
    
    if($action === 'approve') {
        $db->query('UPDATE logbook SET status = "approved", catatan_admin = :catatan WHERE id = :id');
        $db->bind(':catatan', $catatan);
        $db->bind(':id', $id);
        $db->execute();
        $_SESSION['success'] = 'Logbook berhasil di-approve!';
    } elseif($action === 'reject') {
        $db->query('UPDATE logbook SET status = "rejected", catatan_admin = :catatan WHERE id = :id');
        $db->bind(':catatan', $catatan);
        $db->bind(':id', $id);
        $db->execute();
        $_SESSION['success'] = 'Logbook berhasil di-reject!';
    }
    redirect(base_url('logbook_report.php'));
}

// Get all students for filter
$db->query('SELECT id, nama_lengkap FROM users WHERE role = "student" ORDER BY nama_lengkap');
$students = $db->resultSet();

// Bulan options
$bulanOptions = [
    1 => 'Januari', 2 => 'Februari', 3 => 'Maret', 4 => 'April',
    5 => 'Mei', 6 => 'Juni', 7 => 'Juli', 8 => 'Agustus',
    9 => 'September', 10 => 'Oktober', 11 => 'November', 12 => 'Desember'
];

// Generate tahun options
$currentYear = date('Y');
$tahunOptions = range($currentYear - 1, $currentYear + 1);

include 'includes/header.php';
?>

<div class="container-fluid py-4">
    <h1 class="h3 mb-4 text-gray-800">Laporan Logbook Mingguan</h1>

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
                <div class="col-md-2 mb-3">
                    <label class="form-label">Tahun</label>
                    <select name="tahun" class="form-select">
                        <?php foreach($tahunOptions as $thn): ?>
                        <option value="<?= $thn ?>" <?= $filterTahun == $thn ? 'selected' : '' ?>><?= $thn ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="col-md-2 mb-3">
                    <label class="form-label">Bulan</label>
                    <select name="bulan" class="form-select">
                        <option value="">Semua Bulan</option>
                        <?php foreach($bulanOptions as $key => $val): ?>
                        <option value="<?= $key ?>" <?= $filterBulan == $key ? 'selected' : '' ?>><?= $val ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="col-md-3 mb-3">
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
                <div class="col-md-3 mb-3">
                    <label class="form-label">Status</label>
                    <select name="status" class="form-select">
                        <option value="">Semua Status</option>
                        <option value="draft" <?= $filterStatus === 'draft' ? 'selected' : '' ?>>Draft</option>
                        <option value="submitted" <?= $filterStatus === 'submitted' ? 'selected' : '' ?>>Submitted</option>
                        <option value="approved" <?= $filterStatus === 'approved' ? 'selected' : '' ?>>Approved</option>
                        <option value="rejected" <?= $filterStatus === 'rejected' ? 'selected' : '' ?>>Rejected</option>
                    </select>
                </div>
                <div class="col-md-2 mb-3 d-flex align-items-end">
                    <button type="submit" class="btn btn-primary me-2">
                        <i class="fas fa-search me-2"></i>Tampilkan
                    </button>
                    <a href="logbook_report.php" class="btn btn-secondary">Reset</a>
                </div>
            </form>
        </div>
    </div>

    <!-- Logbook List -->
    <div class="card shadow">
        <div class="card-header py-3 d-flex justify-content-between align-items-center">
            <h6 class="m-0 font-weight-bold text-primary">
                <i class="fas fa-book me-2"></i>Data Logbook
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
                            <th>Tahun</th>
                            <th>Bulan</th>
                            <th>Pekan</th>
                            <th>Program</th>
                            <th>Evidence</th>
                            <th>Status</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php $no = 1; foreach($logbookList as $logbook): ?>
                        <tr>
                            <td><?= $no++ ?></td>
                            <td><?= htmlspecialchars($logbook['nama_lengkap']) ?></td>
                            <td><?= $logbook['tahun'] ?></td>
                            <td><?= $bulanOptions[$logbook['bulan']] ?></td>
                            <td>Minggu <?= $logbook['minggu_ke'] ?></td>
                            <td><?= htmlspecialchars($logbook['program_kegiatan'] ?: '-') ?></td>
                            <td>
                                <?php if($logbook['evidence_file']): ?>
                                <a href="<?= base_url($logbook['evidence_file']) ?>" target="_blank" class="btn btn-sm btn-info">
                                    <i class="fas fa-file"></i> Lihat
                                </a>
                                <?php else: ?>
                                <span class="text-muted">-</span>
                                <?php endif; ?>
                            </td>
                            <td>
                                <span class="badge bg-<?= $logbook['status'] === 'draft' ? 'secondary' : ($logbook['status'] === 'submitted' ? 'warning' : ($logbook['status'] === 'approved' ? 'success' : 'danger')) ?>">
                                    <?= ucfirst($logbook['status']) ?>
                                </span>
                            </td>
                            <td>
                                <button type="button" class="btn btn-info btn-sm" data-bs-toggle="modal" data-bs-target="#detailModal<?= $logbook['id'] ?>">
                                    <i class="fas fa-eye"></i> Detail
                                </button>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                        <?php if(empty($logbookList)): ?>
                        <tr>
                            <td colspan="9" class="text-center text-muted">
                                <i class="fas fa-inbox fa-3x mb-3"></i><br>
                                Tidak ada data logbook untuk filter yang dipilih
                            </td>
                        </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<!-- Detail Modals -->
<?php foreach($logbookList as $logbook): ?>
<div class="modal fade" id="detailModal<?= $logbook['id'] ?>" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Detail Logbook - <?= htmlspecialchars($logbook['nama_lengkap']) ?></h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div class="row mb-3">
                    <div class="col-md-4">
                        <strong>Tahun/Bulan:</strong> <?= $logbook['tahun'] ?> / <?= $bulanOptions[$logbook['bulan']] ?>
                    </div>
                    <div class="col-md-4">
                        <strong>Pekan:</strong> Minggu <?= $logbook['minggu_ke'] ?>
                    </div>
                    <div class="col-md-4">
                        <strong>Status:</strong> 
                        <span class="badge bg-<?= $logbook['status'] === 'draft' ? 'secondary' : ($logbook['status'] === 'submitted' ? 'warning' : ($logbook['status'] === 'approved' ? 'success' : 'danger')) ?>">
                            <?= ucfirst($logbook['status']) ?>
                        </span>
                    </div>
                </div>
                
                <div class="mb-3">
                    <h6><strong>Program Kegiatan:</strong></h6>
                    <p><?= htmlspecialchars($logbook['program_kegiatan'] ?: '-') ?></p>
                </div>
                
                <div class="mb-3">
                    <h6><strong>Rencana:</strong></h6>
                    <p><?= nl2br(htmlspecialchars($logbook['rencana'] ?: '-')) ?></p>
                </div>
                
                <div class="mb-3">
                    <h6><strong>Hasil Kegiatan:</strong></h6>
                    <p><?= nl2br(htmlspecialchars($logbook['hasil'] ?: '-')) ?></p>
                </div>
                
                <div class="mb-3">
                    <h6><strong>Hambatan:</strong></h6>
                    <p><?= nl2br(htmlspecialchars($logbook['hambatan'] ?: '-')) ?></p>
                </div>
                
                <div class="mb-3">
                    <h6><strong>Perbaikan:</strong></h6>
                    <p><?= nl2br(htmlspecialchars($logbook['perbaikan'] ?: '-')) ?></p>
                </div>
                
                <?php if($logbook['evidence_file']): ?>
                <div class="mb-3">
                    <h6><strong>Evidence:</strong></h6>
                    <a href="<?= base_url($logbook['evidence_file']) ?>" target="_blank" class="btn btn-info">
                        <i class="fas fa-file"></i> Lihat File Evidence
                    </a>
                </div>
                <?php endif; ?>
                
                <?php if($logbook['catatan_admin']): ?>
                <div class="alert alert-info">
                    <h6><strong>Catatan Admin Sebelumnya:</strong></h6>
                    <p><?= nl2br(htmlspecialchars($logbook['catatan_admin'])) ?></p>
                </div>
                <?php endif; ?>
                
                <?php if($logbook['status'] === 'submitted'): ?>
                <hr>
                <form method="POST" action="">
                    <input type="hidden" name="id" value="<?= $logbook['id'] ?>">
                    <div class="mb-3">
                        <label class="form-label">Catatan Admin</label>
                        <textarea name="catatan" class="form-control" rows="3" placeholder="Berikan catatan untuk siswa..."></textarea>
                    </div>
                    <div class="d-flex gap-2">
                        <button type="submit" name="action" value="approve" class="btn btn-success">
                            <i class="fas fa-check me-2"></i>Approve
                        </button>
                        <button type="submit" name="action" value="reject" class="btn btn-danger">
                            <i class="fas fa-times me-2"></i>Reject
                        </button>
                    </div>
                </form>
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
