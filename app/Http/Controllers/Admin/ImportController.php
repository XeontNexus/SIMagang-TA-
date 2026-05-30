<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Services\WhatsAppService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class ImportController extends Controller
{
    public function index()
    {
        return view('admin.import.index');
    }

    /**
     * Download Template Pembuatan Akun (CSV yang dapat dibuka di Excel)
     */
    public function downloadTemplateAkun()
    {
        $filename = 'template_pembuatan_akun.csv';
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => "attachment; filename=\"$filename\"",
        ];

        $callback = function () {
            $handle = fopen('php://output', 'w');
            // Header
            fputcsv($handle, ['username', 'password', 'nama_lengkap', 'no_hp']);
            // Contoh data
            fputcsv($handle, ['siswa001', 'password123', 'Budi Santoso', '081234567890']);
            fputcsv($handle, ['siswa002', 'password123', 'Ani Wijaya', '081298765432']);
            fclose($handle);
        };

        return response()->stream($callback, 200, $headers);
    }


    /**
     * Import Akun dari CSV/Excel
     */
    public function importAkun(Request $request, WhatsAppService $whatsapp)
    {
        $request->validate([
            'file_akun' => 'required|file|mimes:csv,txt,xlsx,xls',
        ]);

        $file = $request->file('file_akun');
        $handle = fopen($file->getPathname(), 'r');
        $header = fgetcsv($handle);

        if (!$header || count($header) < 4) {
            fclose($handle);
            return back()->with('error', 'Format file tidak valid. Pastikan kolom: username, password, nama_lengkap, no_hp');
        }

        $created = 0;
        $failed = 0;
        $errors = [];
        $line = 2;

        while (($row = fgetcsv($handle)) !== false) {
            if (count($row) < 4) {
                $errors[] = "Baris $line: data tidak lengkap";
                $failed++;
                $line++;
                continue;
            }

            $data = [
                'username' => trim($row[0]),
                'password' => trim($row[1]),
                'nama_lengkap' => trim($row[2]),
                'no_hp' => trim($row[3]),
            ];

            $validator = Validator::make($data, [
                'username' => 'required|unique:users,username|max:50',
                'password' => 'required|min:6',
                'nama_lengkap' => 'required|max:100',
                'no_hp' => 'required|string|max:20',
            ]);

            if ($validator->fails()) {
                $errors[] = "Baris $line ({$data['username']}): " . implode(', ', $validator->errors()->all());
                $failed++;
                $line++;
                continue;
            }

            $user = User::create([
                'username' => $data['username'],
                'password' => Hash::make($data['password']),
                'nama_lengkap' => $data['nama_lengkap'],
                'email' => User::internalEmailFromUsername($data['username']),
                'no_hp' => $data['no_hp'],
                'role' => 'siswa',
                'status' => 'menunggu',
            ]);

            $waResult = $whatsapp->sendAccountCreated($user, $data['password']);
            if (!$waResult['success']) {
                \Illuminate\Support\Facades\Log::error('Gagal mengirim WhatsApp akun: ' . $waResult['message']);
                $errors[] = "Baris $line ({$data['username']}): Akun terbuat tapi WhatsApp gagal dikirim.";
            }

            $created++;
            $line++;
        }

        fclose($handle);

        $message = "Berhasil membuat $created akun siswa.";
        if ($failed > 0) {
            $message .= " $failed baris gagal.";
        }

        return back()
            ->with('success', $message)
            ->with('import_errors', $errors);
    }

    /**
     * Kirim ulang informasi akun ke satu siswa via WhatsApp
     */
    public function resendAccountInfo(User $student, WhatsAppService $whatsapp)
    {
        if (!$student || $student->role !== 'siswa') {
            return back()->with('error', 'Siswa tidak ditemukan atau tipe akun tidak valid.');
        }

        if (empty($student->no_hp)) {
            return back()->with('error', "Nomor WhatsApp {$student->nama_lengkap} belum diisi.");
        }

        // Karena password sudah di-hash, kita tidak bisa menampilkan password asli
        // Jadi kita sarankan admin untuk reset password dulu
        return back()->with('error', 'Untuk mengirim informasi akun, silakan gunakan fitur "Reset Password" terlebih dahulu.');
    }

    /**
     * Kirim informasi akun ke semua siswa yang belum menerima
     */
    public function resendAccountInfoAll(WhatsAppService $whatsapp)
    {
        // Ambil siswa dengan status tertentu (misalnya yang baru di-import)
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
            // Karena password sudah di-hash, kita tidak bisa mengirim password asli
            // Jadi kita hanya bisa reset password atau kirim link login
            $resetUrl = route('password.reset', ['email' => $student->email]);
            
            $message = implode("\n", [
                "Halo *{$student->nama_lengkap}*,",
                '',
                'Admin SIMagang menginformasikan bahwa *akun PKL Anda sudah siap digunakan*.',
                '',
                "Username: *{$student->username}*",
                "Link login: " . url('/login'),
                '',
                '_Segera hubungi admin untuk mendapatkan password Anda atau gunakan fitur reset password._',
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
