<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Models\Presensi;
use App\Models\Logbook;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        
        $today = Carbon::today();
        $todayPresensi = Presensi::where('user_id', $user->id)
            ->whereDate('tanggal', $today)
            ->first();

        $stats = [
            'hadir' => Presensi::where('user_id', $user->id)
                ->whereMonth('tanggal', $today->month)
                ->whereYear('tanggal', $today->year)
                ->where('status', 'hadir')
                ->count(),
            'izin' => Presensi::where('user_id', $user->id)
                ->whereMonth('tanggal', $today->month)
                ->whereYear('tanggal', $today->year)
                ->where('status', 'izin')
                ->count(),
            'sakit' => Presensi::where('user_id', $user->id)
                ->whereMonth('tanggal', $today->month)
                ->whereYear('tanggal', $today->year)
                ->where('status', 'sakit')
                ->count(),
            'alpha' => Presensi::where('user_id', $user->id)
                ->whereMonth('tanggal', $today->month)
                ->whereYear('tanggal', $today->year)
                ->where('status', 'alpha')
                ->count(),
        ];

        $recentLogbooks = Logbook::where('user_id', $user->id)
            ->latest()
            ->take(5)
            ->get();

        // Get pending location change requests for dashboard persistent notification
        $pendingLocationRequests = \App\Models\LocationChangeRequest::where('user_id', $user->id)
            ->pending()
            ->get();

        return view('student.dashboard', compact('todayPresensi', 'stats', 'recentLogbooks', 'pendingLocationRequests'));
    }
}
