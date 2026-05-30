<?php
require_once 'config/config.php';
requireAdmin();

$db = new Database();

// Get filter parameters
$filterMonth = $_GET['month'] ?? date('Y-m');
$filterYear = date('Y', strtotime($filterMonth));
$filterMonthNum = date('m', strtotime($filterMonth));
$searchNama = $_GET['nama'] ?? '';
$searchInstitusi = $_GET['institusi'] ?? '';
$searchKelas = $_GET['kelas'] ?? '';

// Get all kelas for dropdown
$db->query('SELECT * FROM kelas ORDER BY nama_kelas ASC');
$kelasList = $db->resultSet();

// Build query with search filters
$query = 'SELECT 
    u.id,
    u.nama_lengkap,
    u.institusi,
    u.kelas_id,
    u.jurusan_id,
    k.nama_kelas,
    j.nama_jurusan,
    COUNT(CASE WHEN p.status = "hadir" THEN 1 END) as total_hadir,
    COUNT(CASE WHEN p.status = "sakit" THEN 1 END) as total_sakit,
    COUNT(CASE WHEN p.status = "izin" THEN 1 END) as total_izin,
    COUNT(CASE WHEN p.status = "alpha" THEN 1 END) as total_alpha,
    COUNT(p.id) as total_presensi
FROM users u
LEFT JOIN presensi p ON u.id = p.user_id AND DATE_FORMAT(p.tanggal, "%Y-%m") = :month
LEFT JOIN jurusan j ON u.jurusan_id = j.id
LEFT JOIN kelas k ON u.kelas_id = k.id
WHERE u.role = "student" AND u.status = "active"';

$params = [':month' => $filterMonth];

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

$query .= ' GROUP BY u.id, u.nama_lengkap, u.institusi, u.kelas_id, k.nama_kelas, u.jurusan_id, j.nama_jurusan
ORDER BY u.nama_lengkap ASC';

$db->query($query);
foreach($params as $key => $val) {
    $db->bind($key, $val);
}
$studentSummary = $db->resultSet();

// Get filter parameters for detail view
$detailStudentId = $_GET['detail'] ?? null;
$detailStudent = null;
$detailPresensi = [];

if($detailStudentId) {
    // Get student info with internship dates
    $db->query('SELECT u.*, j.nama_jurusan FROM users u LEFT JOIN jurusan j ON u.jurusan_id = j.id WHERE u.id = :id');
    $db->bind(':id', $detailStudentId);
    $detailStudent = $db->single();
    
    // Auto-generate alpha for missing days within internship period
    if($detailStudent && $detailStudent['tanggal_mulai'] && $detailStudent['tanggal_selesai']) {
        $startDate = new DateTime($detailStudent['tanggal_mulai']);
        $endDate = new DateTime($detailStudent['tanggal_selesai']);
        $today = new DateTime();
        
        // Only check up to today or end date, whichever is earlier
        $checkEndDate = $today < $endDate ? $today : $endDate;
        
        // Get all existing presensi dates for this student
        $db->query('SELECT tanggal FROM presensi WHERE user_id = :user_id AND tanggal BETWEEN :start AND :end');
        $db->bind(':user_id', $detailStudentId);
        $db->bind(':start', $startDate->format('Y-m-d'));
        $db->bind(':end', $checkEndDate->format('Y-m-d'));
        $existingDates = $db->resultSet();
        $existingDateMap = array_column($existingDates, 'tanggal', 'tanggal');
        
        // Loop through each day and create alpha if no record exists
        $currentDate = clone $startDate;
        while($currentDate <= $checkEndDate) {
            $dateStr = $currentDate->format('Y-m-d');
            $dayOfWeek = $currentDate->format('N'); // 1=Monday, 7=Sunday
            
            // Skip weekends (Saturday=6, Sunday=7)
            if($dayOfWeek < 6 && !isset($existingDateMap[$dateStr])) {
                // Create alpha record
                $db->query('INSERT INTO presensi (user_id, tanggal, status, keterangan, created_at) 
                           VALUES (:user_id, :tanggal, "alpha", "Otomatis - Tidak ada kehadiran", NOW())');
                $db->bind(':user_id', $detailStudentId);
                $db->bind(':tanggal', $dateStr);
                $db->execute();
            }
            $currentDate->modify('+1 day');
        }
    }
    
    // Get all presensi for this student in selected month (including auto-generated alpha)
    $db->query('SELECT * FROM presensi WHERE user_id = :user_id AND DATE_FORMAT(tanggal, "%Y-%m") = :month ORDER BY tanggal ASC');
    $db->bind(':user_id', $detailStudentId);
    $db->bind(':month', $filterMonth);
    $detailPresensi = $db->resultSet();
}

include 'includes/header.php';

// Helper function to get color class for status
function getStatusColor($status) {
    return match($status) {
        'hadir' => 'success',
        'sakit' => 'danger',
        'izin' => 'warning',
        default => 'secondary'
    };
}

// Helper function to get status label
function getStatusLabel($status) {
    return match($status) {
        'hadir' => 'Hadir',
        'sakit' => 'Sakit',
        'izin' => 'Izin',
        default => 'Alpha'
    };
}
?>

<div class="container-fluid py-4">
    <h1 class="h3 mb-4 text-gray-800">Laporan Presensi Siswa</h1>

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
            <form method="GET" action="" class="row align-items-end">
                <div class="col-md-3 mb-3">
                    <label class="form-label">Bulan</label>
                    <input type="month" name="month" class="form-control" value="<?= $filterMonth ?>">
                </div>
                <div class="col-md-3 mb-3">
                    <label class="form-label">Nama Siswa</label>
                    <input type="text" name="nama" class="form-control" value="<?= htmlspecialchars($searchNama) ?>" placeholder="Cari nama...">
                </div>
                <div class="col-md-2 mb-3">
                    <label class="form-label">Tempat Magang</label>
                    <input type="text" name="institusi" class="form-control" value="<?= htmlspecialchars($searchInstitusi) ?>" placeholder="Cari institusi...">
                </div>
                <div class="col-md-2 mb-3">
                    <label class="form-label">Kelas</label>
                    <select name="kelas" class="form-select">
                        <option value="">Semua Kelas</option>
                        <?php foreach($kelasList as $k): ?>
                        <option value="<?= $k['id'] ?>" <?= $searchKelas == $k['id'] ? 'selected' : '' ?>>
                            <?= htmlspecialchars($k['nama_kelas']) ?>
                        </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="col-md-2 mb-3">
                    <button type="submit" class="btn btn-primary me-2">
                        <i class="fas fa-search me-2"></i>Cari
                    </button>
                    <a href="presensi_report.php<?= $detailStudentId ? '?detail='.$detailStudentId : '' ?>" class="btn btn-secondary">Reset</a>
                </div>
            </form>
        </div>
    </div>

    <?php if($detailStudentId && $detailStudent): ?>
    <!-- Back button for detail view -->
    <div class="mb-3">
        <a href="presensi_report.php?month=<?= $filterMonth ?>&nama=<?= urlencode($searchNama) ?>&institusi=<?= urlencode($searchInstitusi) ?>&kelas=<?= $searchKelas ?>" class="btn btn-outline-primary">
            <i class="fas fa-arrow-left me-2"></i>Kembali ke Daftar
        </a>
    </div>
    
    <!-- Detail View - Calendar Style -->
    <div class="card shadow mb-4">
        <div class="card-header py-3 d-flex justify-content-between align-items-center">
            <div>
                <h6 class="m-0 font-weight-bold text-primary">
                    <i class="fas fa-calendar-alt me-2"></i>Detail Presensi
                </h6>
                <small class="text-muted">
                    <?= htmlspecialchars($detailStudent['nama_lengkap']) ?> - <?= htmlspecialchars($detailStudent['institusi']) ?>
                    <?= $detailStudent['nama_jurusan'] ? '('.htmlspecialchars($detailStudent['nama_jurusan']).')' : '' ?>
                </small>
            </div>
            <button class="btn btn-success btn-sm" onclick="window.print()">
                <i class="fas fa-print me-2"></i>Cetak
            </button>
        </div>
        <div class="card-body">
            <!-- Summary Stats -->
            <?php
            $stats = ['hadir' => 0, 'sakit' => 0, 'izin' => 0, 'alpha' => 0];
            foreach($detailPresensi as $p) {
                $stats[$p['status']]++;
            }
            ?>
            <div class="row mb-4">
                <div class="col-md-3 col-6 mb-2">
                    <div class="card bg-success text-white">
                        <div class="card-body text-center py-2">
                            <h4 class="mb-0"><?= $stats['hadir'] ?></h4>
                            <small>Hadir</small>
                        </div>
                    </div>
                </div>
                <div class="col-md-3 col-6 mb-2">
                    <div class="card bg-danger text-white">
                        <div class="card-body text-center py-2">
                            <h4 class="mb-0"><?= $stats['sakit'] ?></h4>
                            <small>Sakit</small>
                        </div>
                    </div>
                </div>
                <div class="col-md-3 col-6 mb-2">
                    <div class="card bg-warning text-dark">
                        <div class="card-body text-center py-2">
                            <h4 class="mb-0"><?= $stats['izin'] ?></h4>
                            <small>Izin</small>
                        </div>
                    </div>
                </div>
                <div class="col-md-3 col-6 mb-2">
                    <div class="card bg-secondary text-white">
                        <div class="card-body text-center py-2">
                            <h4 class="mb-0"><?= $stats['alpha'] ?></h4>
                            <small>Alpha</small>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Calendar Grid -->
            <div class="calendar-container">
                <h6 class="mb-3">Kalender Presensi - <?= date('F Y', strtotime($filterMonth)) ?></h6>
                
                <!-- Day Headers -->
                <div class="row g-1 mb-1">
                    <?php $days = ['Min', 'Sen', 'Sel', 'Rab', 'Kam', 'Jum', 'Sab']; ?>
                    <?php foreach($days as $day): ?>
                    <div class="col">
                        <div class="text-center py-1 bg-light border fw-bold small"><?= $day ?></div>
                    </div>
                    <?php endforeach; ?>
                </div>
                
                <!-- Calendar Days -->
                <?php
                $firstDay = strtotime("$filterYear-$filterMonthNum-01");
                $daysInMonth = date('t', $firstDay);
                $startDayOfWeek = date('w', $firstDay); // 0 = Sunday
                
                // Create presensi lookup array
                $presensiByDate = [];
                foreach($detailPresensi as $p) {
                    $presensiByDate[$p['tanggal']] = $p;
                }
                
                $dayCount = 1;
                $totalCells = ceil(($daysInMonth + $startDayOfWeek) / 7) * 7;
                ?>
                <div class="row g-1">
                    <?php for($i = 0; $i < $totalCells; $i++): ?>
                        <?php if($i < $startDayOfWeek || $dayCount > $daysInMonth): ?>
                        <div class="col">
                            <div class="calendar-day border bg-light" style="min-height: 80px;"></div>
                        </div>
                        <?php else: 
                            $currentDate = sprintf("%04d-%02d-%02d", $filterYear, $filterMonthNum, $dayCount);
                            $hasPresensi = isset($presensiByDate[$currentDate]);
                            $status = $hasPresensi ? $presensiByDate[$currentDate]['status'] : null;
                            $bgClass = $status ? 'bg-' . getStatusColor($status) : 'bg-white';
                            $textClass = $status ? 'text-white' : 'text-dark';
                        ?>
                        <div class="col">
                            <div class="calendar-day border <?= $bgClass ?> <?= $textClass ?> p-1" 
                                 style="min-height: 80px; cursor: <?= $hasPresensi ? 'pointer' : 'default' ?>;"
                                 title="<?= $hasPresensi ? getStatusLabel($status) . ' - ' . ($presensiByDate[$currentDate]['jam_masuk'] ?: '-') . ' s/d ' . ($presensiByDate[$currentDate]['jam_keluar'] ?: '-') : 'Tidak ada data' ?>">
                                <div class="fw-bold small"><?= $dayCount ?></div>
                                <?php if($hasPresensi): ?>
                                <div class="mt-1">
                                    <span class="badge bg-white text-dark small"><?= strtoupper(substr(getStatusLabel($status), 0, 1)) ?></span>
                                    <div class="small mt-1" style="font-size: 0.7rem;">
                                        <?= $presensiByDate[$currentDate]['jam_masuk'] ?: '-' ?><br>
                                        <?= $presensiByDate[$currentDate]['jam_keluar'] ?: '-' ?>
                                    </div>
                                </div>
                                <?php endif; ?>
                            </div>
                        </div>
                        <?php 
                        $dayCount++;
                        endif; 
                        ?>
                        <?php if(($i + 1) % 7 === 0): ?></div><div class="row g-1"><?php endif; ?>
                    <?php endfor; ?>
                </div>
            </div>

            <!-- Legend -->
            <div class="mt-4">
                <h6 class="mb-2">Keterangan:</h6>
                <div class="d-flex flex-wrap gap-3">
                    <span class="badge bg-success">&nbsp;</span> Hadir
                    <span class="badge bg-danger">&nbsp;</span> Sakit
                    <span class="badge bg-warning">&nbsp;</span> Izin
                    <span class="badge bg-secondary">&nbsp;</span> Alpha
                </div>
            </div>
        </div>
    </div>
    <?php else: ?>
    <!-- Summary View - List of Students -->
    <div class="card shadow">
        <div class="card-header py-3 d-flex justify-content-between align-items-center">
            <h6 class="m-0 font-weight-bold text-primary">
                <i class="fas fa-users me-2"></i>Rekap Presensi Siswa - <?= date('F Y', strtotime($filterMonth)) ?>
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
                            <th>Tempat Magang</th>
                            <th>Kelas</th>
                            <th class="text-center">Hadir</th>
                            <th class="text-center">Sakit</th>
                            <th class="text-center">Izin</th>
                            <th class="text-center">Alpha</th>
                            <th class="text-center">Total</th>
                            <th width="120">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php $no = 1; foreach($studentSummary as $student): ?>
                        <tr>
                            <td><?= $no++ ?></td>
                            <td><?= htmlspecialchars($student['nama_lengkap']) ?></td>
                            <td><?= htmlspecialchars($student['institusi']) ?></td>
                            <td><?= htmlspecialchars($student['nama_kelas'] ?? '-') ?></td>
                            <td class="text-center">
                                <span class="badge bg-success"><?= $student['total_hadir'] ?></span>
                            </td>
                            <td class="text-center">
                                <span class="badge bg-danger"><?= $student['total_sakit'] ?></span>
                            </td>
                            <td class="text-center">
                                <span class="badge bg-warning"><?= $student['total_izin'] ?></span>
                            </td>
                            <td class="text-center">
                                <span class="badge bg-secondary"><?= $student['total_alpha'] ?></span>
                            </td>
                            <td class="text-center">
                                <strong><?= $student['total_presensi'] ?></strong>
                            </td>
                            <td>
                                <a href="presensi_report.php?month=<?= $filterMonth ?>&detail=<?= $student['id'] ?>" class="btn btn-primary btn-sm w-100">
                                    <i class="fas fa-eye me-1"></i>Detail
                                </a>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                        <?php if(empty($studentSummary)): ?>
                        <tr>
                            <td colspan="9" class="text-center text-muted">
                                <i class="fas fa-inbox fa-3x mb-3"></i><br>
                                Tidak ada data siswa aktif
                            </td>
                        </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    <?php endif; ?>
</div>

<style>
.calendar-day {
    transition: transform 0.2s;
}
.calendar-day:hover {
    transform: scale(1.05);
    z-index: 1;
    box-shadow: 0 2px 4px rgba(0,0,0,0.2);
}
</style>

<?php include 'includes/footer.php'; ?>
