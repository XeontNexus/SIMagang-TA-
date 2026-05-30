<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ImportController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        $dataFilled = $user->no_hp && $user->institusi && $user->tanggal_mulai;
        return view('student.import.index', compact('dataFilled'));
    }

    /**
     * Download Template Data Siswa & PKL dengan username terisi (CSV)
     */
    public function downloadTemplateData()
    {
        $user = Auth::user();
        $filename = 'template_data_siswa_' . $user->username . '.csv';
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => "attachment; filename=\"$filename\"",
        ];

        $callback = function () use ($user) {
            $handle = fopen('php://output', 'w');
            fputcsv($handle, [
                'username', 'no_hp', 'institusi', 'jurusan_id', 'kelas_id',
                'tanggal_mulai', 'tanggal_selesai', 'alamat_magang',
                'pembimbing_lapangan', 'gmap_magang', 'guru_pembimbing_id'
            ]);
            fputcsv($handle, [
                $user->username,
                $user->no_hp ?? '',
                $user->institusi ?? '',
                $user->jurusan_id ?? '',
                $user->kelas_id ?? '',
                optional($user->tanggal_mulai)->format('Y-m-d') ?? '',
                optional($user->tanggal_selesai)->format('Y-m-d') ?? '',
                $user->alamat_magang ?? '',
                $user->pembimbing_lapangan ?? '',
                $user->gmap_magang ?? '',
                $user->guru_pembimbing_id ?? ''
            ]);
            fclose($handle);
        };

        return response()->stream($callback, 200, $headers);
    }

    /**
     * Import Data Pribadi & PKL dari CSV/Excel
     */
    public function importData(Request $request)
    {
        $request->validate([
            'file_data' => 'required|file|mimes:csv,txt,xlsx,xls',
        ]);

        $file = $request->file('file_data');
        $handle = fopen($file->getPathname(), 'r');
        $header = fgetcsv($handle);

        if (!$header || count($header) < 11) {
            fclose($handle);
            return back()->with('error', 'Format file tidak valid. Pastikan kolom sesuai template.');
        }

        $user = Auth::user();
        $updated = false;
        $errors = [];

        while (($row = fgetcsv($handle)) !== false) {
            if (count($row) < 11) continue;

            $rowUsername = trim($row[0]);
            if ($rowUsername !== $user->username) {
                $errors[] = "Username di file ('$rowUsername') tidak cocok dengan akun Anda ('{$user->username}').";
                continue;
            }

            $updateData = [];
            if (!empty($row[1])) $updateData['no_hp'] = trim($row[1]);
            if (!empty($row[2])) $updateData['institusi'] = trim($row[2]);
            if (!empty($row[3])) $updateData['jurusan_id'] = (int) $row[3];
            if (!empty($row[4])) $updateData['kelas_id'] = (int) $row[4];
            if (!empty($row[5])) $updateData['tanggal_mulai'] = $row[5];
            if (!empty($row[6])) $updateData['tanggal_selesai'] = $row[6];
            if (!empty($row[7])) $updateData['alamat_magang'] = trim($row[7]);
            if (!empty($row[8])) $updateData['pembimbing_lapangan'] = trim($row[8]);
            if (!empty($row[9])) $updateData['gmap_magang'] = trim($row[9]);
            if (!empty($row[10])) $updateData['guru_pembimbing_id'] = (int) $row[10];

            if (count($updateData) > 0) {
                $user->update($updateData);
                $updated = true;
            }
        }

        fclose($handle);

        if ($updated) {
            return redirect()->route('student.dashboard')->with('success', 'Data pribadi dan PKL berhasil diupdate!');
        }

        if (count($errors) > 0) {
            return back()->with('error', implode(' ', $errors));
        }

        return back()->with('info', 'Tidak ada data yang diupdate.');
    }
}
