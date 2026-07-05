<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\Admin\DashboardController as AdminDashboardController;
use App\Http\Controllers\Admin\StudentController;
use App\Http\Controllers\Admin\PresensiController as AdminPresensiController;
use App\Http\Controllers\Admin\LogbookController as AdminLogbookController;
use App\Http\Controllers\Admin\JadwalPresensiController;
use App\Http\Controllers\Admin\MasterDataController;
use App\Http\Controllers\Admin\ImportController as AdminImportController;
use App\Http\Controllers\Admin\LogbookExcelController;
use App\Http\Controllers\Admin\LocationDebugController;
use App\Http\Controllers\Student\DashboardController as StudentDashboardController;
use App\Http\Controllers\Student\PresensiController as StudentPresensiController;
use App\Http\Controllers\Student\LogbookController as StudentLogbookController;
use App\Http\Controllers\Student\ImportController as StudentImportController;

// Redirect root to login
Route::get('/', function () {
    return redirect()->route('login');
});

// Authentication Routes
Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
Route::post('/login', [AuthController::class, 'login'])->name('login.post');
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

// Registration Routes
Route::get('/register', [AuthController::class, 'showRegister'])->name('register');
Route::post('/register', [AuthController::class, 'register'])->name('register.post');
Route::post('/register/check-duplicate', [AuthController::class, 'checkDuplicateUsernameAndNama'])->name('register.check-duplicate');
// Allow check-duplicate without auth - it's needed during registration form

// Complete Profile Routes
Route::middleware(['auth'])->group(function () {
    Route::get('/complete-profile', [AuthController::class, 'showCompleteProfile'])->name('profile.complete');
    Route::post('/complete-profile', [AuthController::class, 'completeProfile'])->name('profile.complete.post');
    
    // Profile
    Route::get('/profile', [AuthController::class, 'showProfile'])->name('profile.show');
    Route::get('/profile/edit', [AuthController::class, 'editProfile'])->name('profile.edit');
    Route::post('/profile/update', [AuthController::class, 'updateProfile'])->name('profile.update');
    
    // Change Password
    Route::get('/password/change', [AuthController::class, 'showChangePassword'])->name('password.change');
    Route::post('/password/change', [AuthController::class, 'changePassword'])->name('password.change.post');
    
    // Notifications
    Route::prefix('notifications')->name('notifications.')->controller(\App\Http\Controllers\NotificationController::class)->group(function () {
        Route::get('/', 'index')->name('index');
        Route::post('read-all', 'markAllAsRead')->name('read-all');
        Route::post('{notification}/mark-as-read', 'markAsRead')->name('mark-as-read');
        Route::get('unread-count', 'getUnreadCount')->name('unread-count');
        Route::get('unread', 'getUnreadNotifications')->name('unread');
    });
    
    // API for Guru Pembimbing details (for auto-fill no_hp)
    Route::get('/api/guru-pembimbing/{guru}/details', [\App\Http\Controllers\Admin\GuruPembimbingController::class, 'getDetails'])->name('api.guru-pembimbing.details');
});

// Password Reset Routes
Route::get('/forgot-password', [AuthController::class, 'showForgotPassword'])->name('password.forgot');
Route::post('/forgot-password', [AuthController::class, 'sendResetLink'])->name('password.email');
Route::get('/password/reset/{token}', [AuthController::class, 'showResetForm'])->name('password.reset');
Route::post('/password/reset', [AuthController::class, 'resetPassword'])->name('password.update');

// Admin Routes
Route::middleware(['auth', 'role:admin'])->prefix('admin')->name('admin.')->group(function () {
    // Admin Dashboard
    Route::get('/dashboard', [AdminDashboardController::class, 'index'])->name('dashboard');

    // Export Data
    Route::get('/export/students/excel', [\App\Http\Controllers\Admin\ExportController::class, 'exportExcel'])->name('export.excel');
    Route::get('/export/students/guru', [\App\Http\Controllers\Admin\ExportController::class, 'exportByGuru'])->name('export.guru');
    Route::get('/export/students/kelas', [\App\Http\Controllers\Admin\ExportController::class, 'exportByKelas'])->name('export.kelas');
    Route::get('/export/logbooks', [\App\Http\Controllers\Admin\ExportController::class, 'exportLogbook'])->name('export.logbooks');

    // Student Management
    Route::get('/students-list', [StudentController::class, 'list'])->name('students.list');
    Route::post('/students/bulk-delete', [StudentController::class, 'bulkDestroy'])->name('students.bulk-delete');
    Route::post('/students/{student}/mark-as-notified', [StudentController::class, 'markAsNotified'])->name('students.mark-as-notified');
    Route::resource('students', StudentController::class);

    // Pending Approvals
    Route::get('/pending-approvals', [StudentController::class, 'pendingApprovals'])->name('pending-approvals');
    Route::post('/students/{student}/approve', [StudentController::class, 'approve'])->name('students.approve');
    Route::post('/students/{student}/reject', [StudentController::class, 'reject'])->name('students.reject');
    Route::post('/students/{student}/reset-password', [StudentController::class, 'sendResetLink'])->name('students.reset-password.send');
    Route::post('/students/{student}/send-account-info', [StudentController::class, 'sendAccountInfo'])->name('students.send-account-info');
    Route::post('/students/send-account-info-all', [StudentController::class, 'sendAccountInfoAll'])->name('students.send-account-info-all');

    // Master Data
    Route::get('/master/data', [App\Http\Controllers\Admin\MasterDataController::class, 'index'])->name('master.index');
    Route::post('/master/settings', [App\Http\Controllers\Admin\MasterDataController::class, 'updateSettings'])->name('master.settings.update');
    
    // Redirect old routes to the unified view to prevent 405/404 errors from browser history
    Route::redirect('/master/jurusan', '/admin/master/data');
    Route::redirect('/master/kelas', '/admin/master/data');
    Route::redirect('/master/guru', '/admin/master/data');
    Route::resource('master/jurusan', \App\Http\Controllers\Admin\JurusanController::class)->except(['index', 'show'])->names([
        'create' => 'master.jurusan.create',
        'store' => 'master.jurusan.store',
        'edit' => 'master.jurusan.edit',
        'update' => 'master.jurusan.update',
        'destroy' => 'master.jurusan.destroy',
    ]);
    Route::resource('master/kelas', \App\Http\Controllers\Admin\KelasController::class)->except(['index', 'show'])->names([
        'create' => 'master.kelas.create',
        'store' => 'master.kelas.store',
        'edit' => 'master.kelas.edit',
        'update' => 'master.kelas.update',
        'destroy' => 'master.kelas.destroy',
    ]);
    Route::resource('master/guru', \App\Http\Controllers\Admin\GuruPembimbingController::class)->except(['index', 'show'])->names([
        'create' => 'master.guru.create',
        'store' => 'master.guru.store',
        'edit' => 'master.guru.edit',
        'update' => 'master.guru.update',
        'destroy' => 'master.guru.destroy',
    ]);
    // Presensi Management
    Route::get('/presensi', [AdminPresensiController::class, 'index'])->name('presensi.index');
    Route::get('/presensi/report', [AdminPresensiController::class, 'report'])->name('presensi.report');
    Route::get('/presensi/records/{presensi}/bukti', [AdminPresensiController::class, 'showBukti'])->name('presensi.bukti');
    Route::get('/presensi/records/{presensi}/bukti/download', [AdminPresensiController::class, 'downloadBukti'])->name('presensi.bukti.download');
    Route::get('/presensi/{student}/detail', [AdminPresensiController::class, 'detail'])->name('presensi.detail');
    
    // Logbook Management
    Route::get('/logbooks', [AdminLogbookController::class, 'index'])->name('logbooks.index');
    Route::get('/logbooks/{logbook}', [AdminLogbookController::class, 'show'])->name('logbooks.show');
    Route::post('/logbooks/{logbook}/approve', [AdminLogbookController::class, 'approve'])->name('logbooks.approve');
    Route::post('/logbooks/{logbook}/reject', [AdminLogbookController::class, 'reject'])->name('logbooks.reject');
    
    // Jadwal Presensi Management
    Route::get('/jadwal-presensi', [JadwalPresensiController::class, 'index'])->name('jadwal-presensi.index');
    Route::post('/jadwal-presensi', [JadwalPresensiController::class, 'store'])->name('jadwal-presensi.store');
    Route::get('/jadwal-presensi/{student}', [JadwalPresensiController::class, 'show'])->name('jadwal-presensi.show');
    Route::delete('/jadwal-presensi/{jadwal}', [JadwalPresensiController::class, 'destroy'])->name('jadwal-presensi.destroy');

    // Permintaan ubah lokasi magang siswa
    Route::get('/location-requests', [\App\Http\Controllers\Admin\LocationChangeRequestController::class, 'index'])->name('location-requests.index');
    Route::post('/location-requests/{locationRequest}/approve', [\App\Http\Controllers\Admin\LocationChangeRequestController::class, 'approve'])->name('location-requests.approve');
    Route::post('/location-requests/{locationRequest}/reject', [\App\Http\Controllers\Admin\LocationChangeRequestController::class, 'reject'])->name('location-requests.reject');

    // Import Data (Template Excel)
    Route::get('/import', [AdminImportController::class, 'index'])->name('import.index');
    Route::get('/import/template-akun', [AdminImportController::class, 'downloadTemplateAkun'])->name('import.template-akun');
    Route::post('/import/akun', [AdminImportController::class, 'importAkun'])->name('import.akun');
    Route::post('/import/resend-account-info/{student}', [AdminImportController::class, 'resendAccountInfo'])->name('import.resend-account-info');
    Route::post('/import/resend-account-info-all', [AdminImportController::class, 'resendAccountInfoAll'])->name('import.resend-account-info-all');
});

// Student Routes
Route::middleware(['auth', 'role:siswa'])->prefix('student')->name('student.')->group(function () {
    Route::get('/dashboard', [StudentDashboardController::class, 'index'])->name('dashboard');
    
    // Presensi
    Route::get('/presensi', [StudentPresensiController::class, 'index'])->name('presensi.index');
    Route::get('/presensi/create', [StudentPresensiController::class, 'create'])->name('presensi.create');
    Route::get('/presensi/riwayat', [StudentPresensiController::class, 'riwayat'])->name('presensi.riwayat');
    Route::post('/presensi', [StudentPresensiController::class, 'store'])->name('presensi.store');
    Route::post('/presensi/checkout', [StudentPresensiController::class, 'checkout'])->name('presensi.checkout');
    Route::post('/presensi/update-gmap', [StudentPresensiController::class, 'updateGmapLink'])->name('presensi.update-gmap');
    
    // Logbook
    Route::resource('logbooks', StudentLogbookController::class);
    Route::post('/logbooks/{logbook}/submit', [StudentLogbookController::class, 'submit'])->name('logbooks.submit');

    // E-Book - Lihat logbook kakak kelas terdahulu & laporan akhir
    Route::get('/ebook', [StudentLogbookController::class, 'ebook'])->name('ebook.index');
    Route::get('/ebook/{user}', [StudentLogbookController::class, 'ebookDetail'])->name('ebook.detail');

    // Import Data (Template Excel)
    Route::get('/import', [StudentImportController::class, 'index'])->name('import.index');
    Route::get('/import/template-data', [StudentImportController::class, 'downloadTemplateData'])->name('import.template-data');
    Route::post('/import/data', [StudentImportController::class, 'importData'])->name('import.data');
});
