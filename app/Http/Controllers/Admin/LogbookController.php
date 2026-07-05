<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Helpers\SpreadsheetEmbedHelper;
use App\Models\Logbook;
use App\Models\Setting;
use App\Services\NotificationService;
use Illuminate\Http\Request;

class LogbookController extends Controller
{
    public function index(Request $request)
    {
        $query = Logbook::with(['user.kelas', 'user.jurusan']);

        // Filter status: jika tidak ada filter, tampilkan semua
        if ($request->filled('status')) {
            $query->where('logbooks.status', $request->status);
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->whereHas('user', function($q) use ($search) {
                $q->where('nama_lengkap', 'like', "%{$search}%");
            });
        }

        $logbooks = $query
            ->join('users', 'logbooks.user_id', '=', 'users.id')
            ->orderBy('users.nama_lengkap')
            ->orderBy('logbooks.minggu_ke')
            ->select('logbooks.*')
            ->paginate(20);

        // Get approved logbooks for data tab
        $approvedQuery = Logbook::with(['user.kelas', 'user.jurusan'])->where('logbooks.status', 'approved');
        if ($request->filled('search')) {
            $search = $request->search;
            $approvedQuery->whereHas('user', function($q) use ($search) {
                $q->where('nama_lengkap', 'like', "%{$search}%");
            });
        }
        $approvedLogbooks = $approvedQuery
            ->join('users', 'logbooks.user_id', '=', 'users.id')
            ->orderBy('users.nama_lengkap')
            ->orderBy('logbooks.minggu_ke')
            ->select('logbooks.*')
            ->get();

        // Get Excel Logbook data
        $excelUrl = Setting::get('logbook_excel_url', '');
        $embedUrl = $excelUrl ? SpreadsheetEmbedHelper::toEmbedUrl($excelUrl, true) : '';

        return view('admin.logbooks.index', compact('logbooks', 'approvedLogbooks', 'excelUrl', 'embedUrl'));
    }

    public function studentData(Request $request)
    {
        $query = Logbook::with('user')->where('logbooks.status', 'approved');

        if ($request->has('search')) {
            $search = $request->search;
            $query->whereHas('user', function($q) use ($search) {
                $q->where('nama_lengkap', 'like', "%{$search}%");
            });
        }

        $studentLogbooks = $query
            ->join('users', 'logbooks.user_id', '=', 'users.id')
            ->orderBy('users.nama_lengkap')
            ->orderBy('logbooks.minggu_ke')
            ->select('logbooks.*')
            ->paginate(20);
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
        NotificationService::markRelatedAsRead('logbook', $logbook->id);
        NotificationService::create(
            $logbook->user_id,
            'Logbook Disetujui',
            "Logbook minggu ke-{$logbook->minggu_ke} Anda telah disetujui admin.",
            'success',
            'fa-book',
            route('student.logbooks.index'),
            'logbook',
            $logbook->id
        );
        return back()->with('success', 'Logbook berhasil disetujui!');
    }

    public function reject(Request $request, Logbook $logbook)
    {
        $request->validate(['catatan_admin' => 'required|string']);
        $logbook->update([
            'status' => 'rejected',
            'catatan_admin' => $request->catatan_admin
        ]);
        NotificationService::markRelatedAsRead('logbook', $logbook->id);
        NotificationService::create(
            $logbook->user_id,
            'Logbook Ditolak',
            "Logbook minggu ke-{$logbook->minggu_ke} ditolak: {$request->catatan_admin}",
            'danger',
            'fa-book',
            route('student.logbooks.index'),
            'logbook',
            $logbook->id
        );
        return back()->with('success', 'Logbook ditolak dengan catatan!');
    }
}
