<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\GuruPembimbing;
use Illuminate\Http\Request;

class GuruPembimbingController extends Controller
{
    public function index()
    {
        $gurus = GuruPembimbing::all();
        return view('admin.master.guru', compact('gurus'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'nama_guru' => 'required|string|max:100',
            'nip' => 'nullable|string|max:50|unique:guru_pembimbings',
            'no_hp' => 'nullable|string|max:20',
            'status' => 'required|in:active,inactive'
        ]);

        GuruPembimbing::create($request->all());

        return redirect()->route('admin.master.index')->with('success', 'Data guru pembimbing berhasil ditambahkan.');
    }

    public function update(Request $request, GuruPembimbing $guru)
    {
        $request->validate([
            'nama_guru' => 'required|string|max:100',
            'nip' => 'nullable|string|max:50|unique:guru_pembimbings,nip,' . $guru->id,
            'no_hp' => 'nullable|string|max:20',
            'status' => 'required|in:active,inactive'
        ]);

        $guru->update($request->all());

        return redirect()->route('admin.master.index')->with('success', 'Data guru pembimbing berhasil diperbarui.');
    }

    public function destroy(GuruPembimbing $guru)
    {
        try {
            $guru->delete();
            return redirect()->route('admin.master.index')->with('success', 'Data guru pembimbing berhasil dihapus.');
        } catch (\Exception $e) {
            return redirect()->route('admin.master.index')->with('error', 'Gagal menghapus guru pembimbing, pastikan tidak ada data yang terkait.');
        }
    }

    // API endpoint untuk get guru details (used for auto-fill no_hp)
    public function getDetails($id)
    {
        $guru = GuruPembimbing::find($id);
        
        if (!$guru) {
            return response()->json(['error' => 'Guru tidak ditemukan'], 404);
        }

        return response()->json([
            'id' => $guru->id,
            'nama_guru' => $guru->nama_guru,
            'no_hp' => $guru->no_hp,
            'nip' => $guru->nip,
            'email' => $guru->email,
        ]);
    }
}
