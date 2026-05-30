<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Presensi;
use App\Models\Logbook;
use App\Models\Jurusan;
use App\Models\Kelas;
use App\Models\GuruPembimbing;
use Carbon\Carbon;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index()
    {
        $stats = [
            'total_students' => User::where('role', 'siswa')->count(),
            'active_students' => User::where('role', 'siswa')->where('status', 'aktif')->count(),
            'menunggu_students' => User::where('role', 'siswa')->where('status', 'menunggu')->count(),
            'proses_students' => User::where('role', 'siswa')->where('status', 'proses')->count(),
            'total_today' => Presensi::whereDate('tanggal', Carbon::today())->count(),
            'hadir_today' => Presensi::whereDate('tanggal', Carbon::today())->where('status', 'hadir')->count(),
            'izin_today' => Presensi::whereDate('tanggal', Carbon::today())->where('status', 'izin')->count(),
            'sakit_today' => Presensi::whereDate('tanggal', Carbon::today())->where('status', 'sakit')->count(),
            'alpha_today' => Presensi::whereDate('tanggal', Carbon::today())->where('status', 'alpha')->count(),
            'pending_logbooks' => Logbook::where('status', 'submitted')->count(),
        ];

        $recent_students = User::where('role', 'siswa')
            ->latest()
            ->take(5)
            ->get();

        $recent_logbooks = Logbook::with('user')
            ->where('status', 'submitted')
            ->latest()
            ->take(5)
            ->get();

        return view('admin.dashboard', compact('stats', 'recent_students', 'recent_logbooks'));
    }
}
