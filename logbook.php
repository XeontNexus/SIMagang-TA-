<?php
require_once 'config/config.php';
requireStudent();

$db = new Database();
$user_id = $_SESSION['user_id'];

// Get user data for program kegiatan
$db->query('SELECT institusi FROM users WHERE id = :id');
$db->bind(':id', $user_id);
$userData = $db->single();
$programKegiatan = $userData['institusi'] ?? 'Belum diisi';

// Handle form submission
if($_SERVER['REQUEST_METHOD'] == 'POST') {
    $action = $_POST['action'];
    $uploadDir = 'uploads/logbook/';
    
    // Create upload directory if not exists
    if(!is_dir($uploadDir)) {
        mkdir($uploadDir, 0777, true);
    }
    
    if($action === 'add') {
        $tahun = $_POST['tahun'];
        $bulan = $_POST['bulan'];
        $minggu_ke = $_POST['minggu_ke'];
        $rencana = $_POST['rencana'];
        $hasil = $_POST['hasil'];
        $hambatan = $_POST['hambatan'];
        $perbaikan = $_POST['perbaikan'];
        
        // Handle file upload
        $evidence_file = null;
        $evidence_type = null;
        if(isset($_FILES['evidence']) && $_FILES['evidence']['error'] === 0) {
            $file = $_FILES['evidence'];
            $allowedTypes = ['image/jpeg', 'image/png', 'image/gif', 'application/pdf', 
                           'application/msword', 'application/vnd.openxmlformats-officedocument.wordprocessingml.document'];
            
            if(in_array($file['type'], $allowedTypes)) {
                $fileExt = pathinfo($file['name'], PATHINFO_EXTENSION);
                $fileName = 'logbook_' . $user_id . '_' . time() . '.' . $fileExt;
                $filePath = $uploadDir . $fileName;
                
                if(move_uploaded_file($file['tmp_name'], $filePath)) {
                    $evidence_file = $filePath;
                    $evidence_type = $file['type'];
                }
            }
        }
        
        $db->query('INSERT INTO logbook (user_id, tahun, bulan, minggu_ke, 
                    rencana, hasil, hambatan, perbaikan, program_kegiatan, evidence_file, evidence_type, status) 
                    VALUES (:user_id, :tahun, :bulan, :minggu_ke, 
                    :rencana, :hasil, :hambatan, :perbaikan, :program_kegiatan, :evidence_file, :evidence_type, "draft")');
        $db->bind(':user_id', $user_id);
        $db->bind(':tahun', $tahun);
        $db->bind(':bulan', $bulan);
        $db->bind(':minggu_ke', $minggu_ke);
        $db->bind(':rencana', $rencana);
        $db->bind(':hasil', $hasil);
        $db->bind(':hambatan', $hambatan);
        $db->bind(':perbaikan', $perbaikan);
        $db->bind(':program_kegiatan', $programKegiatan);
        $db->bind(':evidence_file', $evidence_file);
        $db->bind(':evidence_type', $evidence_type);
        
        if($db->execute()) {
            $_SESSION['success'] = 'Logbook berhasil ditambahkan!';
            redirect(base_url('logbook.php'));
        } else {
            $error = 'Terjadi kesalahan saat menyimpan logbook!';
        }
    } elseif($action === 'edit') {
        $id = $_POST['id'];
        $tahun = $_POST['tahun'];
        $bulan = $_POST['bulan'];
        $minggu_ke = $_POST['minggu_ke'];
        $rencana = $_POST['rencana'];
        $hasil = $_POST['hasil'];
        $hambatan = $_POST['hambatan'];
        $perbaikan = $_POST['perbaikan'];
        $hapus_evidence = isset($_POST['hapus_evidence']) ? true : false;
        
        // Get old evidence
        $db->query('SELECT evidence_file FROM logbook WHERE id = :id AND user_id = :user_id');
        $db->bind(':id', $id);
        $db->bind(':user_id', $user_id);
        $oldLogbook = $db->single();
        $evidence_file = $oldLogbook ? $oldLogbook['evidence_file'] : null;
        $evidence_type = null;
        
        // Handle file upload (can update even after submit)
        if(isset($_FILES['evidence']) && $_FILES['evidence']['error'] === 0) {
            $file = $_FILES['evidence'];
            $allowedTypes = ['image/jpeg', 'image/png', 'image/gif', 'application/pdf', 
                           'application/msword', 'application/vnd.openxmlformats-officedocument.wordprocessingml.document'];
            
            if(in_array($file['type'], $allowedTypes)) {
                // Delete old file if exists
                if($evidence_file && file_exists($evidence_file)) {
                    unlink($evidence_file);
                }
                
                $fileExt = pathinfo($file['name'], PATHINFO_EXTENSION);
                $fileName = 'logbook_' . $user_id . '_' . time() . '.' . $fileExt;
                $filePath = $uploadDir . $fileName;
                
                if(move_uploaded_file($file['tmp_name'], $filePath)) {
                    $evidence_file = $filePath;
                    $evidence_type = $file['type'];
                }
            }
        }
        
        // Delete evidence if requested
        if($hapus_evidence && $evidence_file && file_exists($evidence_file)) {
            unlink($evidence_file);
            $evidence_file = null;
            $evidence_type = null;
        }
        
        $db->query('UPDATE logbook SET tahun = :tahun, bulan = :bulan, minggu_ke = :minggu_ke, 
                    rencana = :rencana, hasil = :hasil, hambatan = :hambatan, perbaikan = :perbaikan,
                    evidence_file = :evidence_file, evidence_type = :evidence_type 
                    WHERE id = :id AND user_id = :user_id');
        $db->bind(':id', $id);
        $db->bind(':user_id', $user_id);
        $db->bind(':tahun', $tahun);
        $db->bind(':bulan', $bulan);
        $db->bind(':minggu_ke', $minggu_ke);
        $db->bind(':rencana', $rencana);
        $db->bind(':hasil', $hasil);
        $db->bind(':hambatan', $hambatan);
        $db->bind(':perbaikan', $perbaikan);
        $db->bind(':evidence_file', $evidence_file);
        $db->bind(':evidence_type', $evidence_type);
        
        if($db->execute()) {
            $_SESSION['success'] = 'Logbook berhasil diperbarui!';
            redirect(base_url('logbook.php'));
        } else {
            $error = 'Terjadi kesalahan saat memperbarui logbook!';
        }
    } elseif($action === 'submit') {
        $id = $_POST['id'];
        
        $db->query('UPDATE logbook SET status = "submitted" WHERE id = :id AND user_id = :user_id AND status = "draft"');
        $db->bind(':id', $id);
        $db->bind(':user_id', $user_id);
        
        if($db->execute()) {
            $_SESSION['success'] = 'Logbook berhasil disubmit untuk review!';
            redirect(base_url('logbook.php'));
        } else {
            $error = 'Terjadi kesalahan saat submit logbook!';
        }
    } elseif($action === 'delete') {
        $id = $_POST['id'];
        
        // Get evidence file before delete
        $db->query('SELECT evidence_file FROM logbook WHERE id = :id AND user_id = :user_id AND status = "draft"');
        $db->bind(':id', $id);
        $db->bind(':user_id', $user_id);
        $logbookData = $db->single();
        
        if($logbookData && $logbookData['evidence_file'] && file_exists($logbookData['evidence_file'])) {
            unlink($logbookData['evidence_file']);
        }
        
        $db->query('DELETE FROM logbook WHERE id = :id AND user_id = :user_id AND status = "draft"');
        $db->bind(':id', $id);
        $db->bind(':user_id', $user_id);
        
        if($db->execute()) {
            $_SESSION['success'] = 'Logbook berhasil dihapus!';
            redirect(base_url('logbook.php'));
        } else {
            $error = 'Terjadi kesalahan saat menghapus logbook!';
        }
    }
}

// Get all logbooks for this student
$db->query('SELECT * FROM logbook WHERE user_id = :user_id ORDER BY tahun DESC, bulan DESC, minggu_ke DESC');
$db->bind(':user_id', $user_id);
$logbooks = $db->resultSet();

// Get edit data if editing (allow edit even after submit for evidence upload)
$editLogbook = null;
if(isset($_GET['edit'])) {
    $db->query('SELECT * FROM logbook WHERE id = :id AND user_id = :user_id');
    $db->bind(':id', $_GET['edit']);
    $db->bind(':user_id', $user_id);
    $editLogbook = $db->single();
}

// Generate tahun options (current year and 1 year back/ahead)
$currentYear = date('Y');
$tahunOptions = range($currentYear - 1, $currentYear + 1);

// Generate bulan options
$bulanOptions = [
    1 => 'Januari', 2 => 'Februari', 3 => 'Maret', 4 => 'April',
    5 => 'Mei', 6 => 'Juni', 7 => 'Juli', 8 => 'Agustus',
    9 => 'September', 10 => 'Oktober', 11 => 'November', 12 => 'Desember'
];

include 'includes/header.php';
?>

<div class="container-fluid py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3 mb-0 text-gray-800">Logbook Mingguan</h1>
        <?php if(!isset($_GET['add']) && !$editLogbook): ?>
        <a href="?add=1" class="btn btn-primary">
            <i class="fas fa-plus me-2"></i>Tambah Logbook
        </a>
        <?php endif; ?>
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

    <!-- Add/Edit Form -->
    <?php if(isset($_GET['add']) || $editLogbook): ?>
    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">
                <i class="fas fa-edit me-2"></i><?= $editLogbook ? 'Edit' : 'Tambah' ?> Logbook
            </h6>
        </div>
        <div class="card-body">
            <form method="POST" action="" enctype="multipart/form-data">
                <input type="hidden" name="action" value="<?= $editLogbook ? 'edit' : 'add' ?>">
                <?php if($editLogbook): ?>
                <input type="hidden" name="id" value="<?= $editLogbook['id'] ?>">
                <?php endif; ?>
                
                <!-- Tahun, Bulan, Minggu -->
                <div class="row mb-3">
                    <div class="col-md-4">
                        <label class="form-label">Tahun</label>
                        <select name="tahun" class="form-select" required>
                            <?php foreach($tahunOptions as $thn): ?>
                            <option value="<?= $thn ?>" <?= ($editLogbook && $editLogbook['tahun'] == $thn) || (!$editLogbook && $currentYear == $thn) ? 'selected' : '' ?>>
                                <?= $thn ?>
                            </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">Bulan</label>
                        <select name="bulan" class="form-select" required>
                            <?php foreach($bulanOptions as $key => $val): ?>
                            <option value="<?= $key ?>" <?= ($editLogbook && $editLogbook['bulan'] == $key) || (!$editLogbook && date('n') == $key) ? 'selected' : '' ?>>
                                <?= $val ?>
                            </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">Pekan (Minggu ke-)</label>
                        <select name="minggu_ke" class="form-select" required>
                            <option value="">Pilih Minggu</option>
                            <?php for($i = 1; $i <= 5; $i++): ?>
                            <option value="<?= $i ?>" <?= ($editLogbook && $editLogbook['minggu_ke'] == $i) ? 'selected' : '' ?>>
                                Minggu <?= $i ?>
                            </option>
                            <?php endfor; ?>
                        </select>
                        <small class="text-muted">Maksimal 5 minggu per bulan</small>
                    </div>
                </div>
                
                <!-- Program Kegiatan (Read-only dari Institusi) -->
                <div class="mb-3">
                    <label class="form-label">Program Kegiatan (Tempat Magang)</label>
                    <input type="text" class="form-control" value="<?= htmlspecialchars($programKegiatan) ?>" readonly style="background-color: #e9ecef;">
                    <small class="text-muted">Data diambil dari informasi institusi pada data diri Anda</small>
                </div>
                
                <!-- Rencana -->
                <div class="mb-3">
                    <label class="form-label">Rencana Kegiatan</label>
                    <textarea name="rencana" class="form-control" rows="3" required 
                              placeholder="Jelaskan rencana kegiatan yang akan dilakukan minggu ini..."><?= $editLogbook ? htmlspecialchars($editLogbook['rencana']) : '' ?></textarea>
                </div>
                
                <!-- Hasil Kegiatan -->
                <div class="mb-3">
                    <label class="form-label">Hasil Kegiatan</label>
                    <textarea name="hasil" class="form-control" rows="3" required 
                              placeholder="Jelaskan hasil dari kegiatan yang dilakukan..."><?= $editLogbook ? htmlspecialchars($editLogbook['hasil']) : '' ?></textarea>
                </div>
                
                <!-- Hambatan -->
                <div class="mb-3">
                    <label class="form-label">Hambatan/Kendala</label>
                    <textarea name="hambatan" class="form-control" rows="3" 
                              placeholder="Apa hambatan atau kendala yang dihadapi..."><?= $editLogbook ? htmlspecialchars($editLogbook['hambatan']) : '' ?></textarea>
                </div>
                
                <!-- Perbaikan -->
                <div class="mb-3">
                    <label class="form-label">Perbaikan/Solusi</label>
                    <textarea name="perbaikan" class="form-control" rows="3" 
                              placeholder="Bagaimana perbaikan atau solusi yang diterapkan..."><?= $editLogbook ? htmlspecialchars($editLogbook['perbaikan']) : '' ?></textarea>
                </div>
                
                <!-- Upload Evidence -->
                <div class="mb-3">
                    <label class="form-label">Unggah Evidence (Opsional)</label>
                    <input type="file" name="evidence" class="form-control" accept=".jpg,.jpeg,.png,.pdf,.doc,.docx">
                    <small class="text-muted">
                        Format yang diizinkan: JPG, PNG, PDF, Word (DOC/DOCX). Maksimal 5MB.
                        <?php if($editLogbook && $editLogbook['evidence_file']): ?>
                        <br><strong>File saat ini:</strong> 
                        <a href="<?= base_url($editLogbook['evidence_file']) ?>" target="_blank" class="text-primary">
                            <i class="fas fa-file"></i> Lihat File
                        </a>
                        <?php endif; ?>
                    </small>
                </div>
                
                <?php if($editLogbook && $editLogbook['evidence_file']): ?>
                <div class="mb-3 form-check">
                    <input type="checkbox" name="hapus_evidence" class="form-check-input" id="hapus_evidence">
                    <label class="form-check-label" for="hapus_evidence">Hapus file evidence lama</label>
                </div>
                <?php endif; ?>
                
                <div class="d-flex gap-2">
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-paper-plane me-2"></i>Submit Langsung
                    </button>
                    <a href="logbook.php" class="btn btn-secondary">Batal</a>
                </div>
            </form>
        </div>
    </div>
    <?php endif; ?>

    <!-- Logbook List -->
    <div class="card shadow">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">
                <i class="fas fa-list me-2"></i>Daftar Logbook
            </h6>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered table-hover">
                    <thead class="table-light">
                        <tr>
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
                        <?php foreach($logbooks as $logbook): ?>
                        <tr>
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
                                    <i class="fas fa-eye"></i>
                                </button>
                                
                                <?php if($logbook['status'] === 'draft' || $logbook['status'] === 'submitted'): ?>
                                <a href="?edit=<?= $logbook['id'] ?>" class="btn btn-warning btn-sm">
                                    <i class="fas fa-edit"></i>
                                </a>
                                <?php endif; ?>
                                
                                <?php if($logbook['status'] === 'draft'): ?>
                                <form method="POST" action="" class="d-inline" onsubmit="return confirm('Yakin ingin menghapus logbook ini?')">
                                    <input type="hidden" name="action" value="delete">
                                    <input type="hidden" name="id" value="<?= $logbook['id'] ?>">
                                    <button type="submit" class="btn btn-danger btn-sm">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </form>
                                
                                <?php if($logbook['status'] === 'draft'): ?>
                                <form method="POST" action="" class="d-inline" onsubmit="return confirm('Yakin ingin submit logbook ini?')">
                                    <input type="hidden" name="action" value="submit">
                                    <input type="hidden" name="id" value="<?= $logbook['id'] ?>">
                                    <button type="submit" class="btn btn-success btn-sm">
                                        <i class="fas fa-paper-plane"></i> Submit
                                    </button>
                                </form>
                                <?php endif; ?>
                                <?php endif; ?>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                        <?php if(empty($logbooks)): ?>
                        <tr>
                            <td colspan="7" class="text-center text-muted">
                                <i class="fas fa-inbox fa-3x mb-3"></i><br>
                                Belum ada logbook. Klik "Tambah Logbook" untuk membuat.
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
<?php foreach($logbooks as $logbook): ?>
<div class="modal fade" id="detailModal<?= $logbook['id'] ?>" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Detail Logbook - <?= $bulanOptions[$logbook['bulan']] ?> <?= $logbook['tahun'] ?> (Minggu <?= $logbook['minggu_ke'] ?>)</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div class="row mb-3">
                    <div class="col-md-6">
                        <strong>Tahun/Bulan:</strong> <?= $logbook['tahun'] ?> / <?= $bulanOptions[$logbook['bulan']] ?>
                    </div>
                    <div class="col-md-6">
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
                    <h6><strong>Catatan Admin:</strong></h6>
                    <p><?= nl2br(htmlspecialchars($logbook['catatan_admin'])) ?></p>
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
