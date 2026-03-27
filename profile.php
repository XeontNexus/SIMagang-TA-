<?php
require_once 'config/config.php';
requireAuth();

$db = new Database();
$user_id = $_SESSION['user_id'];

// Get user data with jurusan, kelas and guru
$db->query('SELECT u.*, k.nama_kelas as kelas_nama, j.nama_jurusan, g.nama_guru as guru_nama FROM users u 
            LEFT JOIN kelas k ON u.kelas_id = k.id 
            LEFT JOIN jurusan j ON u.jurusan_id = j.id 
            LEFT JOIN guru_pembimbing g ON u.guru_pembimbing_id = g.id 
            WHERE u.id = :id');
$db->bind(':id', $user_id);
$user = $db->single();

// Handle profile update
if($_SERVER['REQUEST_METHOD'] == 'POST') {
    $action = $_POST['action'];
    
    if($action === 'update_profile') {
        $nama_lengkap = $_POST['nama_lengkap'];
        $email = $_POST['email'];
        $no_hp = $_POST['no_hp'];
        $guru_pembimbing_id = $_POST['guru_pembimbing_id'] ?? null;
        $kelas_id = $_POST['kelas_id'] ?? null;
        $jurusan_id = $_POST['jurusan_id'] ?? null;
        $gmap_link = $_POST['gmap_link'] ?? '';
        $pendamping_lapangan = $_POST['pendamping_lapangan'] ?? '';
        $telp_pendamping = $_POST['telp_pendamping'] ?? '';
        
        $alamat_magang = $_POST['alamat_magang'] ?? '';
        $alamat_sekolah = $_POST['alamat_sekolah'] ?? '';
        $tanggal_mulai = $_POST['tanggal_mulai'] ?? null;
        $tanggal_selesai = $_POST['tanggal_selesai'] ?? null;
        
        if ($user['role'] === 'admin') {
            $db->query('UPDATE users SET nama_lengkap = :nama_lengkap, email = :email, no_hp = :no_hp, 
                        alamat_sekolah = :alamat_sekolah 
                        WHERE id = :id');
            $db->bind(':alamat_sekolah', $alamat_sekolah);
        } else {
            $db->query('UPDATE users SET nama_lengkap = :nama_lengkap, email = :email, no_hp = :no_hp, 
                        guru_pembimbing_id = :guru_pembimbing_id, kelas_id = :kelas_id, jurusan_id = :jurusan_id,
                        gmap_link = :gmap_link, alamat_magang = :alamat_magang, pendamping_lapangan = :pendamping_lapangan, telp_pendamping = :telp_pendamping,
                        tanggal_mulai = :tanggal_mulai, tanggal_selesai = :tanggal_selesai 
                        WHERE id = :id');
            $db->bind(':guru_pembimbing_id', $guru_pembimbing_id);
            $db->bind(':kelas_id', $kelas_id);
            $db->bind(':jurusan_id', $jurusan_id);
            $db->bind(':gmap_link', $gmap_link);
            $db->bind(':alamat_magang', $alamat_magang);
            $db->bind(':pendamping_lapangan', $pendamping_lapangan);
            $db->bind(':telp_pendamping', $telp_pendamping);
            $db->bind(':tanggal_mulai', $tanggal_mulai);
            $db->bind(':tanggal_selesai', $tanggal_selesai);
        }
        $db->bind(':nama_lengkap', $nama_lengkap);
        $db->bind(':email', $email);
        $db->bind(':no_hp', $no_hp);
        $db->bind(':id', $user_id);
        
        if($db->execute()) {
            $_SESSION['nama_lengkap'] = $nama_lengkap;
            $_SESSION['email'] = $email;
            $_SESSION['success'] = 'Profil berhasil diperbarui!';
            redirect(base_url('profile.php'));
        } else {
            $error = 'Terjadi kesalahan saat memperbarui profil!';
        }
    } elseif($action === 'change_password') {
        $current_password = $_POST['current_password'];
        $new_password = $_POST['new_password'];
        $confirm_password = $_POST['confirm_password'];
        
        if(!password_verify($current_password, $user['password'])) {
            $error = 'Password saat ini tidak cocok!';
        } elseif($new_password !== $confirm_password) {
            $error = 'Password baru dan konfirmasi tidak cocok!';
        } elseif(strlen($new_password) < 6) {
            $error = 'Password baru minimal 6 karakter!';
        } else {
            $hashed_password = password_hash($new_password, PASSWORD_DEFAULT);
            $db->query('UPDATE users SET password = :password WHERE id = :id');
            $db->bind(':password', $hashed_password);
            $db->bind(':id', $user_id);
            
            if($db->execute()) {
                $_SESSION['success'] = 'Password berhasil diubah!';
                redirect(base_url('profile.php'));
            } else {
                $error = 'Terjadi kesalahan saat mengubah password!';
            }
        }
    } elseif($action === 'complete_profile') {
        $no_hp = $_POST['no_hp'];
        $jurusan_id = $_POST['jurusan_id'] ?? null;
        $institusi = $_POST['institusi'];
        $gmap_link = $_POST['gmap_link'] ?? '';
        $alamat_magang = $_POST['alamat_magang'] ?? '';
        $guru_pembimbing = $_POST['guru_pembimbing'] ?? '';
        $kelas_id = $_POST['kelas_id'] ?? null;
        $pendamping_lapangan = $_POST['pendamping_lapangan'] ?? '';
        $telp_pendamping = $_POST['telp_pendamping'] ?? '';
        $tanggal_mulai = $_POST['tanggal_mulai'];
        $tanggal_selesai = $_POST['tanggal_selesai'];
        
        $db->query('UPDATE users SET no_hp = :no_hp, jurusan_id = :jurusan_id, institusi = :institusi, 
                    gmap_link = :gmap_link, alamat_magang = :alamat_magang, guru_pembimbing = :guru_pembimbing, 
                    kelas_id = :kelas_id, pendamping_lapangan = :pendamping_lapangan, telp_pendamping = :telp_pendamping, 
                    tanggal_mulai = :tanggal_mulai, tanggal_selesai = :tanggal_selesai 
                    WHERE id = :id');
        $db->bind(':no_hp', $no_hp);
        $db->bind(':jurusan_id', $jurusan_id);
        $db->bind(':institusi', $institusi);
        $db->bind(':gmap_link', $gmap_link);
        $db->bind(':alamat_magang', $alamat_magang);
        $db->bind(':guru_pembimbing', $guru_pembimbing);
        $db->bind(':kelas_id', $kelas_id);
        $db->bind(':pendamping_lapangan', $pendamping_lapangan);
        $db->bind(':telp_pendamping', $telp_pendamping);
        $db->bind(':tanggal_mulai', $tanggal_mulai);
        $db->bind(':tanggal_selesai', $tanggal_selesai);
        $db->bind(':id', $user_id);
        
        if($db->execute()) {
            $_SESSION['success'] = 'Data profil dan informasi magang berhasil dilengkapi!';
            redirect(base_url('dashboard.php'));
        } else {
            $error = 'Terjadi kesalahan saat menyimpan data!';
        }
    }
}

include 'includes/header.php';
?>

<div class="container-fluid py-4">
    <h1 class="h3 mb-4 text-gray-800">Profil Saya</h1>

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
        <div class="col-lg-4">
            <!-- Profile Card -->
            <div class="card shadow mb-4">
                <div class="card-body text-center">
                    <div class="user-avatar mx-auto mb-3" style="width: 100px; height: 100px; font-size: 2.5rem;">
                        <?= strtoupper(substr($user['nama_lengkap'], 0, 1)) ?>
                    </div>
                    <h5 class="card-title"><?= htmlspecialchars($user['nama_lengkap']) ?></h5>
                    <p class="card-text text-muted">
                        <span class="badge bg-primary"><?= ucfirst($user['role']) ?></span>
                    </p>
                    <p class="card-text">
                        <small class="text-muted">Bergabung: <?= formatTanggalIndo($user['created_at']) ?></small>
                    </p>
                </div>
            </div>
            
            <!-- Role Info Card -->
            <div class="card shadow">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Informasi Akun</h6>
                </div>
                <div class="card-body">
                    <ul class="list-group list-group-flush">
                        <li class="list-group-item d-flex justify-content-between">
                            <span>Username</span>
                            <strong><?= htmlspecialchars($user['username']) ?></strong>
                        </li>
                        <li class="list-group-item d-flex justify-content-between">
                            <span>Email</span>
                            <strong><?= htmlspecialchars($user['email']) ?></strong>
                        </li>
                        <li class="list-group-item d-flex justify-content-between">
                            <span>No. HP</span>
                            <strong><?= htmlspecialchars($user['no_hp'] ?: '-') ?></strong>
                        </li>
                        <?php if($user['role'] === 'student'): ?>
                        <li class="list-group-item d-flex justify-content-between">
                            <span>Jurusan</span>
                            <strong><?= htmlspecialchars($user['nama_jurusan'] ?: '-') ?></strong>
                        </li>
                        <li class="list-group-item d-flex justify-content-between">
                            <span>Kelas</span>
                            <strong><?= htmlspecialchars($user['kelas_nama'] ?: '-') ?></strong>
                        </li>
                        <li class="list-group-item d-flex justify-content-between">
                            <span>Guru Pembimbing</span>
                            <strong><?= htmlspecialchars($user['guru_nama'] ?: '-') ?></strong>
                        </li>
                        <li class="list-group-item d-flex justify-content-between">
                            <span>Pendamping Lapangan</span>
                            <strong><?= htmlspecialchars($user['pendamping_lapangan'] ?: '-') ?></strong>
                        </li>
                        <li class="list-group-item d-flex justify-content-between">
                            <span>Telp Pendamping</span>
                            <strong><?= htmlspecialchars($user['telp_pendamping'] ?: '-') ?></strong>
                        </li>
                        <li class="list-group-item d-flex justify-content-between">
                            <span>Alamat Magang</span>
                            <strong><?= htmlspecialchars($user['alamat_magang'] ?: '-') ?></strong>
                        </li>
                        <?php endif; ?>
                        <?php if($user['role'] === 'admin'): ?>
                        <li class="list-group-item d-flex justify-content-between">
                            <span>Alamat Sekolah</span>
                            <strong><?= htmlspecialchars($user['alamat_sekolah'] ?: '-') ?></strong>
                        </li>
                        <?php endif; ?>
                    </ul>
                    <?php if($user['gmap_link']): ?>
                    <div class="mt-3">
                        <a href="<?= htmlspecialchars($user['gmap_link']) ?>" target="_blank" class="btn btn-outline-primary btn-sm w-100">
                            <i class="fas fa-map-marker-alt me-2"></i>Lihat Lokasi di Google Maps
                        </a>
                    </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
        
        <div class="col-lg-8">
            <!-- Edit Profile Form -->
            <div class="card shadow mb-4">
                <div class="card-header py-3 d-flex justify-content-between align-items-center">
                    <h6 class="m-0 font-weight-bold text-primary">
                        <i class="fas fa-user me-2"></i>Profil
                    </h6>
                    <button type="button" id="btnEditProfile" class="btn btn-primary btn-sm" onclick="enableEditProfile()">
                        <i class="fas fa-edit me-2"></i>Edit Profil
                    </button>
                    <button type="button" id="btnCancelEdit" class="btn btn-secondary btn-sm d-none" onclick="cancelEditProfile()">
                        <i class="fas fa-times me-2"></i>Batal
                    </button>
                </div>
                <div class="card-body">
                    <form method="POST" action="" id="profileForm">
                        <input type="hidden" name="action" value="update_profile">
                        
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Nama Lengkap</label>
                                <input type="text" name="nama_lengkap" class="form-control profile-input" required 
                                       value="<?= htmlspecialchars($user['nama_lengkap']) ?>" disabled>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Email</label>
                                <input type="email" name="email" class="form-control profile-input" required 
                                       value="<?= htmlspecialchars($user['email']) ?>" disabled>
                            </div>
                        </div>
                        
                        <div class="mb-3">
                            <label class="form-label">No. HP</label>
                            <input type="text" name="no_hp" class="form-control profile-input" 
                                   value="<?= htmlspecialchars($user['no_hp'] ?: '') ?>" disabled>
                        </div>
                        
                        <?php if($user['role'] === 'student'): ?>
                        <?php
                        // Get lists for dropdowns
                        $db->query('SELECT * FROM jurusan ORDER BY nama_jurusan ASC');
                        $jurusanList = $db->resultSet();
                        $db->query('SELECT * FROM kelas ORDER BY nama_kelas ASC');
                        $kelasList = $db->resultSet();
                        $db->query('SELECT * FROM guru_pembimbing WHERE status = "active" ORDER BY nama_guru ASC');
                        $guruList = $db->resultSet();
                        
                        // Get current guru info
                        $currentGuru = null;
                        if($user['guru_pembimbing_id']) {
                            $db->query('SELECT * FROM guru_pembimbing WHERE id = :id');
                            $db->bind(':id', $user['guru_pembimbing_id']);
                            $currentGuru = $db->single();
                        }
                        ?>
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Institusi</label>
                                <input type="text" class="form-control" value="<?= htmlspecialchars($user['institusi'] ?: '-') ?>" readonly disabled>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Jurusan</label>
                                <select name="jurusan_id" class="form-select profile-input" disabled>
                                    <option value="">- Pilih Jurusan -</option>
                                    <?php foreach($jurusanList as $j): ?>
                                    <option value="<?= $j['id'] ?>" <?= ($user['jurusan_id'] == $j['id']) ? 'selected' : '' ?>>
                                        <?= htmlspecialchars($j['nama_jurusan']) ?>
                                    </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Link Google Maps Tempat Magang <span class="text-danger">*</span></label>
                            <input type="url" name="gmap_link" class="form-control profile-input" required 
                                   value="<?= htmlspecialchars($user['gmap_link'] ?: '') ?>" placeholder="https://maps.google.com/..." disabled>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Alamat Tempat Magang <span class="text-danger">*</span></label>
                            <textarea name="alamat_magang" class="form-control profile-input" rows="2" required placeholder="Alamat lengkap tempat magang" disabled><?= htmlspecialchars($user['alamat_magang'] ?: '') ?></textarea>
                        </div>
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Guru Pembimbing</label>
                                <select name="guru_pembimbing_id" class="form-select profile-input" disabled>
                                    <option value="">- Pilih Guru Pembimbing -</option>
                                    <?php foreach($guruList as $g): ?>
                                    <option value="<?= $g['id'] ?>" <?= ($user['guru_pembimbing_id'] == $g['id']) ? 'selected' : '' ?>>
                                        <?= htmlspecialchars($g['nama_guru']) ?>
                                    </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Kelas</label>
                                <select name="kelas_id" class="form-select profile-input" disabled>
                                    <option value="">- Pilih Kelas -</option>
                                    <?php foreach($kelasList as $k): ?>
                                    <option value="<?= $k['id'] ?>" <?= ($user['kelas_id'] == $k['id']) ? 'selected' : '' ?>>
                                        <?= htmlspecialchars($k['nama_kelas']) ?>
                                    </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Pendamping Lapangan</label>
                                <input type="text" name="pendamping_lapangan" class="form-control profile-input" 
                                       value="<?= htmlspecialchars($user['pendamping_lapangan'] ?: '') ?>" disabled>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">No. Telp Pendamping</label>
                                <input type="text" name="telp_pendamping" class="form-control profile-input" 
                                       value="<?= htmlspecialchars($user['telp_pendamping'] ?: '') ?>" disabled>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Tanggal Mulai</label>
                                <input type="date" name="tanggal_mulai" class="form-control profile-input" 
                                       value="<?= htmlspecialchars($user['tanggal_mulai'] ?: '') ?>" disabled>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Tanggal Selesai</label>
                                <input type="date" name="tanggal_selesai" class="form-control profile-input" 
                                       value="<?= htmlspecialchars($user['tanggal_selesai'] ?: '') ?>" disabled>
                            </div>
                        </div>
                        <?php endif; ?>
                        
                        <?php if($user['role'] === 'admin'): ?>
                        <div class="mb-3">
                            <label class="form-label">Alamat Sekolah</label>
                            <textarea name="alamat_sekolah" class="form-control profile-input" rows="2" placeholder="Alamat sekolah/institusi" disabled><?= htmlspecialchars($user['alamat_sekolah'] ?? '') ?></textarea>
                        </div>
                        <?php endif; ?>
                        
                        <button type="submit" id="btnSaveProfile" class="btn btn-success d-none">
                            <i class="fas fa-save me-2"></i>Simpan Perubahan
                        </button>
                    </form>
                    
                    <script>
                        function enableEditProfile() {
                            const inputs = document.querySelectorAll('.profile-input');
                            inputs.forEach(input => input.disabled = false);
                            
                            document.getElementById('btnEditProfile').classList.add('d-none');
                            document.getElementById('btnCancelEdit').classList.remove('d-none');
                            document.getElementById('btnSaveProfile').classList.remove('d-none');
                        }
                        
                        function cancelEditProfile() {
                            const inputs = document.querySelectorAll('.profile-input');
                            inputs.forEach(input => input.disabled = true);
                            
                            document.getElementById('btnEditProfile').classList.remove('d-none');
                            document.getElementById('btnCancelEdit').classList.add('d-none');
                            document.getElementById('btnSaveProfile').classList.add('d-none');
                            
                            // Reset form to original values
                            document.getElementById('profileForm').reset();
                        }
                    </script>
                </div>
            </div>
            
            <!-- Change Password Form -->
            <div class="card shadow">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">
                        <i class="fas fa-lock me-2"></i>Ganti Password
                    </h6>
                </div>
                <div class="card-body">
                    <form method="POST" action="">
                        <input type="hidden" name="action" value="change_password">
                        
                        <div class="mb-3 position-relative">
                            <label class="form-label">Password Saat Ini</label>
                            <input type="password" name="current_password" id="current_password" class="form-control" required>
                            <span class="password-toggle" onclick="togglePassword('current_password', 'toggleIcon1')" style="position: absolute; right: 10px; top: 38px; cursor: pointer; color: #6c757d;">
                                <i class="fas fa-eye" id="toggleIcon1"></i>
                            </span>
                        </div>
                        
                        <div class="mb-3 position-relative">
                            <label class="form-label">Password Baru</label>
                            <input type="password" name="new_password" id="new_password" class="form-control" required minlength="6">
                            <span class="password-toggle" onclick="togglePassword('new_password', 'toggleIcon2')" style="position: absolute; right: 10px; top: 38px; cursor: pointer; color: #6c757d;">
                                <i class="fas fa-eye" id="toggleIcon2"></i>
                            </span>
                            <small class="text-muted">Minimal 6 karakter</small>
                        </div>
                        
                        <div class="mb-3 position-relative">
                            <label class="form-label">Konfirmasi Password Baru</label>
                            <input type="password" name="confirm_password" id="confirm_password" class="form-control" required minlength="6">
                            <span class="password-toggle" onclick="togglePassword('confirm_password', 'toggleIcon3')" style="position: absolute; right: 10px; top: 38px; cursor: pointer; color: #6c757d;">
                                <i class="fas fa-eye" id="toggleIcon3"></i>
                            </span>
                        </div>
                        
                        <button type="submit" class="btn btn-warning">
                            <i class="fas fa-key me-2"></i>Ubah Password
                        </button>
                    </form>
                    
                    <script>
                        function togglePassword(inputId, iconId) {
                            const passwordInput = document.getElementById(inputId);
                            const toggleIcon = document.getElementById(iconId);
                            if (passwordInput.type === 'password') {
                                passwordInput.type = 'text';
                                toggleIcon.classList.remove('fa-eye');
                                toggleIcon.classList.add('fa-eye-slash');
                            } else {
                                passwordInput.type = 'password';
                                toggleIcon.classList.remove('fa-eye-slash');
                                toggleIcon.classList.add('fa-eye');
                            }
                        }
                    </script>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include 'includes/footer.php'; ?>
