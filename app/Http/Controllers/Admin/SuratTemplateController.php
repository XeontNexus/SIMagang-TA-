<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\SuratTemplate;
use Illuminate\Http\Request;

class SuratTemplateController extends Controller
{
    // Manage templates (CRUD)
    public function indexTemplates()
    {
        $templates = SuratTemplate::latest()->get();
        return view('admin.surat.templates.index', compact('templates'));
    }

    public function createTemplate()
    {
        return view('admin.surat.templates.create');
    }

    public function storeTemplate(Request $request)
    {
        $request->validate([
            'nama_template' => 'required|string|max:255',
            'isi_template' => 'required|string',
            'deskripsi' => 'nullable|string',
        ]);

        SuratTemplate::create([
            'nama_template' => $request->nama_template,
            'isi_template' => $request->isi_template,
            'deskripsi' => $request->deskripsi,
        ]);

        return redirect()->route('admin.surat-template.index')
            ->with('success', 'Template surat berhasil dibuat.');
    }

    public function editTemplateItem(SuratTemplate $template)
    {
        return view('admin.surat.templates.edit', compact('template'));
    }

    public function updateTemplateItem(Request $request, SuratTemplate $template)
    {
        $request->validate([
            'nama_template' => 'required|string|max:255',
            'isi_template' => 'required|string',
            'deskripsi' => 'nullable|string',
        ]);

        $template->update([
            'nama_template' => $request->nama_template,
            'isi_template' => $request->isi_template,
            'deskripsi' => $request->deskripsi,
        ]);

        return redirect()->route('admin.surat-template.index')
            ->with('success', 'Template surat berhasil diperbarui.');
    }

    public function deleteTemplate(SuratTemplate $template)
    {
        try {
            $template->delete();
            return redirect()->route('admin.surat-template.index')
                ->with('success', 'Template surat berhasil dihapus.');
        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::error('Gagal menghapus template surat: ' . $e->getMessage());
            return redirect()->route('admin.surat-template.index')
                ->with('error', 'Gagal menghapus template: ' . $e->getMessage());
        }
    }
}
