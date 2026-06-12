<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\LocationChangeRequest;
use App\Models\StudentNotification;
use App\Services\NotificationService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class LocationChangeRequestController extends Controller
{
    public function index()
    {
        $requests = LocationChangeRequest::with('user')
            ->join('users', 'location_change_requests.user_id', '=', 'users.id')
            ->orderBy('users.nama_lengkap')
            ->select('location_change_requests.*')
            ->paginate(20);

        $pendingCount = LocationChangeRequest::pending()->count();

        return view('admin.location-requests.index', compact('requests', 'pendingCount'));
    }

    public function approve(LocationChangeRequest $locationRequest, Request $request)
    {
        if ($locationRequest->status !== 'pending') {
            return back()->with('error', 'Permintaan sudah diproses sebelumnya.');
        }

        $student = $locationRequest->user;
        $student->update([
            'gmap_magang' => $locationRequest->new_gmap_magang,
            'latitude' => $locationRequest->new_latitude,
            'longitude' => $locationRequest->new_longitude,
        ]);
        $student->syncStudentStatus();

        $locationRequest->update([
            'status' => 'approved',
            'admin_note' => $request->input('admin_note'),
            'reviewed_by' => Auth::id(),
            'reviewed_at' => now(),
        ]);

        NotificationService::markRelatedAsRead('location_change_request', $locationRequest->id);

        StudentNotification::notify(
            $student->id,
            'Perubahan Lokasi Disetujui',
            'Admin menyetujui perubahan titik koordinat lokasi magang Anda. Koordinat baru sudah aktif.',
            'success'
        );

        return back()->with('success', 'Permintaan perubahan lokasi disetujui.');
    }

    public function reject(LocationChangeRequest $locationRequest, Request $request)
    {
        $request->validate(['admin_note' => 'required|string|max:500']);

        if ($locationRequest->status !== 'pending') {
            return back()->with('error', 'Permintaan sudah diproses sebelumnya.');
        }

        $locationRequest->update([
            'status' => 'rejected',
            'admin_note' => $request->admin_note,
            'reviewed_by' => Auth::id(),
            'reviewed_at' => now(),
        ]);

        NotificationService::markRelatedAsRead('location_change_request', $locationRequest->id);

        StudentNotification::notify(
            $locationRequest->user_id,
            'Perubahan Lokasi Ditolak',
            'Admin menolak perubahan titik koordinat: ' . $request->admin_note,
            'danger'
        );

        return back()->with('success', 'Permintaan perubahan lokasi ditolak.');
    }
}
