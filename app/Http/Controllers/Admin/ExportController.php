<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\GuruPembimbing;
use App\Models\Kelas;
use App\Models\Logbook;
use App\Models\User;
use Illuminate\Http\Request;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Color;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\NumberFormat;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use Symfony\Component\HttpFoundation\StreamedResponse;

class ExportController extends Controller
{
    public function exportExcel()
    {
        $students = $this->studentsQuery()->get();

        return $this->downloadStudentsSpreadsheet(
            $students,
            'Data_Semua_Siswa_' . date('Ymd_His') . '.xlsx',
            'Semua Siswa'
        );
    }

    public function exportByGuru(Request $request)
    {
        $request->validate([
            'guru_pembimbing_id' => 'required|exists:guru_pembimbings,id',
        ]);

        $guru = GuruPembimbing::findOrFail($request->guru_pembimbing_id);
        $students = $this->studentsQuery()
            ->where('guru_pembimbing_id', $guru->id)
            ->get();

        if ($students->isEmpty()) {
            return back()->with('error', 'Tidak ada siswa bimbingan untuk guru pembimbing ini.');
        }

        $slug = preg_replace('/[^a-zA-Z0-9]+/', '_', $guru->nama_guru);

        return $this->downloadStudentsSpreadsheet(
            $students,
            'Data_Siswa_Guru_' . $slug . '_' . date('Ymd_His') . '.xlsx',
            'Guru: ' . $guru->nama_guru
        );
    }

    public function exportByKelas(Request $request)
    {
        $request->validate([
            'kelas_id' => 'required|exists:kelas,id',
        ]);

        $kelas = Kelas::with('jurusan')->findOrFail($request->kelas_id);
        $students = $this->studentsQuery()
            ->where('kelas_id', $kelas->id)
            ->get();

        if ($students->isEmpty()) {
            return back()->with('error', 'Tidak ada data siswa di kelas yang dipilih.');
        }

        $label = ($kelas->tingkat ?? '') . ' ' . $kelas->nama_kelas;

        return $this->downloadStudentsSpreadsheet(
            $students,
            'Data_Siswa_Kelas_' . preg_replace('/[^a-zA-Z0-9]+/', '_', $label) . '_' . date('Ymd_His') . '.xlsx',
            'Kelas: ' . trim($label)
        );
    }

    public function exportLogbook(Request $request)
    {
        $request->validate([
            'kelas_id' => 'nullable|exists:kelas,id',
            'guru_pembimbing_id' => 'nullable|exists:guru_pembimbings,id',
            'status' => 'nullable|in:draft,submitted,approved,rejected',
        ]);

        $query = Logbook::with(['user.kelas', 'user.jurusan', 'user.guruPembimbing'])
            ->whereHas('user', function ($q) use ($request) {
                $q->where('role', 'siswa');
                if ($request->filled('kelas_id')) {
                    $q->where('kelas_id', $request->kelas_id);
                }
                if ($request->filled('guru_pembimbing_id')) {
                    $q->where('guru_pembimbing_id', $request->guru_pembimbing_id);
                }
            });

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $logbooks = $query->get()->sortBy([
            fn ($logbook) => $logbook->user?->nama_lengkap ?? '',
            fn ($logbook) => $logbook->minggu_ke,
        ])->values();

        if ($logbooks->isEmpty()) {
            return back()->with('error', 'Tidak ada data logbook untuk filter yang dipilih.');
        }

        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle('Laporan Logbook');

        $headers = [
            'No', 'Nama Siswa', 'NISN', 'Institusi', 'Jurusan', 'Kelas',
            'Mitra Magang', 'Minggu Ke', 'Tanggal Mulai', 'Tanggal Selesai',
            'Kegiatan', 'Deskripsi', 'Hasil', 'Kendala', 'Solusi', 'Status', 'Catatan Admin',
        ];

        $this->applyHeaderStyle($sheet, $headers);

        $row = 2;
        foreach ($logbooks as $index => $logbook) {
            $user = $logbook->user;
            $kelasLabel = $user?->kelas
                ? trim(($user->kelas->tingkat ?? '') . ' ' . ($user->kelas->nama_kelas ?? ''))
                : '-';

            $sheet->fromArray([
                $index + 1,
                $user?->nama_lengkap ?? '-',
                $user?->nisn ?? '-',
                $user?->institusi ?? '-',
                $user?->jurusan?->nama_jurusan ?? $user?->jurusan ?? '-',
                $kelasLabel,
                $user?->mitra_magang ?? '-',
                $logbook->minggu_ke,
                $logbook->tanggal_mulai?->format('d/m/Y') ?? '-',
                $logbook->tanggal_selesai?->format('d/m/Y') ?? '-',
                $logbook->kegiatan ?? '-',
                $logbook->deskripsi ?? '-',
                $logbook->hasil ?? '-',
                $logbook->kendala ?? '-',
                $logbook->solusi ?? '-',
                ucfirst($logbook->status),
                $logbook->catatan_admin ?? '-',
            ], null, 'A' . $row);

            $sheet->getStyle('C' . $row)->getNumberFormat()->setFormatCode(NumberFormat::FORMAT_TEXT);
            $row++;
        }

        $this->autoSizeColumns($sheet, count($headers));
        $this->applyDataBorders($sheet, 1, $row - 1, count($headers));

        $suffix = date('Ymd_His');
        if ($request->filled('kelas_id')) {
            $suffix = 'Kelas_' . $request->kelas_id . '_' . $suffix;
        }

        return $this->streamSpreadsheet($spreadsheet, 'Laporan_Logbook_' . $suffix . '.xlsx');
    }

    private function studentsQuery()
    {
        return User::where('role', 'siswa')
            ->with(['jurusan', 'kelas', 'guruPembimbing'])
            ->orderBy('nama_lengkap');
    }

    private function downloadStudentsSpreadsheet($students, string $filename, string $sheetTitle): StreamedResponse
    {
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle(mb_substr($sheetTitle, 0, 31));

        $headers = [
            'No', 'Nama Lengkap', 'NISN', 'Username', 'Institusi', 'Jurusan', 'Kelas',
            'Mitra Magang', 'Alamat Magang', 'Tgl Mulai', 'Tgl Selesai', 'No HP',
            'Guru Pembimbing', 'Pembimbing Lapangan', 'No HP Pembimbing Lapangan', 'Status',
        ];

        $this->applyHeaderStyle($sheet, $headers);

        $row = 2;
        foreach ($students as $index => $student) {
            $kelasLabel = $student->kelas
                ? trim(($student->kelas->tingkat ?? '') . ' ' . ($student->kelas->nama_kelas ?? ''))
                : ($student->kelas ?? '-');

            $sheet->fromArray([
                $index + 1,
                $student->nama_lengkap,
                $student->nisn ?? '-',
                $student->username,
                $student->institusi ?? '-',
                $student->jurusan?->nama_jurusan ?? $student->jurusan ?? '-',
                $kelasLabel,
                $student->mitra_magang ?? '-',
                $student->alamat_magang ?? '-',
                $student->tanggal_mulai?->format('d/m/Y') ?? '-',
                $student->tanggal_selesai?->format('d/m/Y') ?? '-',
                $student->no_hp ?? '-',
                $student->guruPembimbing?->nama_guru ?? '-',
                $student->pembimbing_lapangan ?? '-',
                $student->no_hp_pembimbing_lapangan ?? '-',
                ucfirst($student->status),
            ], null, 'A' . $row);

            $sheet->getStyle('C' . $row)->getNumberFormat()->setFormatCode(NumberFormat::FORMAT_TEXT);
            $sheet->getStyle('L' . $row)->getNumberFormat()->setFormatCode(NumberFormat::FORMAT_TEXT);
            $sheet->getStyle('O' . $row)->getNumberFormat()->setFormatCode(NumberFormat::FORMAT_TEXT);
            $row++;
        }

        $this->autoSizeColumns($sheet, count($headers));
        $this->applyDataBorders($sheet, 1, max(1, $row - 1), count($headers));

        return $this->streamSpreadsheet($spreadsheet, $filename);
    }

    private function applyHeaderStyle($sheet, array $headers): void
    {
        $sheet->fromArray($headers, null, 'A1');
        $lastCol = chr(ord('A') + count($headers) - 1);
        $sheet->getStyle('A1:' . $lastCol . '1')->applyFromArray([
            'font' => ['bold' => true, 'color' => ['argb' => Color::COLOR_WHITE], 'size' => 11],
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER],
            'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['argb' => '198754']],
        ]);
        $sheet->getRowDimension(1)->setRowHeight(22);
    }

    private function autoSizeColumns($sheet, int $columnCount): void
    {
        for ($i = 0; $i < $columnCount; $i++) {
            $sheet->getColumnDimension(chr(ord('A') + $i))->setAutoSize(true);
        }
    }

    private function applyDataBorders($sheet, int $startRow, int $endRow, int $columnCount): void
    {
        if ($endRow < $startRow) {
            return;
        }
        $lastCol = chr(ord('A') + $columnCount - 1);
        $sheet->getStyle('A' . $startRow . ':' . $lastCol . $endRow)->applyFromArray([
            'borders' => [
                'allBorders' => [
                    'borderStyle' => Border::BORDER_THIN,
                    'color' => ['argb' => 'D3D3D3'],
                ],
            ],
        ]);
    }

    private function streamSpreadsheet(Spreadsheet $spreadsheet, string $filename): StreamedResponse
    {
        return response()->streamDownload(function () use ($spreadsheet) {
            $writer = new Xlsx($spreadsheet);
            $writer->save('php://output');
        }, $filename, [
            'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
            'Cache-Control' => 'max-age=0',
        ]);
    }
}
