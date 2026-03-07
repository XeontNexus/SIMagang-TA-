<?php
require_once 'config/config.php';
requireAuth();

$db = new Database();
$user_id = $_SESSION['user_id'];
$role = $_SESSION['role'];

// Get user data with jurusan
$db->query('SELECT u.*, k.nama_kelas as kelas_nama, j.nama_jurusan FROM users u 
            LEFT JOIN kelas k ON u.kelas_id = k.id 
            LEFT JOIN jurusan j ON u.jurusan_id = j.id 
            WHERE u.id = :id');
$db->bind(':id', $user_id);
$user = $db->single();

// Check if profile is incomplete (for first login notification)
$profileIncomplete = empty($user['institusi']) || empty($user['no_hp']) || empty($user['jurusan_id']) || empty($user['alamat_magang']);
$showProfileModal = false;
if($profileIncomplete && !isset($_SESSION['profile_completed'])) {
    $showProfileModal = true;
} elseif(!$profileIncomplete) {
    $_SESSION['profile_completed'] = true;
}

// Dashboard statistics
if($role === 'admin') {
    // Admin stats
    $db->query('SELECT COUNT(*) as total FROM users WHERE role = "student"');
    $totalStudents = $db->single()['total'];
    
    $db->query('SELECT COUNT(*) as total FROM users WHERE role = "student" AND status = "active"');
    $activeStudents = $db->single()['total'];
    
    $db->query('SELECT COUNT(*) as total FROM presensi WHERE tanggal = CURDATE()');
    $todayPresensi = $db->single()['total'];
    
    $db->query('SELECT COUNT(*) as total FROM logbook WHERE status = "submitted"');
    $pendingLogbook = $db->single()['total'];
    
    // Recent students
    $db->query('SELECT * FROM users WHERE role = "student" ORDER BY created_at DESC LIMIT 5');
    $recentStudents = $db->resultSet();
    
} else {
    // Student stats
    $db->query('SELECT COUNT(*) as total FROM presensi WHERE user_id = :user_id');
    $db->bind(':user_id', $user_id);
    $totalPresensi = $db->single()['total'];
    
    $db->query('SELECT COUNT(*) as total FROM logbook WHERE user_id = :user_id');
    $db->bind(':user_id', $user_id);
    $totalLogbook = $db->single()['total'];
    
    $db->query('SELECT COUNT(*) as total FROM logbook WHERE user_id = :user_id AND status = "approved"');
    $db->bind(':user_id', $user_id);
    $approvedLogbook = $db->single()['total'];
    
    // Today's presensi status
    $db->query('SELECT * FROM presensi WHERE user_id = :user_id AND tanggal = CURDATE()');
    $db->bind(':user_id', $user_id);
    $todayPresensi = $db->single();
    
    // Fetch jurusan and kelas lists for profile completion modal
    $db->query('SELECT * FROM jurusan ORDER BY nama_jurusan ASC');
    $jurusanList = $db->resultSet();
    
    $db->query('SELECT * FROM kelas ORDER BY nama_kelas ASC');
    $kelasList = $db->resultSet();
}

include 'includes/header.php';
?>

<div class="container-fluid py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3 mb-0 text-gray-800">Dashboard</h1>
        <span class="text-muted"><?= date('l, d F Y') ?></span>
    </div>

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

    <?php if($role === 'admin'): ?>
        <!-- Admin Dashboard -->
        <div class="row">
            <div class="col-xl-3 col-md-6 mb-4">
                <div class="card border-left-primary shadow h-100 py-2">
                    <div class="card-body">
                        <div class="row no-gutters align-items-center">
                            <div class="col mr-2">
                                <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">Total Siswa Magang</div>
                                <div class="h5 mb-0 font-weight-bold text-gray-800"><?= $totalStudents ?></div>
                            </div>
                            <div class="col-auto">
                                <i class="fas fa-users fa-2x text-gray-300"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-xl-3 col-md-6 mb-4">
                <div class="card border-left-success shadow h-100 py-2">
                    <div class="card-body">
                        <div class="row no-gutters align-items-center">
                            <div class="col mr-2">
                                <div class="text-xs font-weight-bold text-success text-uppercase mb-1">Siswa Aktif</div>
                                <div class="h5 mb-0 font-weight-bold text-gray-800"><?= $activeStudents ?></div>
                            </div>
                            <div class="col-auto">
                                <i class="fas fa-user-check fa-2x text-gray-300"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-xl-3 col-md-6 mb-4">
                <div class="card border-left-info shadow h-100 py-2">
                    <div class="card-body">
                        <div class="row no-gutters align-items-center">
                            <div class="col mr-2">
                                <div class="text-xs font-weight-bold text-info text-uppercase mb-1">Presensi Hari Ini</div>
                                <div class="h5 mb-0 font-weight-bold text-gray-800"><?= $todayPresensi ?></div>
                            </div>
                            <div class="col-auto">
                                <i class="fas fa-calendar-check fa-2x text-gray-300"></i>
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
                                <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">Logbook Pending</div>
                                <div class="h5 mb-0 font-weight-bold text-gray-800"><?= $pendingLogbook ?></div>
                            </div>
                            <div class="col-auto">
                                <i class="fas fa-book fa-2x text-gray-300"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Recent Students Table -->
        <div class="card shadow mb-4">
            <div class="card-header py-3 d-flex justify-content-between align-items-center">
                <h6 class="m-0 font-weight-bold text-primary">Siswa Magang Terbaru</h6>
                <a href="students.php" class="btn btn-primary btn-sm">Lihat Semua</a>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-bordered table-hover">
                        <thead class="thead-light">
                            <tr>
                                <th>Nama</th>
                                <th>Institusi</th>
                                <th>Jurusan</th>
                                <th>Status</th>
                                <th>Tanggal Mulai</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach($recentStudents as $student): ?>
                            <tr>
                                <td><?= htmlspecialchars($student['nama_lengkap']) ?></td>
                                <td><?= htmlspecialchars($student['institusi']) ?></td>
                                <td><?= htmlspecialchars($student['jurusan']) ?></td>
                                <td>
                                    <span class="badge bg-<?= $student['status'] === 'active' ? 'success' : ($student['status'] === 'completed' ? 'info' : 'secondary') ?>">
                                        <?= ucfirst($student['status']) ?>
                                    </span>
                                </td>
                                <td><?= $student['tanggal_mulai'] ? formatTanggalIndo($student['tanggal_mulai']) : '-' ?></td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

    <?php else: ?>
        <!-- Student Dashboard -->
        <div class="row">
            <div class="col-xl-4 col-md-6 mb-4">
                <div class="card border-left-primary shadow h-100 py-2">
                    <div class="card-body">
                        <div class="row no-gutters align-items-center">
                            <div class="col mr-2">
                                <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">Total Presensi</div>
                                <div class="h5 mb-0 font-weight-bold text-gray-800"><?= $totalPresensi ?></div>
                            </div>
                            <div class="col-auto">
                                <i class="fas fa-calendar-check fa-2x text-gray-300"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-xl-4 col-md-6 mb-4">
                <div class="card border-left-success shadow h-100 py-2">
                    <div class="card-body">
                        <div class="row no-gutters align-items-center">
                            <div class="col mr-2">
                                <div class="text-xs font-weight-bold text-success text-uppercase mb-1">Total Logbook</div>
                                <div class="h5 mb-0 font-weight-bold text-gray-800"><?= $totalLogbook ?></div>
                            </div>
                            <div class="col-auto">
                                <i class="fas fa-book fa-2x text-gray-300"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-xl-4 col-md-6 mb-4">
                <div class="card border-left-info shadow h-100 py-2">
                    <div class="card-body">
                        <div class="row no-gutters align-items-center">
                            <div class="col mr-2">
                                <div class="text-xs font-weight-bold text-info text-uppercase mb-1">Logbook Approved</div>
                                <div class="h5 mb-0 font-weight-bold text-gray-800"><?= $approvedLogbook ?></div>
                            </div>
                            <div class="col-auto">
                                <i class="fas fa-check-circle fa-2x text-gray-300"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Today's Attendance Status -->
        <div class="row">
            <div class="col-lg-12">
                <div class="card shadow mb-4">
                    <div class="card-header py-3">
                        <h6 class="m-0 font-weight-bold text-primary">Status Presensi Hari Ini</h6>
                    </div>
                    <div class="card-body">
                        <?php if($todayPresensi): ?>
                            <div class="row">
                                <div class="col-md-6">
                                    <p><strong>Status:</strong> 
                                        <span class="badge bg-<?= $todayPresensi['status'] === 'hadir' ? 'success' : ($todayPresensi['status'] === 'izin' ? 'warning' : ($todayPresensi['status'] === 'sakit' ? 'danger' : 'secondary')) ?>">
                                            <?= ucfirst($todayPresensi['status']) ?>
                                        </span>
                                    </p>
                                    <p><strong>Jam Masuk:</strong> <?= $todayPresensi['jam_masuk'] ?></p>
                                    <p><strong>Jam Keluar:</strong> <?= $todayPresensi['jam_keluar'] ?: 'Belum checkout' ?></p>
                                </div>
                                <div class="col-md-6">
                                    <?php if($todayPresensi['keterangan']): ?>
                                        <p><strong>Keterangan:</strong> <?= htmlspecialchars($todayPresensi['keterangan']) ?></p>
                                    <?php endif; ?>
                                </div>
                            </div>
                        <?php else: ?>
                            <div class="alert alert-info">
                                <i class="fas fa-info-circle me-2"></i>Anda belum melakukan presensi hari ini.
                                <a href="presensi.php" class="btn btn-primary btn-sm ms-3">Presensi Sekarang</a>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>

        <!-- Quick Actions -->
        <div class="row">
            <div class="col-lg-12">
                <div class="card shadow">
                    <div class="card-header py-3">
                        <h6 class="m-0 font-weight-bold text-primary">Aksi Cepat</h6>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <a href="presensi.php" class="btn btn-primary btn-lg w-100">
                                    <i class="fas fa-clock me-2"></i>Presensi
                                </a>
                            </div>
                            <div class="col-md-6 mb-3">
                                <a href="logbook.php" class="btn btn-success btn-lg w-100">
                                    <i class="fas fa-book me-2"></i>Logbook
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    <?php endif; ?>
</div>

<?php include 'includes/footer.php'; ?>

<!-- Profile Completion Modal -->
<?php if($showProfileModal && $role === 'student'): ?>
<div class="modal fade" id="profileModal" tabindex="-1" aria-hidden="true" data-bs-backdrop="static">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title"><i class="fas fa-user-edit me-2"></i>Lengkapi Data Diri & Informasi Magang</h5>
            </div>
            <div class="modal-body">
                <div class="alert alert-info">
                    <i class="fas fa-info-circle me-2"></i>Selamat datang! Silakan lengkapi data diri dan informasi magang Anda sebelum mulai menggunakan sistem.
                </div>
                <form id="profileCompleteForm" method="POST" action="profile.php">
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">No. HP/WA <span class="text-danger">*</span></label>
                            <input type="text" name="no_hp" class="form-control" required placeholder="08xxxxxxxxxx">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Jurusan/Program Studi <span class="text-danger">*</span></label>
                            <select name="jurusan_id" class="form-select" required>
                                <option value="">- Pilih Jurusan -</option>
                                <?php foreach($jurusanList as $j): ?>
                                <option value="<?= $j['id'] ?>"><?= htmlspecialchars($j['nama_jurusan']) ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Tempat Magang (Institusi) <span class="text-danger">*</span></label>
                        <input type="text" name="institusi" class="form-control" required placeholder="Nama Perusahaan/Instansi tempat magang">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Link Google Maps Tempat Magang <small class="text-muted">(opsional)</small></label>
                        <input type="url" name="gmap_link" class="form-control" placeholder="https://maps.google.com/...">
                        <small class="text-muted">Copy link Google Maps lokasi tempat magang Anda (opsional)</small>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Alamat Tempat Magang <span class="text-danger">*</span></label>
                        <textarea name="alamat_magang" class="form-control" rows="2" required placeholder="Alamat lengkap tempat magang"></textarea>
                    </div>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Guru Pembimbing</label>
                            <input type="text" name="guru_pembimbing" class="form-control" placeholder="Nama guru pembimbing">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Kelas</label>
                            <select name="kelas_id" class="form-select">
                                <option value="">- Pilih Kelas -</option>
                                <?php foreach($kelasList as $k): ?>
                                <option value="<?= $k['id'] ?>"><?= htmlspecialchars($k['nama_kelas']) ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Pendamping Lapangan</label>
                            <input type="text" name="pendamping_lapangan" class="form-control" placeholder="Nama pendamping di tempat magang">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">No. Telp Pendamping</label>
                            <input type="text" name="telp_pendamping" class="form-control" placeholder="08xxxxxxxxxx">
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Tanggal Mulai Magang <span class="text-danger">*</span></label>
                            <input type="date" name="tanggal_mulai" class="form-control" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Tanggal Selesai Magang <span class="text-danger">*</span></label>
                            <input type="date" name="tanggal_selesai" class="form-control" required>
                        </div>
                    </div>
                    <input type="hidden" name="action" value="complete_profile">
                    <div class="d-grid">
                        <button type="submit" class="btn btn-primary btn-lg">
                            <i class="fas fa-save me-2"></i>Simpan Data
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        var profileModal = new bootstrap.Modal(document.getElementById('profileModal'));
        profileModal.show();
    });
</script>
<?php endif; ?>
