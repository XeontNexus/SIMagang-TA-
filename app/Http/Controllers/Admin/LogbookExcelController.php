<?php

namespace App\Http\Controllers\Admin;

use App\Helpers\SpreadsheetEmbedHelper;
use App\Http\Controllers\Controller;
use App\Models\Setting;
use Illuminate\Http\Request;

class LogbookExcelController extends Controller
{
    public function index()
    {
        $excelUrl = Setting::get('logbook_excel_url', '');
        $embedUrl = $excelUrl ? SpreadsheetEmbedHelper::toEmbedUrl($excelUrl, true) : '';

        return view('admin.logbook-excel.index', compact('excelUrl', 'embedUrl'));
    }

    public function update(Request $request)
    {
        $request->validate([
            'logbook_excel_url' => 'required|url|max:1000',
        ]);

        Setting::set('logbook_excel_url', $request->logbook_excel_url);

        return redirect()
            ->route('admin.logbook-excel.index')
            ->with('success', 'Link Excel Logbook berhasil disimpan. Perubahan akan tampil realtime untuk siswa.');
    }
}
