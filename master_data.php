<?php
require_once 'config/config.php';
requireAdmin();

$db = new Database();
$activeTab = $_GET['tab'] ?? 'jurusan';

// Handle Jurusan CRUD
if($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['action_jurusan'])) {
    $action = $_POST['action_jurusan'];
    
    if($action === 'add') {
        $nama_jurusan = $_POST['nama_jurusan'];
        
        $db->query('INSERT INTO jurusan (nama_jurusan) VALUES (:nama_jurusan)');
        $db->bind(':nama_jurusan', $nama_jurusan);
        
        if($db->execute()) {
            $_SESSION['success'] = 'Jurusan berhasil ditambahkan!';
        } else {
            $_SESSION['error'] = 'Terjadi kesalahan saat menambahkan jurusan!';
        }
        redirect(base_url('master_data.php?tab=jurusan'));
    } elseif($action === 'edit') {
        $id = $_POST['id'];
        $nama_jurusan = $_POST['nama_jurusan'];
        
        $db->query('UPDATE jurusan SET nama_jurusan = :nama_jurusan WHERE id = :id');
        $db->bind(':id', $id);
        $db->bind(':nama_jurusan', $nama_jurusan);
        
        if($db->execute()) {
            $_SESSION['success'] = 'Jurusan berhasil diperbarui!';
        } else {
            $_SESSION['error'] = 'Terjadi kesalahan saat memperbarui jurusan!';
        }
        redirect(base_url('master_data.php?tab=jurusan'));
    } elseif($action === 'delete') {
        $id = $_POST['id'];
        
        $db->query('DELETE FROM jurusan WHERE id = :id');
        $db->bind(':id', $id);
        
        if($db->execute()) {
            $_SESSION['success'] = 'Jurusan berhasil dihapus!';
        } else {
            $_SESSION['error'] = 'Terjadi kesalahan saat menghapus jurusan!';
        }
        redirect(base_url('master_data.php?tab=jurusan'));
    }
}

// Handle Kelas CRUD
if($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['action_kelas'])) {
    $action = $_POST['action_kelas'];
    
    if($action === 'add') {
        $nama_kelas = $_POST['nama_kelas'];
        
        $db->query('INSERT INTO kelas (nama_kelas) VALUES (:nama_kelas)');
        $db->bind(':nama_kelas', $nama_kelas);
        
        if($db->execute()) {
            $_SESSION['success'] = 'Kelas berhasil ditambahkan!';
        } else {
            $_SESSION['error'] = 'Terjadi kesalahan saat menambahkan kelas!';
        }
        redirect(base_url('master_data.php?tab=kelas'));
    } elseif($action === 'edit') {
        $id = $_POST['id'];
        $nama_kelas = $_POST['nama_kelas'];
        
        $db->query('UPDATE kelas SET nama_kelas = :nama_kelas WHERE id = :id');
        $db->bind(':id', $id);
        $db->bind(':nama_kelas', $nama_kelas);
        
        if($db->execute()) {
            $_SESSION['success'] = 'Kelas berhasil diperbarui!';
        } else {
            $_SESSION['error'] = 'Terjadi kesalahan saat memperbarui kelas!';
        }
        redirect(base_url('master_data.php?tab=kelas'));
    } elseif($action === 'delete') {
        $id = $_POST['id'];
        
        $db->query('DELETE FROM kelas WHERE id = :id');
        $db->bind(':id', $id);
        
        if($db->execute()) {
            $_SESSION['success'] = 'Kelas berhasil dihapus!';
        } else {
            $_SESSION['error'] = 'Terjadi kesalahan saat menghapus kelas!';
        }
        redirect(base_url('master_data.php?tab=kelas'));
    }
}

// Handle Guru Pembimbing CRUD
if($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['action_guru'])) {
    $action = $_POST['action_guru'];
    
    if($action === 'add') {
        $nama_guru = $_POST['nama_guru'];
        $nip = $_POST['nip'] ?? '';
        $no_hp = $_POST['no_hp'] ?? '';
        $email = $_POST['email'] ?? '';
        
        $db->query('INSERT INTO guru_pembimbing (nama_guru, nip, no_hp, email) VALUES (:nama_guru, :nip, :no_hp, :email)');
        $db->bind(':nama_guru', $nama_guru);
        $db->bind(':nip', $nip);
        $db->bind(':no_hp', $no_hp);
        $db->bind(':email', $email);
        
        if($db->execute()) {
            $_SESSION['success'] = 'Guru pembimbing berhasil ditambahkan!';
        } else {
            $_SESSION['error'] = 'Terjadi kesalahan saat menambahkan guru pembimbing!';
        }
        redirect(base_url('master_data.php?tab=guru'));
    } elseif($action === 'edit') {
        $id = $_POST['id'];
        $nama_guru = $_POST['nama_guru'];
        $nip = $_POST['nip'] ?? '';
        $no_hp = $_POST['no_hp'] ?? '';
        $email = $_POST['email'] ?? '';
        $status = $_POST['status'];
        
        $db->query('UPDATE guru_pembimbing SET nama_guru = :nama_guru, nip = :nip, no_hp = :no_hp, email = :email, status = :status WHERE id = :id');
        $db->bind(':id', $id);
        $db->bind(':nama_guru', $nama_guru);
        $db->bind(':nip', $nip);
        $db->bind(':no_hp', $no_hp);
        $db->bind(':email', $email);
        $db->bind(':status', $status);
        
        if($db->execute()) {
            $_SESSION['success'] = 'Guru pembimbing berhasil diperbarui!';
        } else {
            $_SESSION['error'] = 'Terjadi kesalahan saat memperbarui guru pembimbing!';
        }
        redirect(base_url('master_data.php?tab=guru'));
    } elseif($action === 'delete') {
        $id = $_POST['id'];
        
        $db->query('DELETE FROM guru_pembimbing WHERE id = :id');
        $db->bind(':id', $id);
        
        if($db->execute()) {
            $_SESSION['success'] = 'Guru pembimbing berhasil dihapus!';
        } else {
            $_SESSION['error'] = 'Terjadi kesalahan saat menghapus guru pembimbing!';
        }
        redirect(base_url('master_data.php?tab=guru'));
    }
}

// Get all jurusan
$db->query('SELECT * FROM jurusan ORDER BY nama_jurusan ASC');
$jurusanList = $db->resultSet();

// Get all kelas
$db->query('SELECT * FROM kelas ORDER BY nama_kelas ASC');
$kelasList = $db->resultSet();

// Get all guru pembimbing
$db->query('SELECT * FROM guru_pembimbing ORDER BY nama_guru ASC');
$guruList = $db->resultSet();

// Get edit data
$editJurusan = null;
$editKelas = null;
$editGuru = null;
if(isset($_GET['edit_jurusan'])) {
    $db->query('SELECT * FROM jurusan WHERE id = :id');
    $db->bind(':id', $_GET['edit_jurusan']);
    $editJurusan = $db->single();
    $activeTab = 'jurusan';
}
if(isset($_GET['edit_kelas'])) {
    $db->query('SELECT * FROM kelas WHERE id = :id');
    $db->bind(':id', $_GET['edit_kelas']);
    $editKelas = $db->single();
    $activeTab = 'kelas';
}
if(isset($_GET['edit_guru'])) {
    $db->query('SELECT * FROM guru_pembimbing WHERE id = :id');
    $db->bind(':id', $_GET['edit_guru']);
    $editGuru = $db->single();
    $activeTab = 'guru';
}

include 'includes/header.php';
?>

<div class="container-fluid py-4">
    <h1 class="h3 mb-4 text-gray-800">Master Data</h1>

    <?php if(isset($_SESSION['success'])): ?>
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                showPopup('<?= addslashes($_SESSION['success']) ?>', 'success');
            });
        </script>
        <?php unset($_SESSION['success']); ?>
    <?php endif; ?>
    
    <?php if(isset($_SESSION['error'])): ?>
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                showPopup('<?= addslashes($_SESSION['error']) ?>', 'error');
            });
        </script>
        <?php unset($_SESSION['error']); ?>
    <?php endif; ?>

    <!-- Tabs -->
    <div class="mb-4">
        <div class="btn-group" role="group">
            <a href="?tab=jurusan" class="btn btn-<?= $activeTab === 'jurusan' ? 'primary' : 'outline-primary' ?>">
                <i class="fas fa-graduation-cap me-2"></i>Kelola Jurusan
            </a>
            <a href="?tab=kelas" class="btn btn-<?= $activeTab === 'kelas' ? 'primary' : 'outline-primary' ?>">
                <i class="fas fa-school me-2"></i>Kelola Kelas
            </a>
            <a href="?tab=guru" class="btn btn-<?= $activeTab === 'guru' ? 'primary' : 'outline-primary' ?>">
                <i class="fas fa-chalkboard-teacher me-2"></i>Kelola Guru
            </a>
        </div>
    </div>

    <!-- Jurusan Tab -->
    <?php if($activeTab === 'jurusan'): ?>
    <div class="row">
        <div class="col-lg-4">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">
                        <i class="fas fa-<?= $editJurusan ? 'edit' : 'plus' ?> me-2"></i>
                        <?= $editJurusan ? 'Edit' : 'Tambah' ?> Jurusan
                    </h6>
                </div>
                <div class="card-body">
                    <form method="POST" action="">
                        <input type="hidden" name="action_jurusan" value="<?= $editJurusan ? 'edit' : 'add' ?>">
                        <?php if($editJurusan): ?>
                        <input type="hidden" name="id" value="<?= $editJurusan['id'] ?>">
                        <?php endif; ?>
                        
                        <div class="mb-3">
                            <label class="form-label">Nama Jurusan</label>
                            <input type="text" name="nama_jurusan" class="form-control" required 
                                   value="<?= $editJurusan ? $editJurusan['nama_jurusan'] : '' ?>"
                                   placeholder="Contoh: Teknik Informatika">
                        </div>
                        
                        <div class="d-flex gap-2">
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save me-2"></i>Simpan
                            </button>
                            <?php if($editJurusan): ?>
                            <a href="master_data.php?tab=jurusan" class="btn btn-secondary">Batal</a>
                            <?php endif; ?>
                        </div>
                    </form>
                </div>
            </div>
        </div>
        
        <div class="col-lg-8">
            <div class="card shadow">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">
                        <i class="fas fa-list me-2"></i>Daftar Jurusan
                    </h6>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-bordered table-hover">
                            <thead class="table-light">
                                <tr>
                                    <th>No</th>
                                    <th>Nama Jurusan</th>
                                    <th width="150">Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php $no = 1; foreach($jurusanList as $j): ?>
                                <tr>
                                    <td><?= $no++ ?></td>
                                    <td><?= htmlspecialchars($j['nama_jurusan']) ?></td>
                                    <td>
                                        <a href="?tab=jurusan&edit_jurusan=<?= $j['id'] ?>" class="btn btn-warning btn-sm">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        <form method="POST" action="" class="d-inline" onsubmit="return confirm('Yakin ingin menghapus jurusan ini?')">
                                            <input type="hidden" name="action_jurusan" value="delete">
                                            <input type="hidden" name="id" value="<?= $j['id'] ?>">
                                            <button type="submit" class="btn btn-danger btn-sm">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </form>
                                    </td>
                                </tr>
                                <?php endforeach; ?>
                                <?php if(empty($jurusanList)): ?>
                                <tr>
                                    <td colspan="3" class="text-center text-muted">
                                        <i class="fas fa-inbox fa-2x mb-2"></i><br>
                                        Belum ada data jurusan
                                    </td>
                                </tr>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <?php endif; ?>

    <!-- Kelas Tab -->
    <?php if($activeTab === 'kelas'): ?>
    <div class="row">
        <div class="col-lg-4">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">
                        <i class="fas fa-<?= $editKelas ? 'edit' : 'plus' ?> me-2"></i>
                        <?= $editKelas ? 'Edit' : 'Tambah' ?> Kelas
                    </h6>
                </div>
                <div class="card-body">
                    <form method="POST" action="">
                        <input type="hidden" name="action_kelas" value="<?= $editKelas ? 'edit' : 'add' ?>">
                        <?php if($editKelas): ?>
                        <input type="hidden" name="id" value="<?= $editKelas['id'] ?>">
                        <?php endif; ?>
                        
                        <div class="mb-3">
                            <label class="form-label">Nama Kelas</label>
                            <input type="text" name="nama_kelas" class="form-control" required 
                                   value="<?= $editKelas ? $editKelas['nama_kelas'] : '' ?>"
                                   placeholder="Contoh: TI-1A">
                        </div>
                        
                        <div class="d-flex gap-2">
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save me-2"></i>Simpan
                            </button>
                            <?php if($editKelas): ?>
                            <a href="master_data.php?tab=kelas" class="btn btn-secondary">Batal</a>
                            <?php endif; ?>
                        </div>
                    </form>
                </div>
            </div>
        </div>
        
        <div class="col-lg-8">
            <div class="card shadow">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">
                        <i class="fas fa-list me-2"></i>Daftar Kelas
                    </h6>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-bordered table-hover">
                            <thead class="table-light">
                                <tr>
                                    <th>No</th>
                                    <th>Nama Kelas</th>
                                    <th width="150">Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php $no = 1; foreach($kelasList as $k): ?>
                                <tr>
                                    <td><?= $no++ ?></td>
                                    <td><?= htmlspecialchars($k['nama_kelas']) ?></td>
                                    <td>
                                        <a href="?tab=kelas&edit_kelas=<?= $k['id'] ?>" class="btn btn-warning btn-sm">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        <form method="POST" action="" class="d-inline" onsubmit="return confirm('Yakin ingin menghapus kelas ini?')">
                                            <input type="hidden" name="action_kelas" value="delete">
                                            <input type="hidden" name="id" value="<?= $k['id'] ?>">
                                            <button type="submit" class="btn btn-danger btn-sm">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </form>
                                    </td>
                                </tr>
                                <?php endforeach; ?>
                                <?php if(empty($kelasList)): ?>
                                <tr>
                                    <td colspan="3" class="text-center text-muted">
                                        <i class="fas fa-inbox fa-2x mb-2"></i><br>
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
    </div>
    <?php endif; ?>

    <!-- Guru Pembimbing Tab -->
    <?php if($activeTab === 'guru'): ?>
    <div class="row">
        <div class="col-lg-4">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">
                        <i class="fas fa-<?= $editGuru ? 'edit' : 'plus' ?> me-2"></i>
                        <?= $editGuru ? 'Edit' : 'Tambah' ?> Guru Pembimbing
                    </h6>
                </div>
                <div class="card-body">
                    <form method="POST" action="">
                        <input type="hidden" name="action_guru" value="<?= $editGuru ? 'edit' : 'add' ?>">
                        <?php if($editGuru): ?>
                        <input type="hidden" name="id" value="<?= $editGuru['id'] ?>">
                        <?php endif; ?>
                        
                        <div class="mb-3">
                            <label class="form-label">Nama Guru</label>
                            <input type="text" name="nama_guru" class="form-control" required 
                                   value="<?= $editGuru ? $editGuru['nama_guru'] : '' ?>"
                                   placeholder="Contoh: Budi Santoso, S.Pd">
                        </div>
                        
                        <div class="mb-3">
                            <label class="form-label">NIP</label>
                            <input type="text" name="nip" class="form-control" 
                                   value="<?= $editGuru ? $editGuru['nip'] : '' ?>"
                                   placeholder="Nomor Induk Pegawai">
                        </div>
                        
                        <div class="mb-3">
                            <label class="form-label">No. HP</label>
                            <input type="text" name="no_hp" class="form-control" 
                                   value="<?= $editGuru ? $editGuru['no_hp'] : '' ?>"
                                   placeholder="08123456789">
                        </div>
                        
                        <div class="mb-3">
                            <label class="form-label">Email</label>
                            <input type="email" name="email" class="form-control" 
                                   value="<?= $editGuru ? $editGuru['email'] : '' ?>"
                                   placeholder="email@sekolah.ac.id">
                        </div>
                        
                        <?php if($editGuru): ?>
                        <div class="mb-3">
                            <label class="form-label">Status</label>
                            <select name="status" class="form-select" required>
                                <option value="active" <?= $editGuru['status'] === 'active' ? 'selected' : '' ?>>Aktif</option>
                                <option value="inactive" <?= $editGuru['status'] === 'inactive' ? 'selected' : '' ?>>Non-Aktif</option>
                            </select>
                        </div>
                        <?php endif; ?>
                        
                        <div class="d-flex gap-2">
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save me-2"></i>Simpan
                            </button>
                            <?php if($editGuru): ?>
                            <a href="master_data.php?tab=guru" class="btn btn-secondary">Batal</a>
                            <?php endif; ?>
                        </div>
                    </form>
                </div>
            </div>
        </div>
        
        <div class="col-lg-8">
            <div class="card shadow">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">
                        <i class="fas fa-list me-2"></i>Daftar Guru Pembimbing
                    </h6>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-bordered table-hover">
                            <thead class="table-light">
                                <tr>
                                    <th>No</th>
                                    <th>Nama Guru</th>
                                    <th>NIP</th>
                                    <th>No. HP</th>
                                    <th>Email</th>
                                    <th>Status</th>
                                    <th width="150">Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php $no = 1; foreach($guruList as $g): ?>
                                <tr>
                                    <td><?= $no++ ?></td>
                                    <td><?= htmlspecialchars($g['nama_guru']) ?></td>
                                    <td><?= htmlspecialchars($g['nip'] ?: '-') ?></td>
                                    <td><?= htmlspecialchars($g['no_hp'] ?: '-') ?></td>
                                    <td><?= htmlspecialchars($g['email'] ?: '-') ?></td>
                                    <td>
                                        <span class="badge bg-<?= $g['status'] === 'active' ? 'success' : 'secondary' ?>">
                                            <?= $g['status'] === 'active' ? 'Aktif' : 'Non-Aktif' ?>
                                        </span>
                                    </td>
                                    <td>
                                        <a href="?tab=guru&edit_guru=<?= $g['id'] ?>" class="btn btn-warning btn-sm">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        <form method="POST" action="" class="d-inline" onsubmit="return confirm('Yakin ingin menghapus guru pembimbing ini?')">
                                            <input type="hidden" name="action_guru" value="delete">
                                            <input type="hidden" name="id" value="<?= $g['id'] ?>">
                                            <button type="submit" class="btn btn-danger btn-sm">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </form>
                                    </td>
                                </tr>
                                <?php endforeach; ?>
                                <?php if(empty($guruList)): ?>
                                <tr>
                                    <td colspan="7" class="text-center text-muted">
                                        <i class="fas fa-inbox fa-2x mb-2"></i><br>
                                        Belum ada data guru pembimbing
                                    </td>
                                </tr>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <?php endif; ?>
</div>

<?php include 'includes/footer.php'; ?>
