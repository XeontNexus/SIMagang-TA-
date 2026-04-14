<?php
require_once 'config/config.php';
requireAdmin();

$db = new Database();

// Handle POST requests
if($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';
    
    if($action === 'save_jadwal') {
        $selectedStudents = $_POST['selected_students'] ?? [];
        $hariList = $_POST['hari'] ?? [];
        $jamMasuk = $_POST['jam_masuk'] ?? '08:00';
        $jamKeluar = $_POST['jam_keluar'] ?? '16:00';
        $bulanMulai = $_POST['bulan_mulai'];
        $bulanSelesai = $_POST['bulan_selesai'];
        
        if(empty($selectedStudents)) {
            $_SESSION['error'] = 'Pilih minimal 1 siswa!';
        } elseif(empty($hariList)) {
            $_SESSION['error'] = 'Pilih minimal 1 hari!';
        } else {
            $successCount = 0;
            foreach($selectedStudents as $userId) {
                foreach($hariList as $hari) {
                    // Cek apakah jadwal sudah ada
                    $db->query('SELECT id FROM jadwal_presensi 
                               WHERE user_id = :user_id AND hari = :hari 
                               AND bulan_mulai = :bulan_mulai AND bulan_selesai = :bulan_selesai');
                    $db->bind(':user_id', $userId);
                    $db->bind(':hari', $hari);
                    $db->bind(':bulan_mulai', $bulanMulai);
                    $db->bind(':bulan_selesai', $bulanSelesai);
                    $existing = $db->single();
                    
                    if($existing) {
                        // Update jadwal existing
                        $db->query('UPDATE jadwal_presensi 
                                   SET jam_masuk = :jam_masuk, jam_keluar = :jam_keluar, is_active = 1
                                   WHERE id = :id');
                        $db->bind(':jam_masuk', $jamMasuk);
                        $db->bind(':jam_keluar', $jamKeluar);
                        $db->bind(':id', $existing['id']);
                    } else {
                        // Insert jadwal baru
                        $db->query('INSERT INTO jadwal_presensi 
                                   (user_id, hari, jam_masuk, jam_keluar, bulan_mulai, bulan_selesai, is_active) 
                                   VALUES (:user_id, :hari, :jam_masuk, :jam_keluar, :bulan_mulai, :bulan_selesai, 1)');
                        $db->bind(':user_id', $userId);
                        $db->bind(':hari', $hari);
                        $db->bind(':jam_masuk', $jamMasuk);
                        $db->bind(':jam_keluar', $jamKeluar);
                        $db->bind(':bulan_mulai', $bulanMulai);
                        $db->bind(':bulan_selesai', $bulanSelesai);
                    }
                    
                    if($db->execute()) {
                        $successCount++;
                    }
                }
            }
            $_SESSION['success'] = "Berhasil menyimpan jadwal untuk {$successCount} entri!";
            redirect(base_url('jadwal_presensi.php'));
        }
    } elseif($action === 'delete_jadwal_student') {
        // Delete all schedules for a student in a period
        $userId = $_POST['user_id'] ?? 0;
        $bulanMulai = $_POST['bulan_mulai'] ?? '';
        $bulanSelesai = $_POST['bulan_selesai'] ?? '';
        
        $db->query('DELETE FROM jadwal_presensi 
                   WHERE user_id = :user_id AND bulan_mulai = :bulan_mulai AND bulan_selesai = :bulan_selesai');
        $db->bind(':user_id', $userId);
        $db->bind(':bulan_mulai', $bulanMulai);
        $db->bind(':bulan_selesai', $bulanSelesai);
        
        if($db->execute()) {
            $_SESSION['success'] = 'Jadwal siswa berhasil dihapus!';
        } else {
            $_SESSION['error'] = 'Gagal menghapus jadwal!';
        }
        redirect(base_url('jadwal_presensi.php'));
    }
}

// Get filter parameters
$searchNama = $_GET['nama'] ?? '';
$searchInstitusi = $_GET['institusi'] ?? '';
$searchKelas = $_GET['kelas'] ?? '';
$sortBy = $_GET['sort'] ?? 'nama';
$filterStatus = $_GET['status_jadwal'] ?? ''; // '' = semua, 'sudah' = sudah diatur, 'belum' = belum diatur

// Get all kelas for dropdown
$db->query('SELECT * FROM kelas ORDER BY nama_kelas ASC');
$kelasList = $db->resultSet();

// Get students with their schedule summary (grouped by student)
$db->query('SELECT 
    u.id, u.nama_lengkap, u.institusi, u.kelas_id,
    k.nama_kelas,
    COUNT(jp.id) as total_jadwal,
    GROUP_CONCAT(DISTINCT jp.hari ORDER BY FIELD(jp.hari, "Senin","Selasa","Rabu","Kamis","Jumat","Sabtu","Minggu") SEPARATOR ", ") as hari_list,
    MIN(jp.jam_masuk) as jam_masuk,
    MAX(jp.jam_keluar) as jam_keluar,
    MIN(jp.bulan_mulai) as periode_mulai,
    MAX(jp.bulan_selesai) as periode_selesai
FROM users u
LEFT JOIN jadwal_presensi jp ON u.id = jp.user_id AND jp.is_active = 1
LEFT JOIN kelas k ON u.kelas_id = k.id
WHERE u.role = "student" AND u.status = "active"
GROUP BY u.id, u.nama_lengkap, u.institusi, u.kelas_id, k.nama_kelas');
$studentSummary = $db->resultSet();

// Apply filters to summary
if($searchNama || $searchInstitusi || $searchKelas || $filterStatus) {
    $studentSummary = array_filter($studentSummary, function($s) use ($searchNama, $searchInstitusi, $searchKelas, $filterStatus) {
        $matchNama = empty($searchNama) || stripos($s['nama_lengkap'], $searchNama) !== false;
        $matchInstitusi = empty($searchInstitusi) || stripos($s['institusi'], $searchInstitusi) !== false;
        $matchKelas = empty($searchKelas) || $s['kelas_id'] == $searchKelas;
        
        // Filter by schedule status
        $matchStatus = true;
        if($filterStatus === 'sudah') {
            $matchStatus = $s['total_jadwal'] > 0;
        } elseif($filterStatus === 'belum') {
            $matchStatus = $s['total_jadwal'] == 0;
        }
        
        return $matchNama && $matchInstitusi && $matchKelas && $matchStatus;
    });
}

// Sorting
usort($studentSummary, function($a, $b) use ($sortBy) {
    switch($sortBy) {
        case 'institusi':
            return strcmp($a['institusi'] ?? '', $b['institusi'] ?? '') ?: strcmp($a['nama_lengkap'], $b['nama_lengkap']);
        case 'kelas':
            return strcmp($a['nama_kelas'] ?? '', $b['nama_kelas'] ?? '') ?: strcmp($a['nama_lengkap'], $b['nama_lengkap']);
        default:
            return strcmp($a['nama_lengkap'], $b['nama_lengkap']);
    }
});

// Get detail jadwal for a specific student if requested
$detailStudentId = $_GET['detail'] ?? null;
$detailStudent = null;
$detailJadwal = [];

if($detailStudentId) {
    $db->query('SELECT u.*, k.nama_kelas FROM users u LEFT JOIN kelas k ON u.kelas_id = k.id WHERE u.id = :id');
    $db->bind(':id', $detailStudentId);
    $detailStudent = $db->single();
    
    if($detailStudent) {
        $db->query('SELECT * FROM jadwal_presensi 
                   WHERE user_id = :user_id AND is_active = 1 
                   ORDER BY FIELD(hari, "Senin","Selasa","Rabu","Kamis","Jumat","Sabtu","Minggu")');
        $db->bind(':user_id', $detailStudentId);
        $detailJadwal = $db->resultSet();
    }
}

// Get all students for the "Pilih Siswa" form at bottom
$query = 'SELECT 
    u.id, u.nama_lengkap, u.institusi, u.kelas_id,
    k.nama_kelas
FROM users u
LEFT JOIN kelas k ON u.kelas_id = k.id
WHERE u.role = "student" AND u.status = "active"';

$params = [];
if($searchNama) {
    $query .= ' AND u.nama_lengkap LIKE :nama';
    $params[':nama'] = '%' . $searchNama . '%';
}
if($searchInstitusi) {
    $query .= ' AND u.institusi LIKE :institusi';
    $params[':institusi'] = '%' . $searchInstitusi . '%';
}
if($searchKelas) {
    $query .= ' AND u.kelas_id = :kelas';
    $params[':kelas'] = $searchKelas;
}

switch($sortBy) {
    case 'institusi':
        $query .= ' ORDER BY u.institusi ASC, u.nama_lengkap ASC';
        break;
    case 'kelas':
        $query .= ' ORDER BY k.nama_kelas ASC, u.nama_lengkap ASC';
        break;
    default:
        $query .= ' ORDER BY u.nama_lengkap ASC';
}

$db->query($query);
foreach($params as $key => $val) {
    $db->bind($key, $val);
}
$studentsForForm = $db->resultSet();

include 'includes/header.php';
?>

<div class="container-fluid py-4">
    <h1 class="h3 mb-4 text-gray-800">
        <i class="fas fa-calendar-alt me-2"></i>Pengaturan Jadwal Presensi
    </h1>

    <!-- Success/Error Messages -->
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

    <?php if($detailStudentId && $detailStudent): ?>
    <!-- Back button -->
    <div class="mb-3">
        <a href="jadwal_presensi.php?nama=<?= urlencode($searchNama) ?>&institusi=<?= urlencode($searchInstitusi) ?>&kelas=<?= $searchKelas ?>&status_jadwal=<?= $filterStatus ?>" class="btn btn-outline-primary">
            <i class="fas fa-arrow-left me-2"></i>Kembali ke Daftar
        </a>
    </div>

    <!-- Detail View -->
    <div class="card shadow mb-4">
        <div class="card-header py-3 bg-info text-white">
            <h6 class="m-0 font-weight-bold">
                <i class="fas fa-user-clock me-2"></i>Detail Jadwal: <?= htmlspecialchars($detailStudent['nama_lengkap']) ?>
            </h6>
        </div>
        <div class="card-body">
            <div class="row mb-4">
                <div class="col-md-6">
                    <p><strong>Nama:</strong> <?= htmlspecialchars($detailStudent['nama_lengkap']) ?></p>
                    <p><strong>Institusi:</strong> <?= htmlspecialchars($detailStudent['institusi'] ?? '-') ?></p>
                    <p><strong>Kelas:</strong> <?= htmlspecialchars($detailStudent['nama_kelas'] ?? '-') ?></p>
                </div>
                <div class="col-md-6">
                    <p><strong>Total Jadwal:</strong> <span class="badge bg-primary"><?= count($detailJadwal) ?> hari</span></p>
                </div>
            </div>

            <?php if(empty($detailJadwal)): ?>
                <div class="alert alert-warning">
                    <i class="fas fa-exclamation-triangle me-2"></i>Belum ada jadwal untuk siswa ini.
                </div>
            <?php else: ?>
                <h6 class="mb-3">Jadwal Harian:</h6>
                <div class="table-responsive">
                    <table class="table table-bordered table-hover">
                        <thead class="table-light">
                            <tr>
                                <th>Hari</th>
                                <th>Jam Masuk</th>
                                <th>Jam Keluar</th>
                                <th>Periode</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach($detailJadwal as $j): ?>
                            <tr>
                                <td><span class="badge bg-info"><?= $j['hari'] ?></span></td>
                                <td><?= $j['jam_masuk'] ?></td>
                                <td><?= $j['jam_keluar'] ?></td>
                                <td><?= date('M Y', strtotime($j['bulan_mulai'])) ?> - <?= date('M Y', strtotime($j['bulan_selesai'])) ?></td>
                                <td>
                                    <form method="POST" action="" class="d-inline" onsubmit="return confirm('Yakin hapus jadwal ini?')">
                                        <input type="hidden" name="action" value="delete_jadwal_student">
                                        <input type="hidden" name="user_id" value="<?= $detailStudentId ?>">
                                        <input type="hidden" name="bulan_mulai" value="<?= $j['bulan_mulai'] ?>">
                                        <input type="hidden" name="bulan_selesai" value="<?= $j['bulan_selesai'] ?>">
                                        <button type="submit" class="btn btn-danger btn-sm">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </form>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            <?php endif; ?>
        </div>
    </div>

    <?php else: ?>
    <!-- Unified Form for Schedule Settings and Student Selection -->
    <form method="POST" action="">
        <input type="hidden" name="action" value="save_jadwal">
        
        <!-- Schedule Settings Card -->
        <div class="card shadow mb-4">
            <div class="card-header py-3 bg-primary text-white">
                <h6 class="m-0 font-weight-bold">
                    <i class="fas fa-cog me-2"></i>Pengaturan Jadwal Presensi
                </h6>
            </div>
            <div class="card-body">
                <!-- Schedule Settings -->
                <div class="row">
                    <div class="col-md-6">
                        <label class="form-label fw-bold">
                            <i class="fas fa-calendar-week me-1"></i>Hari Presensi
                        </label>
                        <div class="mb-3">
                            <?php 
                            $hariList = ['Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu', 'Minggu'];
                            foreach($hariList as $hari): 
                            ?>
                            <div class="form-check form-check-inline">
                                <input class="form-check-input" type="checkbox" name="hari[]" value="<?= $hari ?>" id="hari_<?= $hari ?>">
                                <label class="form-check-label" for="hari_<?= $hari ?>"><?= $hari ?></label>
                            </div>
                            <?php endforeach; ?>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="row">
                            <div class="col-md-6 mb-2">
                                <label class="form-label">Jam Masuk</label>
                                <input type="time" name="jam_masuk" class="form-control" value="08:00" required>
                            </div>
                            <div class="col-md-6 mb-2">
                                <label class="form-label">Jam Keluar</label>
                                <input type="time" name="jam_keluar" class="form-control" value="16:00" required>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Period Settings -->
                <div class="row mt-3">
                    <div class="col-md-6">
                        <label class="form-label">Periode Bulan Mulai</label>
                        <input type="month" name="bulan_mulai" class="form-control" value="<?= date('Y-m') ?>" required>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Periode Bulan Selesai</label>
                        <input type="month" name="bulan_selesai" class="form-control" value="<?= date('Y-m', strtotime('+1 month')) ?>" required>
                    </div>
                </div>
            </div>
        </div>

        <!-- Unified Student List with Checkboxes -->
        <div class="card shadow mb-4">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-primary">
                    <i class="fas fa-users me-2"></i>Daftar Siswa - Pilih untuk Atur Jadwal
                    <small class="text-muted ms-2">(<span id="selectedCount">0</span> siswa dipilih)</small>
                </h6>
            </div>
            <div class="card-body">
                <!-- Search Filters -->
                <div class="row mb-4">
                    <div class="col-md-3 mb-2">
                        <label class="form-label">Cari Nama</label>
                        <input type="text" name="filter_nama" id="filterNama" class="form-control" value="<?= htmlspecialchars($searchNama) ?>" placeholder="Nama siswa..." onkeyup="filterTable()">
                    </div>
                    <div class="col-md-3 mb-2">
                        <label class="form-label">Cari Mitra/Institusi</label>
                        <input type="text" name="filter_institusi" id="filterInstitusi" class="form-control" value="<?= htmlspecialchars($searchInstitusi) ?>" placeholder="Nama mitra..." onkeyup="filterTable()">
                    </div>
                    <div class="col-md-2 mb-2">
                        <label class="form-label">Kelas</label>
                        <select name="filter_kelas" id="filterKelas" class="form-select" onchange="filterTable()">
                            <option value="">Semua</option>
                            <?php foreach($kelasList as $k): ?>
                            <option value="<?= htmlspecialchars($k['nama_kelas']) ?>" <?= $searchKelas == $k['id'] ? 'selected' : '' ?>>
                                <?= htmlspecialchars($k['nama_kelas']) ?>
                            </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="col-md-2 mb-2">
                        <label class="form-label">Status Jadwal</label>
                        <select name="filter_status" id="filterStatus" class="form-select" onchange="filterTable()">
                            <option value="" <?= $filterStatus == '' ? 'selected' : '' ?>>Semua</option>
                            <option value="sudah" <?= $filterStatus == 'sudah' ? 'selected' : '' ?>>Sudah Diatur</option>
                            <option value="belum" <?= $filterStatus == 'belum' ? 'selected' : '' ?>>Belum Diatur</option>
                        </select>
                    </div>
                    <div class="col-md-2 mb-2 d-flex align-items-end">
                        <a href="jadwal_presensi.php" class="btn btn-secondary w-100">
                            <i class="fas fa-undo me-1"></i>Reset
                        </a>
                    </div>
                </div>

                <!-- Student List Table with Checkboxes -->
                <div class="table-responsive" style="max-height: 450px; overflow-y: auto;">
                    <table class="table table-bordered table-hover" id="studentTable">
                        <thead class="table-light sticky-top">
                            <tr>
                                <th width="40">
                                    <input type="checkbox" class="form-check-input" id="selectAll" onclick="toggleSelectAll()">
                                </th>
                                <th>No</th>
                                <th>Nama Siswa</th>
                                <th>Institusi</th>
                                <th>Kelas</th>
                                <th>Hari Presensi</th>
                                <th>Jam</th>
                                <th>Status</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php $no = 1; foreach($studentSummary as $student): ?>
                            <tr class="student-row" 
                                data-nama="<?= strtolower(htmlspecialchars($student['nama_lengkap'])) ?>"
                                data-institusi="<?= strtolower(htmlspecialchars($student['institusi'] ?? '')) ?>"
                                data-kelas="<?= htmlspecialchars($student['nama_kelas'] ?? '') ?>"
                                data-status="<?= $student['total_jadwal'] > 0 ? 'sudah' : 'belum' ?>">
                                <td>
                                    <input type="checkbox" class="form-check-input student-check" 
                                           name="selected_students[]" value="<?= $student['id'] ?>"
                                           data-nama="<?= htmlspecialchars($student['nama_lengkap']) ?>">
                                </td>
                                <td><?= $no++ ?></td>
                                <td><?= htmlspecialchars($student['nama_lengkap']) ?></td>
                                <td><?= htmlspecialchars($student['institusi'] ?? '-') ?></td>
                                <td><?= htmlspecialchars($student['nama_kelas'] ?? '-') ?></td>
                                <td>
                                    <?php if($student['total_jadwal'] > 0): ?>
                                        <?php 
                                        $hariArray = explode(', ', $student['hari_list']);
                                        foreach($hariArray as $hari): 
                                        ?>
                                        <span class="badge bg-info me-1"><?= trim($hari) ?></span>
                                        <?php endforeach; ?>
                                    <?php else: ?>
                                        <span class="text-muted">-</span>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <?php if($student['total_jadwal'] > 0): ?>
                                        <?= substr($student['jam_masuk'], 0, 5) ?> - <?= substr($student['jam_keluar'], 0, 5) ?>
                                    <?php else: ?>
                                        <span class="text-muted">-</span>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <?php if($student['total_jadwal'] > 0): ?>
                                        <span class="badge bg-success">Sudah diatur</span>
                                    <?php else: ?>
                                        <span class="badge bg-warning text-dark">Belum diatur</span>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <a href="jadwal_presensi.php?detail=<?= $student['id'] ?>&nama=<?= urlencode($searchNama) ?>&institusi=<?= urlencode($searchInstitusi) ?>&kelas=<?= $searchKelas ?>&status_jadwal=<?= $filterStatus ?>" class="btn btn-primary btn-sm">
                                        <i class="fas fa-eye me-1"></i>Detail
                                    </a>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                            <?php if(empty($studentSummary)): ?>
                            <tr>
                                <td colspan="9" class="text-center text-muted py-4">
                                    <i class="fas fa-inbox fa-3x mb-3"></i><br>
                                    Tidak ada data siswa
                                </td>
                            </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
                
                <!-- Submit Button -->
                <div class="mt-4 d-flex justify-content-between align-items-center">
                    <small class="text-muted">
                        <i class="fas fa-info-circle me-1"></i>Centang siswa yang ingin diatur jadwalnya, lalu klik "Simpan Jadwal"
                    </small>
                    <div>
                        <button type="reset" class="btn btn-secondary btn-lg me-2" onclick="resetSelection()">
                            <i class="fas fa-undo me-2"></i>Reset Pilihan
                        </button>
                        <button type="submit" class="btn btn-primary btn-lg">
                            <i class="fas fa-save me-2"></i>Simpan Jadwal
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </form>
    <?php endif; ?>

</div>

<script>
function toggleSelectAll() {
    const selectAll = document.getElementById('selectAll');
    const checkboxes = document.querySelectorAll('.student-check');
    checkboxes.forEach(cb => cb.checked = selectAll.checked);
    updateSelectedCount();
}

function updateSelectedCount() {
    const checked = document.querySelectorAll('.student-check:checked');
    document.getElementById('selectedCount').textContent = checked.length;
}

function resetSelection() {
    document.getElementById('selectAll').checked = false;
    document.querySelectorAll('.student-check').forEach(cb => cb.checked = false);
    updateSelectedCount();
}

function filterTable() {
    const namaFilter = document.getElementById('filterNama').value.toLowerCase();
    const institusiFilter = document.getElementById('filterInstitusi').value.toLowerCase();
    const kelasFilter = document.getElementById('filterKelas').value;
    const statusFilter = document.getElementById('filterStatus').value;
    
    const rows = document.querySelectorAll('.student-row');
    let visibleCount = 0;
    
    rows.forEach(row => {
        const nama = row.getAttribute('data-nama');
        const institusi = row.getAttribute('data-institusi');
        const kelas = row.getAttribute('data-kelas');
        const status = row.getAttribute('data-status');
        
        const matchNama = nama.includes(namaFilter);
        const matchInstitusi = institusi.includes(institusiFilter);
        const matchKelas = !kelasFilter || kelas === kelasFilter;
        const matchStatus = !statusFilter || status === statusFilter;
        
        if (matchNama && matchInstitusi && matchKelas && matchStatus) {
            row.style.display = '';
            visibleCount++;
        } else {
            row.style.display = 'none';
        }
    });
}

// Update count on individual checkbox change
document.querySelectorAll('.student-check').forEach(cb => {
    cb.addEventListener('change', updateSelectedCount);
});
</script>

<?php include 'includes/footer.php'; ?>
