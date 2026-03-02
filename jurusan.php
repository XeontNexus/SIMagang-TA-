<?php
require_once 'config/config.php';
requireAuth();
requireAdmin();

$db = new Database();

// Handle form submissions
if($_SERVER['REQUEST_METHOD'] == 'POST') {
    $action = $_POST['action'];
    
    if($action === 'add') {
        $nama_jurusan = $_POST['nama_jurusan'];
        $keterangan = $_POST['keterangan'];
        
        $db->query('INSERT INTO jurusan (nama_jurusan, keterangan) VALUES (:nama_jurusan, :keterangan)');
        $db->bind(':nama_jurusan', $nama_jurusan);
        $db->bind(':keterangan', $keterangan);
        
        if($db->execute()) {
            $_SESSION['success'] = 'Jurusan berhasil ditambahkan!';
            redirect(base_url('jurusan.php'));
        } else {
            $error = 'Terjadi kesalahan saat menambahkan jurusan!';
        }
    } elseif($action === 'edit') {
        $id = $_POST['id'];
        $nama_jurusan = $_POST['nama_jurusan'];
        $keterangan = $_POST['keterangan'];
        
        $db->query('UPDATE jurusan SET nama_jurusan = :nama_jurusan, keterangan = :keterangan WHERE id = :id');
        $db->bind(':nama_jurusan', $nama_jurusan);
        $db->bind(':keterangan', $keterangan);
        $db->bind(':id', $id);
        
        if($db->execute()) {
            $_SESSION['success'] = 'Jurusan berhasil diperbarui!';
            redirect(base_url('jurusan.php'));
        } else {
            $error = 'Terjadi kesalahan saat memperbarui jurusan!';
        }
    } elseif($action === 'delete') {
        $id = $_POST['id'];
        
        $db->query('DELETE FROM jurusan WHERE id = :id');
        $db->bind(':id', $id);
        
        if($db->execute()) {
            $_SESSION['success'] = 'Jurusan berhasil dihapus!';
            redirect(base_url('jurusan.php'));
        } else {
            $error = 'Terjadi kesalahan saat menghapus jurusan!';
        }
    }
}

// Get all jurusan
$db->query('SELECT * FROM jurusan ORDER BY nama_jurusan ASC');
$jurusan = $db->resultSet();

// Get jurusan for edit
$editJurusan = null;
if(isset($_GET['edit'])) {
    $db->query('SELECT * FROM jurusan WHERE id = :id');
    $db->bind(':id', $_GET['edit']);
    $editJurusan = $db->single();
}

$pageTitle = 'Kelola Jurusan';
include 'includes/header.php';
?>

<div class="container-fluid py-4">
    <h1 class="h3 mb-4 text-gray-800">Kelola Jurusan</h1>
    
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
    
    <div class="row">
        <!-- Form -->
        <div class="col-lg-4">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">
                        <i class="fas fa-<?= $editJurusan ? 'edit' : 'plus' ?> me-2"></i>
                        <?= $editJurusan ? 'Edit Jurusan' : 'Tambah Jurusan' ?>
                    </h6>
                </div>
                <div class="card-body">
                    <form method="POST" action="">
                        <input type="hidden" name="action" value="<?= $editJurusan ? 'edit' : 'add' ?>">
                        <?php if($editJurusan): ?>
                        <input type="hidden" name="id" value="<?= $editJurusan['id'] ?>">
                        <?php endif; ?>
                        
                        <div class="mb-3">
                            <label class="form-label">Nama Jurusan</label>
                            <input type="text" name="nama_jurusan" class="form-control" required
                                   value="<?= $editJurusan ? htmlspecialchars($editJurusan['nama_jurusan']) : '' ?>">
                        </div>
                        
                        <div class="mb-3">
                            <label class="form-label">Keterangan</label>
                            <textarea name="keterangan" class="form-control" rows="3"><?= $editJurusan ? htmlspecialchars($editJurusan['keterangan']) : '' ?></textarea>
                        </div>
                        
                        <div class="d-grid">
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save me-2"></i>
                                <?= $editJurusan ? 'Update' : 'Simpan' ?>
                            </button>
                        </div>
                        
                        <?php if($editJurusan): ?>
                        <div class="d-grid mt-2">
                            <a href="jurusan.php" class="btn btn-secondary">
                                <i class="fas fa-times me-2"></i>Batal
                            </a>
                        </div>
                        <?php endif; ?>
                    </form>
                </div>
            </div>
        </div>
        
        <!-- Table -->
        <div class="col-lg-8">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">
                        <i class="fas fa-list me-2"></i>Daftar Jurusan
                    </h6>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-bordered table-hover" id="dataTable">
                            <thead class="thead-light">
                                <tr>
                                    <th>No</th>
                                    <th>Nama Jurusan</th>
                                    <th>Keterangan</th>
                                    <th>Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php $no = 1; foreach($jurusan as $j): ?>
                                <tr>
                                    <td><?= $no++ ?></td>
                                    <td><?= htmlspecialchars($j['nama_jurusan']) ?></td>
                                    <td><?= htmlspecialchars($j['keterangan'] ?: '-') ?></td>
                                    <td>
                                        <a href="?edit=<?= $j['id'] ?>" class="btn btn-warning btn-sm" title="Edit">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        <form method="POST" action="" class="d-inline" onsubmit="return confirm('Yakin ingin menghapus jurusan ini?')">
                                            <input type="hidden" name="action" value="delete">
                                            <input type="hidden" name="id" value="<?= $j['id'] ?>">
                                            <button type="submit" class="btn btn-danger btn-sm" title="Hapus">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </form>
                                    </td>
                                </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include 'includes/footer.php'; ?>
