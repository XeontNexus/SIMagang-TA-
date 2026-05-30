<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\PresenceDistanceSetting;
use Illuminate\Http\Request;

class PresenceDistanceSettingController extends Controller
{
    /**
     * Display the presence distance setting
     */
    public function index()
    {
        $setting = PresenceDistanceSetting::first() ?? PresenceDistanceSetting::create([
            'jarak_maksimal' => 500,
            'satuan' => 'meter',
            'deskripsi' => 'Jarak maksimal yang diizinkan untuk melakukan presensi dari lokasi magang',
            'aktif' => true,
        ]);

        return view('admin.presence.distance-setting', compact('setting'));
    }

    /**
     * Update the presence distance setting
     */
    public function update(Request $request)
    {
        $request->validate([
            'jarak_maksimal' => 'required|numeric|min:10|max:5000',
            'satuan' => 'required|in:meter,km',
            'deskripsi' => 'nullable|string|max:500',
            'aktif' => 'boolean',
        ]);

        $setting = PresenceDistanceSetting::first();

        if (!$setting) {
            $setting = PresenceDistanceSetting::create([
                'jarak_maksimal' => $request->jarak_maksimal,
                'satuan' => $request->satuan,
                'deskripsi' => $request->deskripsi,
                'aktif' => $request->boolean('aktif'),
            ]);
        } else {
            $setting->update([
                'jarak_maksimal' => $request->jarak_maksimal,
                'satuan' => $request->satuan,
                'deskripsi' => $request->deskripsi,
                'aktif' => $request->boolean('aktif'),
            ]);
        }

        return redirect()->route('admin.presence-distance.index')
            ->with('success', 'Pengaturan jarak presensi berhasil diperbarui!');
    }
}
