<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Helpers\SpreadsheetEmbedHelper;
use App\Models\Logbook;
use App\Models\Setting;
use Illuminate\Http\Request;

class LogbookController extends Controller
{
    public function index(Request $request)
    {
        $query = Logbook::with('user');

        // Exclude approved logbooks from approval list
        $query->where('status', '!=', 'approved');

        if ($request->has('status')) {
            $query->where('status', $request->status);
        }

        if ($request->has('search')) {
            $search = $request->search;
            $query->whereHas('user', function($q) use ($search) {
                $q->where('nama_lengkap', 'like', "%{$search}%");
            });
        }

        $logbooks = $query->latest()->paginate(20);

        // Get approved logbooks for data tab
        $approvedQuery = Logbook::with('user')->where('status', 'approved');
        if ($request->has('search') && strpos($request->input('search'), '') === 0) {
            $search = $request->search;
            $approvedQuery->whereHas('user', function($q) use ($search) {
                $q->where('nama_lengkap', 'like', "%{$search}%");
            });
        }
        $approvedLogbooks = $approvedQuery->latest()->get();

        // Get Excel Logbook data
        $excelUrl = Setting::get('logbook_excel_url', '');
        $embedUrl = $excelUrl ? SpreadsheetEmbedHelper::toEmbedUrl($excelUrl, true) : '';

        return view('admin.logbooks.index', compact('logbooks', 'approvedLogbooks', 'excelUrl', 'embedUrl'));
    }

    public function studentData(Request $request)
    {
        $query = Logbook::with('user')->where('status', 'approved');

        if ($request->has('search')) {
            $search = $request->search;
            $query->whereHas('user', function($q) use ($search) {
                $q->where('nama_lengkap', 'like', "%{$search}%");
            });
        }

        $studentLogbooks = $query->latest()->paginate(20);
        return view('admin.logbooks.student-data', compact('studentLogbooks'));
    }

    public function show(Logbook $logbook)
    {
        $logbook->load('user');
        return view('admin.logbooks.show', compact('logbook'));
    }

    public function approve(Logbook $logbook)
    {
        $logbook->update(['status' => 'approved']);
        return back()->with('success', 'Logbook berhasil disetujui!');
    }

    public function reject(Request $request, Logbook $logbook)
    {
        $request->validate(['catatan_admin' => 'required|string']);
        $logbook->update([
            'status' => 'rejected',
            'catatan_admin' => $request->catatan_admin
        ]);
        return back()->with('success', 'Logbook ditolak dengan catatan!');
    }
}
