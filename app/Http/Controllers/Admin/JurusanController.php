<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Jurusan;
use Illuminate\Http\Request;

class JurusanController extends Controller
{
    public function index()
    {
        $jurusans = Jurusan::all();
        return view('admin.master.jurusan', compact('jurusans'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'kode_jurusan' => 'required|string|max:20|unique:jurusans',
            'nama_jurusan' => 'required|string|max:100',
            'deskripsi' => 'nullable|string'
        ]);

        Jurusan::create($request->all());

        return redirect()->route('admin.master.index')->with('success', 'Data jurusan berhasil ditambahkan.');
    }

    public function update(Request $request, Jurusan $jurusan)
    {
        $request->validate([
            'kode_jurusan' => 'required|string|max:20|unique:jurusans,kode_jurusan,' . $jurusan->id,
            'nama_jurusan' => 'required|string|max:100',
            'deskripsi' => 'nullable|string'
        ]);

        $jurusan->update($request->all());

        return redirect()->route('admin.master.index')->with('success', 'Data jurusan berhasil diperbarui.');
    }

    public function destroy(Jurusan $jurusan)
    {
        try {
            $jurusan->delete();
            return redirect()->route('admin.master.index')->with('success', 'Data jurusan berhasil dihapus.');
        } catch (\Exception $e) {
            return redirect()->route('admin.master.index')->with('error', 'Gagal menghapus jurusan, pastikan tidak ada data yang terkait.');
        }
    }
}
