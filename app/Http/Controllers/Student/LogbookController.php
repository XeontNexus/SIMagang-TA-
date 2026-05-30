<?php

namespace App\Http\Controllers\Student;

use App\Helpers\SpreadsheetEmbedHelper;
use App\Http\Controllers\Controller;
use App\Models\Logbook;
use App\Models\Setting;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class LogbookController extends Controller
{
    public function index(Request $request)
    {
        $user = Auth::user();
        
        // Get all logbooks for the user
        $allLogbooks = Logbook::where('user_id', $user->id)->orderBy('tanggal_mulai', 'asc')->get();
        
        // Determine current year and starting month for filter (per 2 bulan)
        $currentYear = $request->input('tahun', now()->year);
        $startMonth = $request->input('bulan', now()->month);
        
        // Calculate end month (start + 1 for 2 months period)
        $endMonth = $startMonth + 1;
        $endYear = $currentYear;
        if ($endMonth > 12) {
            $endMonth = 1;
            $endYear = $currentYear + 1;
        }
        
        // Filter logbooks for 2-month period, sorted by month then week
        $logbooks = $allLogbooks->filter(function ($logbook) use ($currentYear, $startMonth, $endYear, $endMonth) {
            $logYear = $logbook->tanggal_mulai->year;
            $logMonth = $logbook->tanggal_mulai->month;
            
            if ($currentYear == $endYear) {
                // Both months in same year
                return $logYear == $currentYear && $logMonth >= $startMonth && $logMonth <= $endMonth;
            } else {
                // Months span two years
                return ($logYear == $currentYear && $logMonth >= $startMonth) || 
                       ($logYear == $endYear && $logMonth <= $endMonth);
            }
        })->values();
        
        // Sort by month, then by week
        $logbooks = $logbooks->sortBy(function ($logbook) {
            return $logbook->tanggal_mulai->month . '-' . $logbook->minggu_ke;
        })->values();
        
        // Get all available years from logbooks
        $availableYears = $allLogbooks->map(function ($logbook) {
            return $logbook->tanggal_mulai->year;
        })->unique()->sort()->values()->toArray();
        
        // Get all unique months for current year from all logbooks
        $availableMonthsInYear = $allLogbooks->filter(function ($logbook) use ($currentYear) {
            return $logbook->tanggal_mulai->year == $currentYear;
        })->map(function ($logbook) {
            return $logbook->tanggal_mulai->month;
        })->unique()->sort()->values()->toArray();
        
        // Get month labels for display
        $bulanLabels = ['', 'Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni', 'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'];
        
        // Previous and next period (2 months)
        if ($startMonth <= 2) {
            $prevMonth = $startMonth + 10;
            $prevYear = $currentYear - 1;
        } else {
            $prevMonth = $startMonth - 2;
            $prevYear = $currentYear;
        }
        
        if ($startMonth >= 11) {
            $nextMonth = $startMonth - 10;
            $nextYear = $currentYear + 1;
        } else {
            $nextMonth = $startMonth + 2;
            $nextYear = $currentYear;
        }
        
        // Build period label (2 months)
        $periodLabel = $bulanLabels[$startMonth] . ' - ' . $bulanLabels[$endMonth] . ' ' . $currentYear;
        if ($currentYear != $endYear) {
            $periodLabel = $bulanLabels[$startMonth] . ' ' . $currentYear . ' - ' . $bulanLabels[$endMonth] . ' ' . $endYear;
        }
        
        $excelUrl = Setting::get('logbook_excel_url', '');
        $embedUrl = $excelUrl ? SpreadsheetEmbedHelper::toEmbedUrl($excelUrl, false) : '';

        return view('student.logbooks.index', compact(
            'logbooks', 
            'embedUrl',
            'currentYear',
            'startMonth',
            'endMonth',
            'availableYears',
            'availableMonthsInYear',
            'bulanLabels',
            'prevMonth',
            'prevYear',
            'nextMonth',
            'nextYear',
            'periodLabel'
        ));
    }

    public function create()
    {
        $user = Auth::user();
        $existingWeeks = Logbook::where('user_id', $user->id)->pluck('minggu_ke')->toArray();

        // Calculate current week based on tanggal_mulai
        $startDate = Carbon::parse($user->tanggal_mulai);
        $currentWeek = $startDate->diffInWeeks(Carbon::now()) + 1;

        // Get user PKL dates for display
        $userTanggalMulai = $user->tanggal_mulai;
        $userTanggalSelesai = $user->tanggal_selesai;

        return view('student.logbooks.create', compact('existingWeeks', 'currentWeek', 'userTanggalMulai', 'userTanggalSelesai'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'minggu_ke' => 'required|integer|min:1|max:5',
            'bulan' => 'required|integer|min:1|max:12',
            'kegiatan' => 'required|string',
            'hasil' => 'nullable|string',
            'kendala' => 'nullable|string',
            'solusi' => 'nullable|string',
        ]);

        $user = Auth::user();

        // Check for duplicate: same month and week
        $existing = Logbook::where('user_id', $user->id)
            ->whereMonth('tanggal_mulai', $request->bulan)
            ->where('minggu_ke', $request->minggu_ke)
            ->first();

        if ($existing) {
            return back()->withErrors(['minggu_ke' => 'Logbook untuk bulan dan minggu ini sudah ada.'])->withInput();
        }

        // Calculate dates based on selected month
        $bulan = $request->bulan;
        $tahun = now()->year; // Gunakan tahun sekarang
        
        $tanggalMulai = Carbon::createFromDate($tahun, $bulan, 1);
        $tanggalSelesai = $tanggalMulai->copy()->endOfMonth();

        Logbook::create([
            'user_id' => $user->id,
            'minggu_ke' => $request->minggu_ke,
            'tanggal_mulai' => $tanggalMulai,
            'tanggal_selesai' => $tanggalSelesai,
            'kegiatan' => $request->kegiatan,
            'hasil' => $request->hasil,
            'kendala' => $request->kendala,
            'solusi' => $request->solusi,
            'status' => 'submitted',
        ]);

        return redirect()->route('student.logbooks.index')
            ->with('success', 'Logbook berhasil disimpan!');
    }

    public function show(Logbook $logbook)
    {
        $this->authorizeAccess($logbook);
        return view('student.logbooks.show', compact('logbook'));
    }

    public function edit(Logbook $logbook)
    {
        $this->authorizeAccess($logbook);
        
        if ($logbook->status === 'approved') {
            return redirect()->route('student.logbooks.index')
                ->with('error', 'Logbook yang sudah disetujui tidak dapat diedit.');
        }

        return view('student.logbooks.edit', compact('logbook'));
    }

    public function update(Request $request, Logbook $logbook)
    {
        $this->authorizeAccess($logbook);

        if ($logbook->status === 'approved') {
            return redirect()->route('student.logbooks.index')
                ->with('error', 'Logbook yang sudah disetujui tidak dapat diedit.');
        }

        $request->validate([
            'tanggal_mulai' => 'required|date',
            'tanggal_selesai' => 'required|date|after_or_equal:tanggal_mulai',
            'kegiatan' => 'required|string',
            'hasil' => 'nullable|string',
            'kendala' => 'nullable|string',
            'solusi' => 'nullable|string',
        ]);

        $logbook->update($request->only([
            'tanggal_mulai', 'tanggal_selesai', 'kegiatan',
            'hasil', 'kendala', 'solusi'
        ]));

        return redirect()->route('student.logbooks.index')->with('success', 'Logbook berhasil diupdate!');
    }

    public function submit(Logbook $logbook)
    {
        $this->authorizeAccess($logbook);

        if ($logbook->status !== 'draft') {
            return redirect()->route('student.logbooks.index')
                ->with('error', 'Hanya logbook dengan status draft yang dapat disubmit.');
        }

        $logbook->update(['status' => 'submitted']);

        return redirect()->route('student.logbooks.index')
            ->with('success', 'Logbook berhasil disubmit untuk approval!');
    }

    public function destroy(Logbook $logbook)
    {
        $this->authorizeAccess($logbook);

        if ($logbook->status === 'approved') {
            return redirect()->route('student.logbooks.index')
                ->with('error', 'Logbook yang sudah disetujui tidak dapat dihapus.');
        }

        $logbook->delete();
        return redirect()->route('student.logbooks.index')->with('success', 'Logbook berhasil dihapus!');
    }

    private function authorizeAccess(Logbook $logbook)
    {
        if ($logbook->user_id !== Auth::id()) {
            abort(403, 'Unauthorized access.');
        }
    }

    public function ebook()
    {
        $currentUser = Auth::user();

        // Kakak kelas = siswa dengan jurusan yang sama yang sudah menyelesaikan magang
        $seniorStudents = User::where('role', 'siswa')
            ->where('jurusan_id', $currentUser->jurusan_id)
            ->where('id', '!=', $currentUser->id)
            ->where(function ($query) {
                $query->whereNotNull('tanggal_selesai')
                      ->whereDate('tanggal_selesai', '<', Carbon::today());
            })
            ->orWhere(function ($query) {
                $query->where('status', 'completed');
            })
            ->withCount(['logbooks' => function ($query) {
                $query->where('status', 'approved');
            }])
            ->orderBy('tanggal_selesai', 'desc')
            ->paginate(12);

        return view('student.ebook.index', compact('seniorStudents'));
    }

    public function ebookDetail(User $user)
    {
        $currentUser = Auth::user();

        // Pastikan hanya bisa lihat kakak kelas dari jurusan yang sama
        if ($user->role !== 'siswa' || $user->jurusan_id !== $currentUser->jurusan_id || $user->id === $currentUser->id) {
            abort(403, 'Unauthorized access.');
        }

        $logbooks = Logbook::where('user_id', $user->id)
            ->where('status', 'approved')
            ->orderBy('minggu_ke', 'asc')
            ->get();

        return view('student.ebook.detail', compact('user', 'logbooks'));
    }
}
