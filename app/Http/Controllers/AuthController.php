<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\GuruPembimbing;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use App\Services\NotificationService;

class AuthController extends Controller
{
    /**
     * Show login form
     */
    public function showLogin()
    {
        if (Auth::check()) {
            return $this->redirectBasedOnRole(Auth::user());
        }
        
        return view('auth.login');
    }

    /**
     * Handle login request
     */
    public function login(Request $request)
    {
        $request->validate([
            'username' => 'required|string',
            'password' => 'required'
        ]);

        $credentials = $request->only('username', 'password');

        if (Auth::attempt($credentials)) {
            $request->session()->regenerate();

            $user = Auth::user();
            if ($user->isSiswa()) {
                $user->syncStudentStatus(true);
            }

            return $this->redirectBasedOnRole($user->fresh());
        }

        return back()->withErrors([
            'error' => 'Username atau password salah.'
        ])->withInput($request->except('password'));
    }

    /**
     * Show register form
     */
    public function showRegister()
    {
        $guruPembimbings = \Cache::remember('active_guru_pembimbings', 3600, function () {
            return GuruPembimbing::where('status', 'active')
                ->select(['id', 'nama_guru', 'no_hp'])
                ->orderBy('nama_guru')
                ->get();
        });
        return view('auth.register', compact('guruPembimbings'));
    }

    /**
     * Handle register request
     */
    public function register(Request $request)
    {
        $request->validate([
            'nama_lengkap' => 'required|string|max:100',
            'username' => 'required|string|max:50|unique:users',
            'password' => 'required|string|min:6|confirmed',
        ]);

        // Check if username and nama_lengkap combination already exists
        $existingUser = User::where('username', $request->username)
                            ->where('nama_lengkap', $request->nama_lengkap)
                            ->first();

        if ($existingUser) {
            return back()->withErrors([
                'username' => 'Kombinasi username dan nama sudah terdaftar! Gunakan username yang berbeda untuk membedakan akun.'
            ])->withInput($request->except('password', 'password_confirmation'));
        }

        $user = User::create([
            'nama_lengkap' => $request->nama_lengkap,
            'username' => $request->username,
            'email' => User::internalEmailFromUsername($request->username),
            'password' => Hash::make($request->password),
            'role' => 'siswa',
            'status' => 'pending',
        ]);

        Auth::login($user);

        NotificationService::notifyAllAdmins(
            'Pendaftaran Siswa Baru',
            "{$user->nama_lengkap} mendaftar sebagai siswa magang (menunggu approval).",
            'warning',
            'fa-user-plus',
            route('admin.pending-approvals'),
            'student_registration',
            $user->id
        );

        return redirect()->route('student.dashboard')->with('success', 'Pendaftaran berhasil! Selamat datang.');
    }

    /**
     * Check if username and nama_lengkap combination already exists
     */
    public function checkDuplicateUsernameAndNama(Request $request)
    {
        if (!$request->isJson()) {
            return response()->json(['error' => 'Invalid request'], 400);
        }

        $username = $request->input('username');
        $nama_lengkap = $request->input('nama_lengkap');

        if (!$username || !$nama_lengkap) {
            return response()->json(['isDuplicate' => false]);
        }

        // Check if combination exists
        $exists = User::where('username', $username)
                      ->where('nama_lengkap', $nama_lengkap)
                      ->exists();

        return response()->json(['isDuplicate' => $exists]);
    }

    /**
     * Handle logout request
     */
    public function logout(Request $request)
    {
        Auth::logout();
        
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        
        return redirect()
            ->route('login')
            ->withHeaders([
                'Cache-Control' => 'no-store, no-cache, must-revalidate, max-age=0',
                'Pragma' => 'no-cache',
            ])
            ->with('success', 'Anda telah logout.');
    }

    /**
     * Show complete profile form for first-time login
     */
    public function showCompleteProfile()
    {
        return redirect()->route('profile.edit');
    }

    /**
     * Handle complete profile request
     */
    public function completeProfile(Request $request)
    {
        return $this->updateProfile($request);
    }

    /**
     * Show user profile
     */
    public function showProfile()
    {
        $exportGuruList = collect();
        $exportKelasList = collect();

        if (Auth::user()->isAdmin()) {
            $exportGuruList = GuruPembimbing::orderBy('nama_guru')->get();

            $exportKelasList = \App\Models\Kelas::with('jurusan')
                ->orderBy('nama_kelas')
                ->get();
        }

        return view('auth.profile', compact('exportGuruList', 'exportKelasList'));
    }

    /**
     * Show change password form
     */
    public function showChangePassword()
    {
        return view('auth.password-change');
    }

    /**
     * Handle password change
     */
    public function changePassword(Request $request)
    {
        $user = Auth::user();

        $rules = [
            'current_password' => 'required',
        ];

        if ($user->isSiswa()) {
            $rules['username'] = ['required', 'string', 'max:50', Rule::unique('users')->ignore($user->id)];
            $rules['new_password'] = 'nullable|min:6|confirmed';
        } else {
            $rules['new_password'] = 'required|min:6|confirmed';
        }

        $request->validate($rules);

        if (!Hash::check($request->current_password, $user->password)) {
            return back()->with('error', 'Password saat ini salah!');
        }

        $updateData = [];

        if ($user->isSiswa()) {
            $updateData['username'] = $request->username;
            if (empty($user->email) || str_ends_with($user->email, '@simagang.local')) {
                $updateData['email'] = User::internalEmailFromUsername($request->username);
            }
        }

        if ($request->filled('new_password')) {
            $updateData['password'] = Hash::make($request->new_password);
        }

        $user->update($updateData);

        $message = $user->isSiswa()
            ? 'Username' . ($request->filled('new_password') ? ' dan password' : '') . ' berhasil diperbarui!'
            : 'Password berhasil diubah!';

        return back()->with('success', $message);
    }

    /**
     * Show edit profile form
     */
    public function editProfile()
    {
        $user = Auth::user();

        $guruPembimbings = GuruPembimbing::orderBy('nama_guru')->get();
        $jurusans = \App\Models\Jurusan::orderBy('nama_jurusan')->get();
        $kelas = \App\Models\Kelas::orderBy('nama_kelas')->get();
        return view('auth.profile-edit', compact('guruPembimbings', 'jurusans', 'kelas'));
    }

    /**
     * Handle profile update
     */
    /**
     * Normalize Indonesian phone number: convert leading 0 to 62, strip non-digits.
     */
    private function normalizePhone(?string $phone): ?string
    {
        if (empty($phone)) {
            return null;
        }
        $phone = preg_replace('/[^0-9]/', '', $phone);
        if (str_starts_with($phone, '0')) {
            $phone = '62' . substr($phone, 1);
        }
        return $phone ?: null;
    }

    public function updateProfile(Request $request)
    {
        $user = Auth::user();

        // Normalize phone numbers before validation
        $phoneFields = ['no_hp', 'no_hp_pembimbing_lapangan', 'no_hp_guru_pembimbing'];
        foreach ($phoneFields as $field) {
            if ($request->has($field)) {
                $request->merge([$field => $this->normalizePhone($request->input($field))]);
            }
        }

        $rules = [
            'nama_lengkap' => ['required', 'string', 'max:100', 'regex:/^[\p{L}\s\'.,]+$/u'],
            'no_hp' => ['nullable', 'string', 'max:15', 'regex:/^62[0-9]{8,13}$/'],
        ];

        if ($user->isAdmin()) {
            $rules['email'] = ['required', 'email', Rule::unique('users')->ignore($user->id)];
            $rules['username'] = ['required', 'string', 'max:50', Rule::unique('users')->ignore($user->id)];
            $rules['no_hp'] = ['nullable', 'string', 'max:15', 'regex:/^62[0-9]{8,13}$/'];
        }

        if ($user->isStudent()) {
            $rules = array_merge($rules, [
                'nisn' => ['required', 'string', 'max:20', 'regex:/^[0-9]+$/'],
                'institusi' => ['nullable', 'string', 'max:100'],
                'jurusan_id' => ['nullable', 'exists:jurusans,id'],
                'kelas_id' => ['nullable', 'exists:kelas,id'],
                'mitra_magang' => ['nullable', 'string', 'max:100'],
                'alamat_magang' => ['nullable', 'string', 'max:255'],
                'tanggal_mulai' => ['nullable', 'date'],
                'tanggal_selesai' => ['nullable', 'date', 'after_or_equal:tanggal_mulai'],
                'pembimbing_lapangan' => ['nullable', 'string', 'max:100', 'regex:/^[\p{L}\s\'.,]+$/u'],
                'no_hp_pembimbing_lapangan' => ['nullable', 'string', 'max:15', 'regex:/^62[0-9]{8,13}$/'],
                'guru_pembimbing_id' => ['nullable', 'exists:guru_pembimbings,id'],
                'no_hp_guru_pembimbing' => ['nullable', 'string', 'max:15', 'regex:/^62[0-9]{8,13}$/'],
            ]);
        }

        $validated = $request->validate($rules, [
            'nisn.required' => 'NISN wajib diisi.',
        ]);

        $user->update($validated);

        if ($user->isSiswa()) {
            $user->syncStudentStatus();
        }

        return redirect()->route('profile.show')->with('success', 'Profil berhasil diperbarui!');
    }

    /**
     * Show forgot password form
     */
    public function showForgotPassword()
    {
        return view('auth.forgot-password');
    }

    /**
     * Send password reset link
     */
    public function sendResetLink(Request $request)
    {
        $request->validate([
            'email' => 'required|email'
        ]);

        $user = User::where('email', $request->email)->first();

        if (!$user) {
            return back()->withErrors(['email' => 'Email tidak ditemukan.']);
        }

        // Generate token
        $token = \Illuminate\Support\Str::random(64);
        \Illuminate\Support\Facades\DB::table('password_resets')->updateOrInsert(
            ['email' => $request->email],
            ['token' => \Illuminate\Support\Facades\Hash::make($token), 'created_at' => now()]
        );

        // Send email (logs to file in dev mode)
        $resetUrl = url(route('password.reset', ['token' => $token, 'email' => $request->email], false));
        \Illuminate\Support\Facades\Mail::send('emails.password-reset', ['url' => $resetUrl, 'user' => $user], function($message) use ($user) {
            $message->to($user->email);
            $message->subject('Reset Password SIMagang');
        });

        return back()->with('status', 'Link reset password telah dikirim ke email Anda.');
    }

    /**
     * Show reset password form
     */
    public function showResetForm(Request $request, $token = null)
    {
        return view('auth.reset-password', [
            'token' => $token,
            'email' => $request->email ?? old('email')
        ]);
    }

    /**
     * Reset password
     */
    public function resetPassword(Request $request)
    {
        $request->validate([
            'token' => 'required',
            'email' => 'required|email',
            'password' => 'required|min:6|confirmed'
        ]);

        $record = \Illuminate\Support\Facades\DB::table('password_resets')
            ->where('email', $request->email)
            ->first();

        if (!$record || !\Illuminate\Support\Facades\Hash::check($request->token, $record->token)) {
            return back()->with('error', 'Token reset password tidak valid atau sudah kadaluarsa.');
        }

        if (now()->diffInMinutes($record->created_at) > 60) {
            return back()->with('error', 'Token reset password sudah kadaluarsa. Silakan minta link baru.');
        }

        $user = User::where('email', $request->email)->first();
        if (!$user) {
            return back()->with('error', 'User tidak ditemukan.');
        }

        $user->update(['password' => Hash::make($request->password)]);
        \Illuminate\Support\Facades\DB::table('password_resets')->where('email', $request->email)->delete();

        return redirect()->route('login')->with('success', 'Password berhasil direset! Silakan login dengan password baru.');
    }

    /**
     * Redirect user based on their role
     */
    private function redirectBasedOnRole($user)
    {
        $role = $user->role;
        
        switch ($role) {
            case 'siswa':
                if ($user->status === 'pending') {
                    return redirect()->route('student.dashboard');
                }
                if (!$user->isProfileComplete()) {
                    return redirect()->route('profile.edit');
                }
                return redirect()->route('student.dashboard');
            case 'admin':
                return redirect()->route('admin.dashboard');
            default:
                return redirect()->route('login');
        }
    }
}
