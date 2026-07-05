<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Mail\StudentAccountCreated;
use App\Mail\StudentAccountInfo;
use App\Mail\StudentAccountUpdated;
use App\Mail\StudentPasswordReset;
use App\Models\User;
use App\Services\NotificationService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class StudentController extends Controller
{
    public function index(Request $request)
    {
        $query = User::where('role', 'siswa')->with(['jurusan', 'kelas', 'guruPembimbing']);

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('nama_lengkap', 'like', "%{$search}%")
                  ->orWhere('username', 'like', "%{$search}%")
                  ->orWhere('no_hp', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%");
            });
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $students = $query->orderBy('nama_lengkap')->paginate(20);

        return view('admin.students.index', compact('students'));
    }

    public function list(Request $request)
    {
        $query = User::where('role', 'siswa')->with(['jurusan', 'kelas', 'guruPembimbing', 'presensis']);

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('nama_lengkap', 'like', "%{$search}%")
                  ->orWhere('username', 'like', "%{$search}%")
                  ->orWhere('no_hp', 'like', "%{$search}%")
                  ->orWhere('institusi', 'like', "%{$search}%")
                  ->orWhere('mitra_magang', 'like', "%{$search}%");
            });
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('kelas_id')) {
            $query->where('kelas_id', $request->kelas_id);
        }

        if ($request->filled('jurusan_id')) {
            $query->where('jurusan_id', $request->jurusan_id);
        }

        $students = $query->orderBy('nama_lengkap')->paginate(20);
        $jurusans = \App\Models\Jurusan::orderBy('nama_jurusan')->get();
        $kelas = \App\Models\Kelas::orderBy('nama_kelas')->get();

        return view('admin.students.list', compact('students', 'jurusans', 'kelas'));
    }

    public function create()
    {
        return view('admin.students.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'nama_lengkap' => ['required', 'string', 'max:100', 'regex:/^[\p{L}\s\'.,]+$/u'],
            'username'     => 'required|string|max:50|unique:users',
            'no_hp'        => ['required', 'string', 'max:15', 'regex:/^62[0-9]{8,13}$/'],
            'password'     => 'required|string|min:6',
        ], [
            'nama_lengkap.regex' => 'Nama lengkap hanya boleh berisi huruf, spasi, titik, koma, dan tanda apostrof (\').',
            'no_hp.regex'        => 'Nomor WA harus dimulai dari 62 dan terdiri dari 10–15 digit angka.',
        ]);

        $user = User::create([
            'nama_lengkap' => $request->nama_lengkap,
            'username' => $request->username,
            'no_hp' => $request->no_hp,
            'email' => User::internalEmailFromUsername($request->username),
            'password' => Hash::make($request->password),
            'password_plain' => $request->password,
            'role' => 'siswa',
            'status' => 'belum_dinotifikasi',
        ]);

        return redirect()
            ->route('admin.students.index')
            ->with('success', '✅ Siswa berhasil ditambahkan!');
    }

    public function show(User $student)
    {
        $student->load(['jurusan', 'kelas', 'guruPembimbing', 'presensis', 'logbooks']);
        return view('admin.students.show', compact('student'));
    }

    public function edit(User $student)
    {
        return view('admin.students.edit', compact('student'));
    }

    public function update(Request $request, User $student)
    {
        $request->validate([
            'nama_lengkap' => ['required', 'string', 'max:100', 'regex:/^[\p{L}\s\'.,]+$/u'],
            'username'     => 'required|string|max:50|unique:users,username,' . $student->id,
            'no_hp'        => ['required', 'string', 'max:15', 'regex:/^62[0-9]{8,13}$/'],
        ], [
            'nama_lengkap.regex' => 'Nama lengkap hanya boleh berisi huruf, spasi, titik, koma, dan tanda apostrof (\').',
            'no_hp.regex'        => 'Nomor WA harus dimulai dari 62 dan terdiri dari 10–15 digit angka.',
        ]);

        $data = [
            'nama_lengkap' => $request->nama_lengkap,
            'username' => $request->username,
            'no_hp' => $request->no_hp,
            'email' => User::internalEmailFromUsername($request->username),
        ];

        $passwordChanged = $request->filled('password');
        if ($passwordChanged) {
            $data['password'] = Hash::make($request->password);
            $data['password_plain'] = $request->password;
        }

        $student->update($data);
        $student->syncStudentStatus();

        return redirect()->route('admin.students.index')->with('success', 'Data akun siswa berhasil diupdate!');
    }

    public function destroy(User $student)
    {
        try {
            // Delete related data first to prevent FK constraint issues
            $student->presensis()->delete();
            $student->logbooks()->delete();
            $student->jadwalPresensis()->delete();
            
            $student->delete();
            return redirect()->route('admin.students.index')->with('success', 'Siswa berhasil dihapus!');
        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::error('Gagal menghapus siswa: ' . $e->getMessage());
            return redirect()->route('admin.students.index')->with('error', 'Gagal menghapus siswa: ' . $e->getMessage());
        }
    }

    /**
     * Hapus banyak siswa sekaligus (bulk delete)
     */
    public function bulkDestroy(Request $request)
    {
        $request->validate([
            'ids'   => 'required|array|min:1',
            'ids.*' => 'integer|exists:users,id',
        ]);

        $students = User::where('role', 'siswa')->whereIn('id', $request->ids)->get();

        $deleted = 0;
        $errors  = [];

        foreach ($students as $student) {
            try {
                $student->presensis()->delete();
                $student->logbooks()->delete();
                $student->jadwalPresensis()->delete();
                $student->delete();
                $deleted++;
            } catch (\Exception $e) {
                $errors[] = $student->nama_lengkap . ': ' . $e->getMessage();
                Log::error('Gagal menghapus siswa ' . $student->id . ': ' . $e->getMessage());
            }
        }

        $msg = "$deleted akun siswa berhasil dihapus.";
        if (!empty($errors)) {
            $msg .= ' ' . count($errors) . ' gagal dihapus.';
        }

        return redirect()->route('admin.students.index')->with(
            empty($errors) ? 'success' : 'warning',
            $msg
        );
    }

    /**
     * Tandai siswa sudah dinotifikasi: status belum_dinotifikasi → menunggu
     */
    public function markAsNotified(User $student)
    {
        if ($student->status === 'belum_dinotifikasi') {
            $student->update(['status' => 'menunggu']);
        }
        return back()->with('success', "Akun {$student->nama_lengkap} ditandai sudah dinotifikasi (status → Menunggu).");
    }


    public function pendingApprovals()
    {
        $students = User::where('role', 'siswa')
                        ->where('status', 'pending')
                        ->with(['jurusan', 'kelas', 'guruPembimbing'])
                        ->orderBy('nama_lengkap')
                        ->paginate(20);
        return view('admin.students.pending', compact('students'));
    }

    public function approve(User $student)
    {
        $student->update(['status' => 'menunggu']);

        NotificationService::create(
            $student->id,
            'Pendaftaran Disetujui',
            'Pendaftaran akun Anda telah disetujui oleh Admin. Silakan lengkapi profil Anda.',
            'success',
            'fa-user-check',
            route('profile.edit'),
            'student_approval',
            $student->id
        );

        return redirect()->route('admin.pending-approvals')->with('success', 'Siswa berhasil disetujui!');
    }

    public function reject(User $student)
    {
        $student->update(['status' => 'rejected']);

        NotificationService::create(
            $student->id,
            'Pendaftaran Ditolak',
            'Pendaftaran akun Anda ditolak oleh Admin.',
            'danger',
            'fa-user-times',
            null,
            'student_rejection',
            $student->id
        );

        return redirect()->route('admin.pending-approvals')->with('success', 'Siswa berhasil ditolak!');
    }

    /**
     * Send password reset link to student (admin initiated)
     */
    public function sendResetLink(User $student)
    {
        if (empty($student->email) || str_ends_with($student->email, '@simagang.local')) {
            return redirect()->route('admin.students.index')
                ->with('error', 'Email siswa belum diisi. Tidak dapat mengirim link reset.');
        }

        $token = \Illuminate\Support\Str::random(64);
        \Illuminate\Support\Facades\DB::table('password_resets')->updateOrInsert(
            ['email' => $student->email],
            ['token' => \Illuminate\Support\Facades\Hash::make($token), 'created_at' => now()]
        );

        $resetUrl = url(route('password.reset', ['token' => $token, 'email' => $student->email], false));

        try {
            Mail::to($student->email)->send(new StudentPasswordReset($student, $resetUrl));
            return redirect()->route('admin.students.index')
                ->with('success', 'Link reset password berhasil dikirim ke email ' . $student->email);
        } catch (\Exception $e) {
            Log::error('Gagal mengirim email reset password: ' . $e->getMessage());
            return redirect()->route('admin.students.index')
                ->with('error', 'Gagal mengirim email reset password: ' . $e->getMessage());
        }
    }

    /**
     * Kirim informasi akun ke siswa individual via Email
     */
    public function sendAccountInfo(User $student)
    {
        if ($student->role !== 'siswa') {
            return back()->with('error', 'User bukan siswa.');
        }

        if (empty($student->email) || str_ends_with($student->email, '@simagang.local')) {
            return back()->with('error', "Email {$student->nama_lengkap} belum diisi.");
        }

        try {
            Mail::to($student->email)->send(new StudentAccountInfo($student));
            if ($student->status === 'belum_dinotifikasi') {
                $student->update(['status' => 'menunggu']);
            }
            return back()->with('success', 'Informasi akun berhasil dikirim ke email ' . $student->email . ' dan status diubah ke Menunggu.');
        } catch (\Exception $e) {
            Log::error('Gagal mengirim email info akun: ' . $e->getMessage());
            return back()->with('error', 'Gagal mengirim email: ' . $e->getMessage());
        }
    }

    /**
     * Kirim informasi akun ke semua siswa dengan status belum_dinotifikasi
     */
    public function sendAccountInfoAll()
    {
        $students = User::where('role', 'siswa')
            ->where('status', 'belum_dinotifikasi')
            ->whereNotNull('email')
            ->where('email', 'not like', '%@simagang.local')
            ->get();

        if ($students->isEmpty()) {
            return back()->with('info', 'Tidak ada siswa dengan status Belum Dinotifikasi yang memiliki email.');
        }

        $sent = 0;
        $failed = 0;
        $errors = [];

        foreach ($students as $student) {
            try {
                Mail::to($student->email)->send(new StudentAccountInfo($student));
                $student->update(['status' => 'menunggu']);
                $sent++;
            } catch (\Exception $e) {
                $failed++;
                $errors[] = "{$student->nama_lengkap}: " . $e->getMessage();
                Log::error("Gagal mengirim email ke {$student->email}: " . $e->getMessage());
            }
        }

        $message = "Informasi akun berhasil dikirim ke $sent siswa via email dan status diubah ke Menunggu.";
        if ($failed > 0) {
            $message .= " $failed gagal dikirim.";
        }

        return back()
            ->with('success', $message)
            ->with('import_errors', $errors);
    }
}
