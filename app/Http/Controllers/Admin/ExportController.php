<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;

class ExportController extends Controller
{
    public function exportExcel()
    {
        $students = User::where('role', 'siswa')
            ->with(['jurusan', 'kelas', 'guruPembimbing'])
            ->get();

        $filename = "Data_Siswa_Magang_" . date('Ymd_His') . ".xls";

        header("Content-Type: application/vnd.ms-excel");
        header("Content-Disposition: attachment; filename=\"$filename\"");

        return view('admin.export.table', compact('students'));
    }

    public function exportWord()
    {
        $students = User::where('role', 'siswa')
            ->with(['jurusan', 'kelas', 'guruPembimbing'])
            ->get();

        $filename = "Data_Siswa_Magang_" . date('Ymd_His') . ".doc";

        header("Content-Type: application/vnd.ms-word");
        header("Content-Disposition: attachment; filename=\"$filename\"");

        return view('admin.export.document', compact('students'));
    }
}
