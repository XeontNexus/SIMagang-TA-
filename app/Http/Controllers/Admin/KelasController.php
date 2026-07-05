<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Kelas;
use App\Models\Jurusan;
use Illuminate\Http\Request;

class KelasController extends Controller
{
    public function index()
    {
        $kelas = Kelas::with('jurusan')->get();
        $jurusans = Jurusan::all();
        return view('admin.master.kelas', compact('kelas', 'jurusans'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'nama_kelas' => 'required|string|max:2|min:1|regex:/^[a-zA-Z0-9]{1,2}$/',
            'tingkat' => 'required|string|max:20',
            'jurusan_id' => 'required|exists:jurusans,id'
        ], [
            'nama_kelas.max' => 'Nama Kelas maksimal 2 karakter.',
            'nama_kelas.min' => 'Nama Kelas minimal 1 karakter.',
            'nama_kelas.regex' => 'Nama Kelas hanya boleh berisi huruf atau angka (maks. 2 karakter).',
        ]);

        Kelas::create($request->all());

        return redirect()->route('admin.master.index')->with('success', 'Data kelas berhasil ditambahkan.');
    }

    public function update(Request $request, Kelas $kela) // Laravel passes 'kela' by default due to singularization
    {
        $request->validate([
            'nama_kelas' => 'required|string|max:2|min:1|regex:/^[a-zA-Z0-9]{1,2}$/',
            'tingkat' => 'required|string|max:20',
            'jurusan_id' => 'required|exists:jurusans,id'
        ], [
            'nama_kelas.max' => 'Nama Kelas maksimal 2 karakter.',
            'nama_kelas.min' => 'Nama Kelas minimal 1 karakter.',
            'nama_kelas.regex' => 'Nama Kelas hanya boleh berisi huruf atau angka (maks. 2 karakter).',
        ]);

        $kela->update($request->all());

        return redirect()->route('admin.master.index')->with('success', 'Data kelas berhasil diperbarui.');
    }

    public function destroy(Kelas $kela)
    {
        try {
            $kela->delete();
            return redirect()->route('admin.master.index')->with('success', 'Data kelas berhasil dihapus.');
        } catch (\Exception $e) {
            return redirect()->route('admin.master.index')->with('error', 'Gagal menghapus kelas, pastikan tidak ada data yang terkait.');
        }
    }
}
