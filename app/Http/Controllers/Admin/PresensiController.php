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

    /**
     * Laporan presensi: tampilkan SEMUA siswa aktif untuk tanggal terpilih,
     * termasuk yang belum presensi (status virtual 'belum_presensi').
     * Default tanggal = hari ini. Bisa filter ke 7 hari ke belakang.
     */
    public function report(Request $request)
    {
        PresensiRetentionService::cleanupExpired();

        $startDate = PresensiRetentionService::earliestVisibleDate();
        $endDate   = Carbon::today();

        // Default tanggal = hari ini
        $selectedDate = $request->filled('tanggal')
            ? Carbon::parse($request->tanggal)
            : Carbon::today();

        // Pastikan tanggal dalam rentang yang diizinkan
        if ($selectedDate->lt($startDate)) {
            $selectedDate = $startDate;
        }
        if ($selectedDate->gt($endDate)) {
            $selectedDate = $endDate;
        }

        // Ambil semua siswa aktif (kecuali rejected & pending)
        $siswaQuery = User::where('role', 'siswa')
            ->whereNotIn('status', ['rejected', 'pending'])
            ->with(['kelas', 'jurusan', 'mitra']);

        if ($request->filled('search')) {
            $search = $request->search;
            $siswaQuery->where(function ($q) use ($search) {
                $q->where('nama_lengkap', 'like', "%{$search}%")
                  ->orWhere('username', 'like', "%{$search}%")
                  ->orWhere('institusi', 'like', "%{$search}%");
            });
        }

        $semuaSiswa = $siswaQuery->orderBy('nama_lengkap')->get();

        // Ambil data presensi di tanggal terpilih
        $presensiByUserId = Presensi::whereDate('tanggal', $selectedDate)
            ->with(['user.kelas', 'user.jurusan', 'user.mitra'])
            ->get()
            ->keyBy('user_id');

        // Gabungkan: siswa yang ada presensi → data presensi, yang tidak → status 'belum_presensi'
        $presensiReport = $semuaSiswa->map(function ($siswa) use ($presensiByUserId, $selectedDate) {
            if ($presensiByUserId->has($siswa->id)) {
                $presensi = $presensiByUserId->get($siswa->id);
                $presensi->setRelation('user', $siswa); // Ensure loaded
                return [
                    'type'    => 'presensi',
                    'presensi'=> $presensi,
                    'user'    => $siswa,
                ];
            } else {
                return [
                    'type'    => 'belum_presensi',
                    'presensi'=> null,
                    'user'    => $siswa,
                    'tanggal' => $selectedDate,
                ];
            }
        });

        return view('admin.presensi.report', compact(
            'presensiReport', 'startDate', 'endDate', 'selectedDate'
        ));
    }

    /**
     * Admin menambahkan presensi baru untuk siswa yang belum presensi.
     */
    public function store(Request $request)
    {
        $request->validate([
            'user_id'    => 'required|exists:users,id',
            'tanggal'    => 'required|date',
            'status'     => 'required|in:hadir,izin,sakit,alfa',
            'keterangan' => 'nullable|string|max:255',
            'jam_masuk'  => 'nullable|date_format:H:i',
            'jam_keluar' => 'nullable|date_format:H:i',
        ]);

        // Cek apakah sudah ada presensi di tanggal itu
        $existing = Presensi::where('user_id', $request->user_id)
            ->whereDate('tanggal', $request->tanggal)
            ->first();

        if ($existing) {
            return redirect()->back()->with('error', 'Siswa ini sudah memiliki catatan presensi pada tanggal tersebut.');
        }

        $student = User::findOrFail($request->user_id);

        Presensi::create([
            'user_id'    => $request->user_id,
            'tanggal'    => $request->tanggal,
            'status'     => $request->status,
            'keterangan' => $request->keterangan,
            'jam_masuk'  => $request->filled('jam_masuk') ? $request->jam_masuk . ':00' : null,
            'jam_keluar' => $request->filled('jam_keluar') ? $request->jam_keluar . ':00' : null,
        ]);

        return redirect()->back()->with('success',
            "✅ Presensi {$student->nama_lengkap} ({$request->tanggal}) berhasil ditambahkan dengan status \"" . ucfirst($request->status) . "\"."
        );
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
