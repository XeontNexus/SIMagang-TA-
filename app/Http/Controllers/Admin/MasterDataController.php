<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Jurusan;
use App\Models\Kelas;
use App\Models\GuruPembimbing;
use App\Models\Setting;
use Illuminate\Http\Request;

class MasterDataController extends Controller
{
    public function index()
    {
        $jurusans = Jurusan::orderBy('nama_jurusan')->get();
        $kelas = Kelas::with('jurusan')->orderBy('nama_kelas')->get();
        $gurus = GuruPembimbing::orderBy('nama_guru')->get();
        $radiusHijau = Setting::get('radius_hijau', 30);
        $radiusKuning = Setting::get('radius_kuning', 70);
        
        return view('admin.master.index', compact('jurusans', 'kelas', 'gurus', 'radiusHijau', 'radiusKuning'));
    }

    public function updateSettings(Request $request)
    {
        $request->validate([
            'radius_hijau' => 'required|numeric|min:1',
            'radius_kuning' => 'required|numeric|gt:radius_hijau',
        ]);

        Setting::set('radius_hijau', $request->radius_hijau);
        Setting::set('radius_kuning', $request->radius_kuning);

        return redirect()->route('admin.master.index')->with('success', 'Pengaturan presensi berhasil diperbarui!');
    }
}
