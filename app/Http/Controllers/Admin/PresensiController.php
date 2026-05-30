<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Presensi;
use Carbon\Carbon;
use Illuminate\Http\Request;

class PresensiController extends Controller
{
    public function index(Request $request)
    {
        $query = Presensi::with('user');

        if ($request->has('search')) {
            $search = $request->search;
            $query->whereHas('user', function($q) use ($search) {
                $q->where('nama_lengkap', 'like', "%{$search}%")
                  ->orWhere('institusi', 'like', "%{$search}%");
            });
        }
        
        $presensis = $query->latest()->paginate(20);
        return view('admin.presensi.index', compact('presensis'));
    }

    public function report(Request $request)
    {
        // Query data presensi hari ini
        $todayQuery = Presensi::whereDate('tanggal', Carbon::today())
            ->with(['user.kelas', 'user.jurusan', 'user.mitra']);

        if ($request->filled('search')) {
            $search = $request->search;
            $todayQuery->whereHas('user', function($q) use ($search) {
                $q->where('nama_lengkap', 'like', "%{$search}%")
                  ->orWhere('username', 'like', "%{$search}%")
                  ->orWhere('institusi', 'like', "%{$search}%");
            });
        }
        $todayPresensi = $todayQuery->latest('jam_masuk')->get();

        return view('admin.presensi.report', compact('todayPresensi'));
    }

    public function detail(User $student, Request $request)
    {
        $bulan = $request->get('bulan', Carbon::now()->format('Y-m'));
        
        $presensis = Presensi::where('user_id', $student->id)
            ->whereMonth('tanggal', Carbon::parse($bulan)->month)
            ->whereYear('tanggal', Carbon::parse($bulan)->year)
            ->orderBy('tanggal')
            ->get();

        return view('admin.presensi.detail', compact('student', 'presensis', 'bulan'));
    }
}
