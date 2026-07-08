<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Presensi;
use App\Services\PresensiRetentionService;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\StreamedResponse;

class PresensiController extends Controller
{
    public function index(Request $request)
    {
        PresensiRetentionService::cleanupExpired();

        $query = Presensi::with('user')
            ->whereDate('tanggal', '>=', PresensiRetentionService::earliestVisibleDate());

        if ($request->has('search')) {
            $search = $request->search;
            $query->whereHas('user', function ($q) use ($search) {
                $q->where('nama_lengkap', 'like', "%{$search}%")
                  ->orWhere('institusi', 'like', "%{$search}%");
            });
        }

        $presensis = $query
            ->join('users', 'presensis.user_id', '=', 'users.id')
            ->orderBy('users.nama_lengkap')
            ->orderByDesc('presensis.tanggal')
            ->select('presensis.*')
            ->paginate(20);

        return view('admin.presensi.index', compact('presensis'));
    }

    public function report(Request $request)
    {
        PresensiRetentionService::cleanupExpired();

        $startDate = PresensiRetentionService::earliestVisibleDate();
        $endDate = Carbon::today();

        $query = Presensi::whereBetween('tanggal', [$startDate, $endDate])
            ->with(['user.kelas', 'user.jurusan', 'user.mitra']);

        if ($request->filled('search')) {
            $search = $request->search;
            $query->whereHas('user', function ($q) use ($search) {
                $q->where('nama_lengkap', 'like', "%{$search}%")
                  ->orWhere('username', 'like', "%{$search}%")
                  ->orWhere('institusi', 'like', "%{$search}%");
            });
        }

        if ($request->filled('tanggal')) {
            $query->whereDate('tanggal', $request->tanggal);
        }

        $presensiReport = $query
            ->get()
            ->sortBy([
                fn ($presensi) => $presensi->tanggal->format('Y-m-d'),
                fn ($presensi) => $presensi->user?->nama_lengkap ?? '',
            ])
            ->values();

        return view('admin.presensi.report', compact('presensiReport', 'startDate', 'endDate'));
    }

    public function showBukti(Presensi $presensi)
    {
        if (!$presensi->hasBuktiFoto() || !Storage::disk('public')->exists($presensi->bukti_foto)) {
            abort(404, 'Bukti foto tidak ditemukan.');
        }

        $presensi->load('user');

        return view('admin.presensi.bukti', compact('presensi'));
    }

    public function downloadBukti(Presensi $presensi): StreamedResponse
    {
        if (!$presensi->hasBuktiFoto() || !Storage::disk('public')->exists($presensi->bukti_foto)) {
            abort(404, 'Bukti foto tidak ditemukan.');
        }

        $presensi->load('user');
        $extension = pathinfo($presensi->bukti_foto, PATHINFO_EXTENSION) ?: 'jpg';
        $filename = 'bukti_presensi_' . ($presensi->user->nisn ?? $presensi->user_id) . '_' . $presensi->tanggal->format('Ymd') . '.' . $extension;

        return Storage::disk('public')->download($presensi->bukti_foto, $filename);
    }

    public function detail(User $student, Request $request)
    {
        PresensiRetentionService::cleanupExpired();

        $bulan = $request->get('bulan', Carbon::now()->format('Y-m'));

        $presensis = Presensi::where('user_id', $student->id)
            ->whereDate('tanggal', '>=', PresensiRetentionService::earliestVisibleDate())
            ->whereMonth('tanggal', Carbon::parse($bulan)->month)
            ->whereYear('tanggal', Carbon::parse($bulan)->year)
            ->orderBy('tanggal')
            ->get();

        return view('admin.presensi.detail', compact('student', 'presensis', 'bulan'));
    }

    /**
     * Admin mengubah status presensi (misal alfa → hadir)
     */
    public function updateStatus(Request $request, Presensi $presensi)
    {
        $request->validate([
            'status'     => 'required|in:hadir,izin,sakit,alfa',
            'keterangan' => 'nullable|string|max:255',
            'jam_masuk'  => 'nullable|date_format:H:i',
            'jam_keluar' => 'nullable|date_format:H:i',
        ]);

        $data = [
            'status'     => $request->status,
            'keterangan' => $request->keterangan,
        ];

        // Jika diubah ke hadir dan ada jam masuk yang diisi, simpan
        if ($request->status === 'hadir') {
            if ($request->filled('jam_masuk')) {
                $data['jam_masuk'] = $request->jam_masuk . ':00';
            }
            if ($request->filled('jam_keluar')) {
                $data['jam_keluar'] = $request->jam_keluar . ':00';
            }
        }

        $presensi->update($data);

        return redirect()->back()->with('success',
            "✅ Status presensi {$presensi->user->nama_lengkap} ({$presensi->tanggal->translatedFormat('d F Y')}) berhasil diubah menjadi \"" . ucfirst($request->status) . "\"."
        );
    }
}
