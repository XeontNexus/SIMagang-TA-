<?php
require_once 'config/config.php';
requireAdmin();

$db = new Database();

// Handle form submissions
if($_SERVER['REQUEST_METHOD'] == 'POST') {
    $action = $_POST['action'];
    
    if($action === 'add') {
        $nama_kelas = trim($_POST['nama_kelas']);
        $keterangan = trim($_POST['keterangan']);
        
        if(empty($nama_kelas)) {
            $error = 'Nama kelas harus diisi!';
        } else {
            $db->query('INSERT INTO kelas (nama_kelas, keterangan) VALUES (:nama_kelas, :keterangan)');
            $db->bind(':nama_kelas', $nama_kelas);
            $db->bind(':keterangan', $keterangan);
            
            if($db->execute()) {
                $_SESSION['success'] = 'Kelas berhasil ditambahkan!';
                redirect(base_url('kelas.php'));
            } else {
                $error = 'Terjadi kesalahan saat menambahkan kelas!';
            }
        }
    } elseif($action === 'edit') {
        $id = $_POST['id'];
        $nama_kelas = trim($_POST['nama_kelas']);
        $keterangan = trim($_POST['keterangan']);
        
        if(empty($nama_kelas)) {
            $error = 'Nama kelas harus diisi!';
        } else {
            $db->query('UPDATE kelas SET nama_kelas = :nama_kelas, keterangan = :keterangan WHERE id = :id');
            $db->bind(':id', $id);
            $db->bind(':nama_kelas', $nama_kelas);
            $db->bind(':keterangan', $keterangan);
            
            if($db->execute()) {
                $_SESSION['success'] = 'Kelas berhasil diperbarui!';
                redirect(base_url('kelas.php'));
            } else {
                $error = 'Terjadi kesalahan saat memperbarui kelas!';
            }
        }
    } elseif($action === 'delete') {
        $id = $_POST['id'];
        
        $db->query('DELETE FROM kelas WHERE id = :id');
        $db->bind(':id', $id);
        
        if($db->execute()) {
            $_SESSION['success'] = 'Kelas berhasil dihapus!';
            redirect(base_url('kelas.php'));
        } else {
            $error = 'Terjadi kesalahan saat menghapus kelas!';
        }
    }
}

// Get all kelas
$db->query('SELECT * FROM kelas ORDER BY nama_kelas ASC');
$kelasList = $db->resultSet();

// Get edit data
$editKelas = null;
if(isset($_GET['edit'])) {
    $db->query('SELECT * FROM kelas WHERE id = :id');
    $db->bind(':id', $_GET['edit']);
    $editKelas = $db->single();
}

include 'includes/header.php';
?>

<div class="container-fluid py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3 mb-0 text-gray-800">
            <i class="fas fa-school me-2"></i>Kelola Kelas
        </h1>
        <?php if(!isset($_GET['add']) && !$editKelas): ?>
        <a href="?add=1" class="btn btn-primary">
            <i class="fas fa-plus me-2"></i>Tambah Kelas
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
    
    <?php if(isset($_SESSION['error']) || isset($error)): ?>
        <?php $errMsg = isset($_SESSION['error']) ? $_SESSION['error'] : $error; unset($_SESSION['error']); ?>
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                showPopup('<?= addslashes($errMsg) ?>', 'error');
            });
        </script>
    <?php endif; ?>

    <!-- Add/Edit Form -->
    <?php if(isset($_GET['add']) || $editKelas): ?>
    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">
                <i class="fas fa-<?= $editKelas ? 'edit' : 'plus' ?> me-2"></i><?= $editKelas ? 'Edit' : 'Tambah' ?> Kelas
            </h6>
        </div>
        <div class="card-body">
            <form method="POST" action="">
                <input type="hidden" name="action" value="<?= $editKelas ? 'edit' : 'add' ?>">
                <?php if($editKelas): ?>
                <input type="hidden" name="id" value="<?= $editKelas['id'] ?>">
                <?php endif; ?>
                
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Nama Kelas <span class="text-danger">*</span></label>
                        <input type="text" name="nama_kelas" class="form-control" required 
                               value="<?= $editKelas ? htmlspecialchars($editKelas['nama_kelas']) : '' ?>">
                    </div>
                </div>
                
                <div class="mb-3">
                    <label class="form-label">Keterangan</label>
                    <textarea name="keterangan" class="form-control" rows="2" 
                              placeholder="Keterangan tambahan tentang kelas..."><?= $editKelas ? htmlspecialchars($editKelas['keterangan']) : '' ?></textarea>
                </div>
                
                <div class="d-flex gap-2">
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save me-2"></i>Simpan
                    </button>
                    <a href="kelas.php" class="btn btn-secondary">Batal</a>
                </div>
            </form>
        </div>
    </div>
    <?php endif; ?>

    <!-- Kelas List -->
    <div class="card shadow">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">
                <i class="fas fa-list me-2"></i>Daftar Kelas
            </h6>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered table-hover" id="kelasTable">
                    <thead class="table-light">
                        <tr>
                            <th>No</th>
                            <th>Nama Kelas</th>
                            <th>Keterangan</th>
                            <th width="150">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php $no = 1; foreach($kelasList as $kelas): ?>
                        <tr>
                            <td><?= $no++ ?></td>
                            <td><?= htmlspecialchars($kelas['nama_kelas']) ?></td>
                            <td><?= htmlspecialchars($kelas['keterangan'] ?: '-') ?></td>
                            <td>
                                <a href="?edit=<?= $kelas['id'] ?>" class="btn btn-warning btn-sm" title="Edit">
                                    <i class="fas fa-edit"></i>
                                </a>
                                
                                <form method="POST" action="" class="d-inline" onsubmit="return confirm('Yakin ingin menghapus kelas ini?')">
                                    <input type="hidden" name="action" value="delete">
                                    <input type="hidden" name="id" value="<?= $kelas['id'] ?>">
                                    <button type="submit" class="btn btn-danger btn-sm" title="Hapus">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </form>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                        <?php if(empty($kelasList)): ?>
                        <tr>
                            <td colspan="4" class="text-center text-muted py-4">
                                <i class="fas fa-inbox fa-3x mb-3"></i><br>
                                Belum ada data kelas
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
