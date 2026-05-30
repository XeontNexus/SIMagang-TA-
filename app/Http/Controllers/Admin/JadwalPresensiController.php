<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\JadwalPresensi;
use Illuminate\Http\Request;

class JadwalPresensiController extends Controller
{
    public function index(Request $request)
    {
        // All students for dropdown selection
        $students = User::where('role', 'siswa')->get();
        
        // All jadwal presensi
        $jadwals = JadwalPresensi::with('user')->latest()->get();

        return view('admin.jadwal-presensi.index', compact('students', 'jadwals'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'user_id' => 'required|exists:users,id',
            'hari' => 'required',
            'jam_masuk' => 'required|date_format:H:i',
            'jam_keluar' => 'required|date_format:H:i|after:jam_masuk',
        ]);

        JadwalPresensi::create([
            'user_id' => $request->user_id,
            'hari' => $request->hari,
            'jam_masuk' => $request->jam_masuk,
            'jam_keluar' => $request->jam_keluar,
            'bulan_mulai' => now(),
            'bulan_selesai' => now()->addMonths(6),
        ]);

        return back()->with('success', 'Jadwal presensi berhasil disimpan!');
    }

    public function show(User $student)
    {
        $jadwals = $student->jadwalPresensis()->orderBy('bulan_mulai')->get();
        return view('admin.jadwal-presensi.show', compact('student', 'jadwals'));
    }

    public function destroy(JadwalPresensi $jadwal)
    {
        try {
            $jadwal->delete();
            return back()->with('success', 'Jadwal berhasil dihapus!');
        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::error('Gagal menghapus jadwal presensi: ' . $e->getMessage());
            return back()->with('error', 'Gagal menghapus jadwal: ' . $e->getMessage());
        }
    }
}
