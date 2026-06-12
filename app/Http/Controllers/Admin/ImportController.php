<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Mail\StudentAccountCreated;
use App\Mail\StudentAccountInfo;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Validator;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

class ImportController extends Controller
{
    public function index()
    {
        return view('admin.import.index');
    }

    /**
     * Download Template Pembuatan Akun (Excel XLSX)
     */
    public function downloadTemplateAkun()
    {
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        
        // Header
        $sheet->setCellValue('A1', 'nama lengkap');
        $sheet->setCellValue('B1', 'username');
        $sheet->setCellValue('C1', 'nisn');
        $sheet->setCellValue('D1', 'nomor wa');
        
        // Contoh data
        $sheet->setCellValue('A2', 'Budi Santoso');
        $sheet->setCellValue('B2', 'siswa001');
        $sheet->setCellValue('C2', '1234567890');
        $sheet->setCellValue('D2', '081234567890');

        $sheet->setCellValue('A3', 'Ani Wijaya');
        $sheet->setCellValue('B3', 'siswa002');
        $sheet->setCellValue('C3', '1234567891');
        $sheet->setCellValue('D3', '081234567891');

        // Style header (A1:D1) - Bold, White text, Blue Fill, Centered
        $headerStyle = [
            'font' => [
                'bold' => true,
                'color' => ['argb' => \PhpOffice\PhpSpreadsheet\Style\Color::COLOR_WHITE],
                'size' => 11,
            ],
            'alignment' => [
                'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER,
                'vertical' => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER,
            ],
            'fill' => [
                'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                'startColor' => ['argb' => '4E73DF'], // Theme Primary Blue
            ],
        ];
        $sheet->getStyle('A1:D1')->applyFromArray($headerStyle);
        $sheet->getRowDimension(1)->setRowHeight(25);

        // Center sample data columns for NISN and WA
        $sheet->getStyle('B2:D3')->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);

        // Set cells as Text format to avoid dropping leading zeros
        $sheet->getStyle('C2:C3')->getNumberFormat()->setFormatCode(\PhpOffice\PhpSpreadsheet\Style\NumberFormat::FORMAT_TEXT);
        $sheet->getStyle('D2:D3')->getNumberFormat()->setFormatCode(\PhpOffice\PhpSpreadsheet\Style\NumberFormat::FORMAT_TEXT);

        // Add borders to A1:D3
        $borderStyle = [
            'borders' => [
                'allBorders' => [
                    'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
                    'color' => ['argb' => 'D3D3D3'],
                ],
            ],
        ];
        $sheet->getStyle('A1:D3')->applyFromArray($borderStyle);

        // Auto size columns to fit content nicely
        foreach (range('A', 'D') as $col) {
            $sheet->getColumnDimension($col)->setAutoSize(true);
        }

        return response()->streamDownload(function() use ($spreadsheet) {
            $writer = new Xlsx($spreadsheet);
            $writer->save('php://output');
        }, 'template_pembuatan_akun.xlsx', [
            'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
            'Cache-Control' => 'max-age=0',
        ]);
    }


    /**
     * Import Akun dari CSV/Excel
     */
    public function importAkun(Request $request)
    {
        $request->validate([
            'file_akun' => 'required|file|mimes:csv,txt,xlsx,xls',
        ]);

        $file = $request->file('file_akun');
        $extension = $file->getClientOriginalExtension();
        
        try {
            if (in_array(strtolower($extension), ['xlsx', 'xls'])) {
                $reader = \PhpOffice\PhpSpreadsheet\IOFactory::createReaderForFile($file->getPathname());
                $reader->setReadDataOnly(true);
                $spreadsheet = $reader->load($file->getPathname());
                $worksheet = $spreadsheet->getActiveSheet();
                $rows = $worksheet->toArray();
                
                // Get header
                $header = array_shift($rows);
            } else {
                // Fallback to CSV parsing
                $handle = fopen($file->getPathname(), 'r');
                $header = fgetcsv($handle);
                $rows = [];
                while (($row = fgetcsv($handle)) !== false) {
                    $rows[] = $row;
                }
                fclose($handle);
            }
        } catch (\Exception $e) {
            return back()->with('error', 'Gagal membaca file: ' . $e->getMessage());
        }

        if (!$header || count($header) < 4) {
            return back()->with('error', 'Format file tidak valid. Pastikan kolom: nama lengkap, username, nisn, nomor wa');
        }

        $created = 0;
        $failed = 0;
        $errors = [];
        $line = 2;

        foreach ($rows as $row) {
            if (empty($row) || count($row) < 4) {
                if (empty(array_filter($row))) {
                    $line++;
                    continue;
                }
                $errors[] = "Baris $line: data tidak lengkap";
                $failed++;
                $line++;
                continue;
            }

            $data = [
                'nama_lengkap' => trim($row[0]),
                'username' => trim($row[1]),
                'nisn' => trim($row[2]),
                'no_hp' => trim($row[3]),
            ];

            $validator = Validator::make($data, [
                'username' => 'required|unique:users,username|max:50',
                'nisn' => 'required|min:6',
                'nama_lengkap' => 'required|max:100',
                'no_hp' => 'required|max:20',
            ]);

            if ($validator->fails()) {
                $errors[] = "Baris $line ({$data['username']}): " . implode(', ', $validator->errors()->all());
                $failed++;
                $line++;
                continue;
            }

            User::create([
                'username' => $data['username'],
                'nisn' => $data['nisn'],
                'password' => Hash::make($data['nisn']), // nisn digunakan sebagai password
                'password_plain' => $data['nisn'], // simpan password asli
                'nama_lengkap' => $data['nama_lengkap'],
                'no_hp' => $data['no_hp'],
                'email' => User::internalEmailFromUsername($data['username']),
                'role' => 'siswa',
                'status' => 'menunggu',
            ]);

            $created++;
            $line++;
        }

        $message = "Berhasil membuat $created akun siswa.";
        if ($failed > 0) {
            $message .= " $failed baris gagal.";
        }

        return back()
            ->with('success', $message)
            ->with('import_errors', $errors);
    }

    /**
     * Kirim ulang informasi akun ke satu siswa via Email
     */
    public function resendAccountInfo(User $student)
    {
        if (!$student || $student->role !== 'siswa') {
            return back()->with('error', 'Siswa tidak ditemukan atau tipe akun tidak valid.');
        }

        if (empty($student->email) || str_ends_with($student->email, '@simagang.local')) {
            return back()->with('error', "Email {$student->nama_lengkap} belum diisi.");
        }

        // Karena password sudah di-hash, kita tidak bisa menampilkan password asli
        // Jadi kita sarankan admin untuk reset password dulu
        return back()->with('error', 'Untuk mengirim informasi akun, silakan gunakan fitur "Reset Password" terlebih dahulu.');
    }

    /**
     * Kirim informasi akun ke semua siswa yang belum menerima
     */
    public function resendAccountInfoAll()
    {
        // Ambil siswa dengan status tertentu (misalnya yang baru di-import)
        $students = User::where('role', 'siswa')
            ->where('status', 'menunggu')
            ->whereNotNull('email')
            ->where('email', 'not like', '%@simagang.local')
            ->get();

        if ($students->isEmpty()) {
            return back()->with('info', 'Tidak ada siswa dengan status menunggu yang memiliki email.');
        }

        $sent = 0;
        $failed = 0;
        $errors = [];

        foreach ($students as $student) {
            try {
                Mail::to($student->email)->send(new StudentAccountInfo($student));
                $sent++;
            } catch (\Exception $e) {
                $failed++;
                $errors[] = "{$student->nama_lengkap}: " . $e->getMessage();
                Log::error("Gagal mengirim email ke {$student->email}: " . $e->getMessage());
            }
        }

        $message = "Informasi akun berhasil dikirim ke $sent siswa via email.";
        if ($failed > 0) {
            $message .= " $failed gagal dikirim.";
        }

        return back()
            ->with('success', $message)
            ->with('import_errors', $errors);
    }
}
