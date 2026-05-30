<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Services\WhatsAppService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

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
                  ->orWhere('institusi', 'like', "%{$search}%");
            });
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $students = $query->latest()->paginate(20);

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

        $students = $query->latest()->paginate(20);
        $jurusans = \App\Models\Jurusan::all();
        $kelas = \App\Models\Kelas::all();

        return view('admin.students.list', compact('students', 'jurusans', 'kelas'));
    }

    public function create()
    {
        return view('admin.students.create');
    }

    public function store(Request $request, WhatsAppService $whatsapp)
    {
        $request->validate([
            'nama_lengkap' => 'required|string|max:100',
            'username' => 'required|string|max:50|unique:users',
            'no_hp' => 'required|string|max:20',
            'password' => 'required|string|min:6',
        ]);

        $user = User::create([
            'nama_lengkap' => $request->nama_lengkap,
            'username' => $request->username,
            'email' => User::internalEmailFromUsername($request->username),
            'no_hp' => $request->no_hp,
            'password' => Hash::make($request->password),
            'role' => 'siswa',
            'status' => 'menunggu',
        ]);

        $result = $whatsapp->sendAccountCreated($user, $request->password);

        if ($result['success']) {
            $msg = 'Siswa berhasil ditambahkan dan notifikasi WhatsApp telah dikirim!';
        } else {
            $waLink = $whatsapp->waMeLink(
                $user->no_hp,
                $whatsapp->buildAccountCreatedMessage($user, $request->password)
            );
            $msg = 'Siswa berhasil ditambahkan, namun WhatsApp otomatis gagal: ' . $result['message'];

            return redirect()
                ->route('admin.students.index')
                ->with('success', $msg)
                ->with('wa_fallback_link', $waLink)
                ->with('wa_fallback_name', $user->nama_lengkap);
        }

        return redirect()->route('admin.students.index')->with('success', $msg);
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

    public function update(Request $request, User $student, WhatsAppService $whatsapp)
    {
        $request->validate([
            'nama_lengkap' => 'required|string|max:100',
            'username' => 'required|string|max:50|unique:users,username,' . $student->id,
            'no_hp' => 'required|string|max:20',
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
        }

        $student->update($data);
        $student->syncStudentStatus();

        if ($passwordChanged) {
            $message = implode("\n", [
                "Halo *{$student->nama_lengkap}*,",
                '',
                'Admin SIMagang menginformasikan bahwa *akun PKL Anda telah diperbarui*.',
                '',
                "Username: *{$request->username}*",
                "Password Baru: *{$request->password}*",
                "Link login: " . url('/login'),
                '',
                '_Silakan login menggunakan kredensial baru Anda. Perubahan ini tidak menghapus data kemajuan (progress) akun Anda._',
            ]);

            $result = $whatsapp->send($request->no_hp, $message);

            if (!$result['success']) {
                $waLink = $whatsapp->waMeLink($request->no_hp, $message);
                return redirect()
                    ->route('admin.students.index')
                    ->with('success', 'Data akun siswa berhasil diupdate, namun WhatsApp otomatis gagal: ' . $result['message'])
                    ->with('wa_fallback_link', $waLink)
                    ->with('wa_fallback_name', $student->nama_lengkap);
            }
        }

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

    // Pending Approvals
    public function pendingApprovals()
    {
        $students = User::where('role', 'siswa')
                        ->where('status', 'pending')
                        ->with(['jurusan', 'kelas', 'guruPembimbing'])
                        ->latest()
                        ->paginate(20);
        return view('admin.students.pending', compact('students'));
    }

    public function approve(User $student)
    {
        $student->update(['status' => 'menunggu']);
        return redirect()->route('admin.pending-approvals')->with('success', 'Siswa berhasil disetujui!');
    }

    public function reject(User $student)
    {
        $student->update(['status' => 'rejected']);
        return redirect()->route('admin.pending-approvals')->with('success', 'Siswa berhasil ditolak!');
    }

    /**
     * Send password reset link to student (admin initiated)
     */
    public function sendResetLink(User $student, WhatsAppService $whatsapp)
    {
        if (empty($student->no_hp)) {
            return redirect()->route('admin.students.index')
                ->with('error', 'Nomor WhatsApp siswa belum diisi. Tidak dapat mengirim link reset.');
        }

        $token = \Illuminate\Support\Str::random(64);
        \Illuminate\Support\Facades\DB::table('password_resets')->updateOrInsert(
            ['email' => $student->email],
            ['token' => \Illuminate\Support\Facades\Hash::make($token), 'created_at' => now()]
        );

        $resetUrl = url(route('password.reset', ['token' => $token, 'email' => $student->email], false));
        $result = $whatsapp->sendPasswordReset($student, $resetUrl);

        if ($result['success']) {
            return redirect()->route('admin.students.index')
                ->with('success', 'Link reset password berhasil dikirim ke WhatsApp ' . $student->no_hp);
        }

        $waLink = $whatsapp->waMeLink($student->no_hp, $whatsapp->buildPasswordResetMessage($student, $resetUrl));

        return redirect()
            ->route('admin.students.index')
            ->with('error', 'Gagal mengirim WhatsApp: ' . $result['message'])
            ->with('wa_fallback_link', $waLink)
            ->with('wa_fallback_name', $student->nama_lengkap);
    }

    /**
     * Kirim informasi akun ke siswa individual via WhatsApp
     */
    public function sendAccountInfo(User $student, WhatsAppService $whatsapp)
    {
        if ($student->role !== 'siswa') {
            return back()->with('error', 'User bukan siswa.');
        }

        if (empty($student->no_hp)) {
            return back()->with('error', "Nomor WhatsApp {$student->nama_lengkap} belum diisi.");
        }

        // Kirim pesan berisi link login dan username
        $message = implode("\n", [
            "Halo *{$student->nama_lengkap}*,",
            '',
            'Admin SIMagang menginformasikan bahwa *akun PKL Anda sudah siap digunakan*.',
            '',
            "Username: *{$student->username}*",
            "Link login: " . url('/login'),
            '',
            '_Jika Anda lupa password, gunakan fitur "Reset Password" di halaman login._',
        ]);

        $result = $whatsapp->send($student->no_hp, $message);

        if ($result['success']) {
            return back()->with('success', 'Informasi akun berhasil dikirim ke WhatsApp ' . $student->no_hp);
        }

        $waLink = $whatsapp->waMeLink($student->no_hp, $message);

        return back()
            ->with('error', 'Gagal mengirim WhatsApp: ' . $result['message'])
            ->with('wa_fallback_link', $waLink)
            ->with('wa_fallback_name', $student->nama_lengkap);
    }

    /**
     * Kirim informasi akun ke semua siswa dengan status tertentu
     */
    public function sendAccountInfoAll(WhatsAppService $whatsapp)
    {
        $students = User::where('role', 'siswa')
            ->where('status', 'menunggu')
            ->whereNotNull('no_hp')
            ->get();

        if ($students->isEmpty()) {
            return back()->with('info', 'Tidak ada siswa dengan status menunggu.');
        }

        $sent = 0;
        $failed = 0;
        $errors = [];

        foreach ($students as $student) {
            $message = implode("\n", [
                "Halo *{$student->nama_lengkap}*,",
                '',
                'Admin SIMagang menginformasikan bahwa *akun PKL Anda sudah siap digunakan*.',
                '',
                "Username: *{$student->username}*",
                "Link login: " . url('/login'),
                '',
                '_Jika Anda lupa password, gunakan fitur "Reset Password" di halaman login._',
            ]);

            $result = $whatsapp->send($student->no_hp, $message);

            if ($result['success']) {
                $sent++;
            } else {
                $failed++;
                $errors[] = "{$student->nama_lengkap}: " . $result['message'];
            }
        }

        $message = "Informasi akun berhasil dikirim ke $sent siswa.";
        if ($failed > 0) {
            $message .= " $failed gagal dikirim.";
        }

        return back()
            ->with('success', $message)
            ->with('import_errors', $errors);
    }
}
