<?php
require_once 'config/config.php';
requireAdmin();

$db = new Database();

// Handle approve action
if($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['action'])) {
    $id = $_POST['id'];
    
    if($_POST['action'] === 'approve') {
        $db->query('UPDATE users SET status = "active" WHERE id = :id AND role = "student"');
        $db->bind(':id', $id);
        
        if($db->execute()) {
            $_SESSION['success'] = 'Pendaftaran siswa berhasil disetujui!';
            redirect(base_url('pending_approvals.php'));
        } else {
            $error = 'Terjadi kesalahan saat menyetujui pendaftaran!';
        }
    } elseif($_POST['action'] === 'reject') {
        $db->query('DELETE FROM users WHERE id = :id AND role = "student" AND status = "pending"');
        $db->bind(':id', $id);
        
        if($db->execute()) {
            $_SESSION['success'] = 'Pendaftaran siswa ditolak dan dihapus!';
            redirect(base_url('pending_approvals.php'));
        } else {
            $error = 'Terjadi kesalahan saat menolak pendaftaran!';
        }
    }
}

// Get pending students
$db->query('SELECT * FROM users WHERE role = "student" AND status = "pending" ORDER BY created_at DESC');
$pendingStudents = $db->resultSet();

// Count total pending
$totalPending = count($pendingStudents);

include 'includes/header.php';
?>

<div class="container-fluid py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3 mb-0 text-gray-800">
            <i class="fas fa-user-clock me-2 text-warning"></i>Persetujuan Pendaftaran
        </h1>
        <a href="students.php" class="btn btn-outline-primary">
            <i class="fas fa-users me-2"></i>Kelola Siswa
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
    
    <?php if(isset($error)): ?>
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                showPopup('<?= addslashes($error) ?>', 'error');
            });
        </script>
    <?php endif; ?>

    <!-- Summary Card -->
    <div class="row mb-4">
        <div class="col-xl-3 col-md-6">
            <div class="card border-left-warning shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">
                                Menunggu Persetujuan
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800"><?= $totalPending ?> Siswa</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-user-clock fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Pending Students List -->
    <div class="card shadow">
        <div class="card-header py-3 d-flex justify-content-between align-items-center">
            <h6 class="m-0 font-weight-bold text-primary">
                <i class="fas fa-list me-2"></i>Daftar Pendaftaran Menunggu
            </h6>
            <?php if($totalPending > 0): ?>
            <span class="badge bg-warning"><?= $totalPending ?> Pending</span>
            <?php endif; ?>
        </div>
        <div class="card-body">
            <?php if(!empty($pendingStudents)): ?>
            <div class="table-responsive">
                <table class="table table-bordered table-hover" id="pendingTable">
                    <thead class="table-light">
                        <tr>
                            <th>No</th>
                            <th>Username</th>
                            <th>Nama Lengkap</th>
                            <th>Email</th>
                            <th>Tanggal Daftar</th>
                            <th width="180">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php $no = 1; foreach($pendingStudents as $student): ?>
                        <tr>
                            <td><?= $no++ ?></td>
                            <td><?= htmlspecialchars($student['username']) ?></td>
                            <td><?= htmlspecialchars($student['nama_lengkap']) ?></td>
                            <td><?= htmlspecialchars($student['email']) ?></td>
                            <td><?= formatTanggalIndo($student['created_at']) ?></td>
                            <td>
                                <form method="POST" action="" class="d-inline" onsubmit="return confirm('Setujui pendaftaran <?= htmlspecialchars($student['nama_lengkap']) ?>?')">
                                    <input type="hidden" name="action" value="approve">
                                    <input type="hidden" name="id" value="<?= $student['id'] ?>">
                                    <button type="submit" class="btn btn-success btn-sm" title="Setujui">
                                        <i class="fas fa-check me-1"></i>Terima
                                    </button>
                                </form>
                                <form method="POST" action="" class="d-inline" onsubmit="return confirm('Tolak dan hapus pendaftaran <?= htmlspecialchars($student['nama_lengkap']) ?>?')">
                                    <input type="hidden" name="action" value="reject">
                                    <input type="hidden" name="id" value="<?= $student['id'] ?>">
                                    <button type="submit" class="btn btn-danger btn-sm" title="Tolak">
                                        <i class="fas fa-times me-1"></i>Tolak
                                    </button>
                                </form>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
            <?php else: ?>
            <div class="text-center py-5">
                <i class="fas fa-check-circle fa-4x text-success mb-3"></i>
                <h5 class="text-muted">Tidak ada pendaftaran yang menunggu persetujuan</h5>
                <p class="text-muted">Semua pendaftaran siswa sudah diproses.</p>
            </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<?php include 'includes/footer.php'; ?>
