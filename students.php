<?php
require_once 'config/config.php';
requireAdmin();

$db = new Database();

// Handle form submissions
if($_SERVER['REQUEST_METHOD'] == 'POST') {
    $action = $_POST['action'];
    
    if($action === 'add') {
        $username = $_POST['username'];
        $password = password_hash($_POST['password'], PASSWORD_DEFAULT);
        $nama_lengkap = $_POST['nama_lengkap'];
        $email = $_POST['email'];
        $no_hp = $_POST['no_hp'];
        $institusi = $_POST['institusi'];
        $jurusan = $_POST['jurusan'];
        $tanggal_mulai = $_POST['tanggal_mulai'];
        $tanggal_selesai = $_POST['tanggal_selesai'];
        
        $db->query('INSERT INTO users (username, password, nama_lengkap, email, role, no_hp, institusi, jurusan, tanggal_mulai, tanggal_selesai, status) 
                    VALUES (:username, :password, :nama_lengkap, :email, "student", :no_hp, :institusi, :jurusan, :tanggal_mulai, :tanggal_selesai, "active")');
        $db->bind(':username', $username);
        $db->bind(':password', $password);
        $db->bind(':nama_lengkap', $nama_lengkap);
        $db->bind(':email', $email);
        $db->bind(':no_hp', $no_hp);
        $db->bind(':institusi', $institusi);
        $db->bind(':jurusan', $jurusan);
        $db->bind(':tanggal_mulai', $tanggal_mulai);
        $db->bind(':tanggal_selesai', $tanggal_selesai);
        
        if($db->execute()) {
            $_SESSION['success'] = 'Siswa berhasil ditambahkan!';
            redirect(base_url('students.php'));
        } else {
            $error = 'Terjadi kesalahan saat menambahkan siswa!';
        }
    } elseif($action === 'edit') {
        $id = $_POST['id'];
        $nama_lengkap = $_POST['nama_lengkap'];
        $email = $_POST['email'];
        $no_hp = $_POST['no_hp'];
        $institusi = $_POST['institusi'];
        $jurusan = $_POST['jurusan'];
        $tanggal_mulai = $_POST['tanggal_mulai'];
        $tanggal_selesai = $_POST['tanggal_selesai'];
        $status = $_POST['status'];
        
        // Update password if provided
        $password_sql = '';
        if(!empty($_POST['password'])) {
            $password = password_hash($_POST['password'], PASSWORD_DEFAULT);
            $password_sql = ', password = :password';
        }
        
        $db->query('UPDATE users SET nama_lengkap = :nama_lengkap, email = :email, no_hp = :no_hp, 
                    institusi = :institusi, jurusan = :jurusan, tanggal_mulai = :tanggal_mulai, 
                    tanggal_selesai = :tanggal_selesai, status = :status ' . $password_sql . ' 
                    WHERE id = :id AND role = "student"');
        $db->bind(':id', $id);
        $db->bind(':nama_lengkap', $nama_lengkap);
        $db->bind(':email', $email);
        $db->bind(':no_hp', $no_hp);
        $db->bind(':institusi', $institusi);
        $db->bind(':jurusan', $jurusan);
        $db->bind(':tanggal_mulai', $tanggal_mulai);
        $db->bind(':tanggal_selesai', $tanggal_selesai);
        $db->bind(':status', $status);
        if(!empty($_POST['password'])) {
            $db->bind(':password', $password);
        }
        
        if($db->execute()) {
            $_SESSION['success'] = 'Data siswa berhasil diperbarui!';
            redirect(base_url('students.php'));
        } else {
            $error = 'Terjadi kesalahan saat memperbarui data siswa!';
        }
    } elseif($action === 'approve') {
        $id = $_POST['id'];
        
        $db->query('UPDATE users SET status = "active" WHERE id = :id AND role = "student"');
        $db->bind(':id', $id);
        
        if($db->execute()) {
            $_SESSION['success'] = 'Pendaftaran siswa berhasil disetujui!';
            redirect(base_url('students.php'));
        } else {
            $error = 'Terjadi kesalahan saat menyetujui pendaftaran!';
        }
    } elseif($action === 'reject') {
        $id = $_POST['id'];
        
        $db->query('DELETE FROM users WHERE id = :id AND role = "student" AND status = "pending"');
        $db->bind(':id', $id);
        
        if($db->execute()) {
            $_SESSION['success'] = 'Pendaftaran siswa ditolak dan dihapus!';
            redirect(base_url('students.php'));
        } else {
            $error = 'Terjadi kesalahan saat menolak pendaftaran!';
        }
    } elseif($action === 'delete') {
        $id = $_POST['id'];
        
        $db->query('DELETE FROM users WHERE id = :id AND role = "student"');
        $db->bind(':id', $id);
        
        if($db->execute()) {
            $_SESSION['success'] = 'Siswa berhasil dihapus!';
            redirect(base_url('students.php'));
        } else {
            $error = 'Terjadi kesalahan saat menghapus siswa!';
        }
    }
}

// Get search parameters
$search = $_GET['search'] ?? '';
$searchType = $_GET['search_type'] ?? 'all';

// Build search query
$searchSql = '';
$searchParams = [];

if(!empty($search)) {
    $searchTerm = "%$search%";
    switch($searchType) {
        case 'nama':
            $searchSql = ' AND (u.nama_lengkap LIKE :search)';
            break;
        case 'username':
            $searchSql = ' AND (u.username LIKE :search)';
            break;
        case 'jurusan':
            $searchSql = ' AND (j.nama_jurusan LIKE :search)';
            break;
        case 'institusi':
            $searchSql = ' AND (u.institusi LIKE :search)';
            break;
        case 'magang':
            $searchSql = ' AND (u.alamat_magang LIKE :search OR u.institusi LIKE :search)';
            break;
        default: // 'all' - search in all fields
            $searchSql = ' AND (u.nama_lengkap LIKE :search OR u.username LIKE :search OR j.nama_jurusan LIKE :search OR u.institusi LIKE :search OR u.alamat_magang LIKE :search)';
    }
    $searchParams[':search'] = $searchTerm;
}

// Get all students with search
$sql = 'SELECT u.*, j.nama_jurusan FROM users u 
        LEFT JOIN jurusan j ON u.jurusan_id = j.id 
        WHERE u.role = "student"' . $searchSql . ' 
        ORDER BY 
        CASE u.status 
            WHEN "pending" THEN 0 
            WHEN "active" THEN 1 
            ELSE 2 
        END, u.created_at DESC';

$db->query($sql);
foreach($searchParams as $key => $val) {
    $db->bind($key, $val);
}
$students = $db->resultSet();

// Count pending students
$db->query('SELECT COUNT(*) as total FROM users WHERE role = "student" AND status = "pending"');
$pendingCount = $db->single()['total'];

// Get edit data
$editStudent = null;
if(isset($_GET['edit'])) {
    $db->query('SELECT * FROM users WHERE id = :id AND role = "student"');
    $db->bind(':id', $_GET['edit']);
    $editStudent = $db->single();
}

include 'includes/header.php';
?>

<div class="container-fluid py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3 mb-0 text-gray-800">Kelola Siswa Magang</h1>
        <?php if(!isset($_GET['add']) && !$editStudent): ?>
        <div class="d-flex gap-2">
            <?php if($pendingCount > 0): ?>
            <span class="badge bg-warning fs-6">
                <i class="fas fa-clock me-1"></i><?= $pendingCount ?> Pending
            </span>
            <?php endif; ?>
            <a href="?add=1" class="btn btn-primary">
                <i class="fas fa-plus me-2"></i>Tambah Siswa
            </a>
        </div>
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
    <?php if(isset($_GET['add']) || $editStudent): ?>
    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">
                <i class="fas fa-user-plus me-2"></i><?= $editStudent ? 'Edit' : 'Tambah' ?> Siswa
            </h6>
        </div>
        <div class="card-body">
            <form method="POST" action="">
                <input type="hidden" name="action" value="<?= $editStudent ? 'edit' : 'add' ?>">
                <?php if($editStudent): ?>
                <input type="hidden" name="id" value="<?= $editStudent['id'] ?>">
                <?php endif; ?>
                
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Username</label>
                        <input type="text" name="username" class="form-control" required 
                               value="<?= $editStudent ? $editStudent['username'] : '' ?>" 
                               <?= $editStudent ? 'readonly' : '' ?>>
                        <?php if($editStudent): ?>
                        <small class="text-muted">Username tidak dapat diubah</small>
                        <?php endif; ?>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Password <?= $editStudent ? '(Kosongkan jika tidak ingin mengubah)' : '' ?></label>
                        <input type="password" name="password" class="form-control" <?= $editStudent ? '' : 'required' ?>>
                    </div>
                </div>
                
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Nama Lengkap</label>
                        <input type="text" name="nama_lengkap" class="form-control" required 
                               value="<?= $editStudent ? $editStudent['nama_lengkap'] : '' ?>">
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Email</label>
                        <input type="email" name="email" class="form-control" required 
                               value="<?= $editStudent ? $editStudent['email'] : '' ?>">
                    </div>
                </div>
                
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="form-label">No. HP</label>
                        <input type="text" name="no_hp" class="form-control" 
                               value="<?= $editStudent ? $editStudent['no_hp'] : '' ?>">
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Institusi/Sekolah</label>
                        <input type="text" name="institusi" class="form-control" required 
                               value="<?= $editStudent ? $editStudent['institusi'] : '' ?>">
                    </div>
                </div>
                
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Jurusan</label>
                        <input type="text" name="jurusan" class="form-control" 
                               value="<?= $editStudent ? $editStudent['jurusan'] : '' ?>">
                    </div>
                    <?php if($editStudent): ?>
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Status</label>
                        <select name="status" class="form-select" required>
                            <option value="active" <?= $editStudent['status'] === 'active' ? 'selected' : '' ?>>Active</option>
                            <option value="inactive" <?= $editStudent['status'] === 'inactive' ? 'selected' : '' ?>>Inactive</option>
                            <option value="completed" <?= $editStudent['status'] === 'completed' ? 'selected' : '' ?>>Completed</option>
                        </select>
                    </div>
                    <?php endif; ?>
                </div>
                
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Tanggal Mulai</label>
                        <input type="date" name="tanggal_mulai" class="form-control" 
                               value="<?= $editStudent ? $editStudent['tanggal_mulai'] : '' ?>">
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Tanggal Selesai</label>
                        <input type="date" name="tanggal_selesai" class="form-control" 
                               value="<?= $editStudent ? $editStudent['tanggal_selesai'] : '' ?>">
                    </div>
                </div>
                
                <div class="d-flex gap-2">
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save me-2"></i>Simpan
                    </button>
                    <a href="students.php" class="btn btn-secondary">Batal</a>
                </div>
            </form>
        </div>
    </div>
    <?php endif; ?>

    <!-- Search Form -->
    <?php if(!isset($_GET['add']) && !$editStudent): ?>
    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">
                <i class="fas fa-search me-2"></i>Pencarian Siswa
            </h6>
        </div>
        <div class="card-body">
            <form method="GET" action="" class="row g-3">
                <div class="col-md-6">
                    <div class="input-group">
                        <span class="input-group-text"><i class="fas fa-search"></i></span>
                        <input type="text" name="search" class="form-control" placeholder="Cari siswa..." 
                               value="<?= htmlspecialchars($search) ?>">
                    </div>
                </div>
                <div class="col-md-4">
                    <select name="search_type" class="form-select">
                        <option value="all" <?= $searchType === 'all' ? 'selected' : '' ?>>Semua Field</option>
                        <option value="nama" <?= $searchType === 'nama' ? 'selected' : '' ?>>Nama Lengkap</option>
                        <option value="username" <?= $searchType === 'username' ? 'selected' : '' ?>>Username</option>
                        <option value="jurusan" <?= $searchType === 'jurusan' ? 'selected' : '' ?>>Jurusan</option>
                        <option value="institusi" <?= $searchType === 'institusi' ? 'selected' : '' ?>>Institusi/Sekolah</option>
                        <option value="magang" <?= $searchType === 'magang' ? 'selected' : '' ?>>Tempat Magang</option>
                    </select>
                </div>
                <div class="col-md-2">
                    <button type="submit" class="btn btn-primary w-100">
                        <i class="fas fa-search me-2"></i>Cari
                    </button>
                </div>
            </form>
            <?php if(!empty($search)): ?>
            <div class="mt-3">
                <a href="students.php" class="btn btn-outline-secondary btn-sm">
                    <i class="fas fa-times me-2"></i>Reset Pencarian
                </a>
                <span class="ms-2 text-muted">Hasil pencarian untuk: <strong><?= htmlspecialchars($search) ?></strong></span>
            </div>
            <?php endif; ?>
        </div>
    </div>
    <?php endif; ?>

    <!-- Students List -->
    <div class="card shadow">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">
                <i class="fas fa-users me-2"></i>Daftar Siswa Magang
            </h6>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered table-hover" id="studentsTable">
                    <thead class="table-light">
                        <tr>
                            <th>Username</th>
                            <th>Nama Lengkap</th>
                            <th>Institusi</th>
                            <th>Jurusan</th>
                            <th>Periode</th>
                            <th>Status</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach($students as $student): ?>
                        <tr>
                            <td><?= htmlspecialchars($student['username']) ?></td>
                            <td><?= htmlspecialchars($student['nama_lengkap']) ?></td>
                            <td><?= htmlspecialchars($student['institusi']) ?></td>
                            <td><?= htmlspecialchars($student['jurusan']) ?></td>
                            <td>
                                <?= $student['tanggal_mulai'] ? formatTanggalIndo($student['tanggal_mulai']) : '-' ?><br>
                                <small>s/d</small><br>
                                <?= $student['tanggal_selesai'] ? formatTanggalIndo($student['tanggal_selesai']) : '-' ?>
                            </td>
                            <td>
                                <?php if($student['status'] === 'pending'): ?>
                                    <span class="badge bg-warning">Pending Approval</span>
                                <?php else: ?>
                                    <span class="badge bg-<?= $student['status'] === 'active' ? 'success' : ($student['status'] === 'completed' ? 'info' : 'secondary') ?>">
                                        <?= ucfirst($student['status']) ?>
                                    </span>
                                <?php endif; ?>
                            </td>
                            <td>
                                <?php if($student['status'] === 'pending'): ?>
                                    <form method="POST" action="" class="d-inline" onsubmit="return confirm('Setujui pendaftaran siswa ini?')">
                                        <input type="hidden" name="action" value="approve">
                                        <input type="hidden" name="id" value="<?= $student['id'] ?>">
                                        <button type="submit" class="btn btn-success btn-sm" title="Setujui">
                                            <i class="fas fa-check"></i>
                                        </button>
                                    </form>
                                    <form method="POST" action="" class="d-inline" onsubmit="return confirm('Tolak dan hapus pendaftaran ini?')">
                                        <input type="hidden" name="action" value="reject">
                                        <input type="hidden" name="id" value="<?= $student['id'] ?>">
                                        <button type="submit" class="btn btn-danger btn-sm" title="Tolak">
                                            <i class="fas fa-times"></i>
                                        </button>
                                    </form>
                                <?php else: ?>
                                    <a href="?edit=<?= $student['id'] ?>" class="btn btn-warning btn-sm">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    
                                    <form method="POST" action="" class="d-inline" onsubmit="return confirm('Yakin ingin menghapus siswa ini? Data presensi dan logbook juga akan terhapus.')">
                                        <input type="hidden" name="action" value="delete">
                                        <input type="hidden" name="id" value="<?= $student['id'] ?>">
                                        <button type="submit" class="btn btn-danger btn-sm">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </form>
                                <?php endif; ?>
                                
                                <a href="student_detail.php?id=<?= $student['id'] ?>" class="btn btn-info btn-sm">
                                    <i class="fas fa-eye"></i>
                                </a>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                        <?php if(empty($students)): ?>
                        <tr>
                            <td colspan="7" class="text-center text-muted">
                                <i class="fas fa-inbox fa-3x mb-3"></i><br>
                                Belum ada data siswa magang
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
