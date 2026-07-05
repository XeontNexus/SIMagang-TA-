<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Models\Presensi;
use App\Models\JadwalPresensi;
use App\Models\Setting;
use App\Models\LocationChangeRequest;
use App\Helpers\LocationHelper;
use App\Services\NotificationService;
use App\Services\PresensiRetentionService;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class PresensiController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        $today = Carbon::today();

        $presensiHariIni = Presensi::where('user_id', $user->id)
            ->whereDate('tanggal', $today)
            ->first();

        $radiusHijau = Setting::get('radius_hijau', 30);
        $radiusKuning = Setting::get('radius_kuning', 70);
        $targetLat = $user->latitude;
        $targetLng = $user->longitude;
        $hasGmap = !empty($user->gmap_magang) && $user->latitude && $user->longitude;
        
        // Get pending location change request (for presensi closable notification)
        $pendingLocationRequest = LocationChangeRequest::where('user_id', $user->id)->pending()->first();
        $hasPendingLocationRequest = $pendingLocationRequest ? true : false;

        return view('student.presensi.index', compact(
            'presensiHariIni', 'radiusHijau', 'radiusKuning', 'targetLat', 'targetLng', 'hasGmap', 'hasPendingLocationRequest', 'pendingLocationRequest'
        ));
    }

    public function riwayat()
    {
        $user = Auth::user();
        PresensiRetentionService::cleanupExpired();

        $presensis = Presensi::where('user_id', $user->id)
            ->whereDate('tanggal', '>=', PresensiRetentionService::earliestVisibleDate())
            ->orderBy('tanggal', 'desc')
            ->paginate(20);

        return view('student.presensi.riwayat', compact('presensis'));
    }

    public function create()
    {
        $user = Auth::user();
        $today = Carbon::today();
        
        $existingPresensi = Presensi::where('user_id', $user->id)
            ->whereDate('tanggal', $today)
            ->first();

        if ($existingPresensi) {
            return redirect()->route('student.presensi.index')
                ->with('info', 'Anda sudah melakukan presensi hari ini.');
        }

        // Get max distance from settings
        $distanceSetting = PresenceDistanceSetting::first();
        $maxDistance = $distanceSetting ? $distanceSetting->jarak_maksimal : 500;
        if ($distanceSetting && $distanceSetting->satuan === 'km') {
            $maxDistance = $maxDistance * 1000; // Convert km to meter
        }

        return view('student.presensi.create', compact('maxDistance'));
    }

    public function store(Request $request)
    {
        $rules = [
            'status' => 'required|in:hadir,izin,sakit',
            'keterangan' => 'nullable|string|max:255',
            'bukti_foto' => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
            'latitude' => 'nullable|numeric',
            'longitude' => 'nullable|numeric',
        ];

        if (in_array($request->status, ['izin', 'sakit'], true)) {
            $rules['keterangan'] = 'required|string|max:255';
            $rules['bukti_foto'] = 'required|image|mimes:jpg,jpeg,png|max:2048';
        }

        $request->validate($rules);

        $user = Auth::user();
        $today = Carbon::today();

        $existingPresensi = Presensi::where('user_id', $user->id)
            ->whereDate('tanggal', $today)
            ->first();

        if ($existingPresensi) {
            return redirect()->route('student.presensi.index')
                ->with('error', 'Anda sudah melakukan presensi hari ini.');
        }

        // Untuk status izin/sakit: lewati semua pengecekan lokasi/GPS
        if ($request->status === 'hadir') {
            // Validasi: wajib punya data lokasi magang terdaftar
            if (!$user->gmap_magang || !$user->latitude || !$user->longitude) {
                return redirect()->route('student.presensi.index')
                    ->with('error', 'Anda belum mengisi link Google Maps lokasi magang. Isi terlebih dahulu di halaman Presensi.');
            }

            if (!$request->latitude || !$request->longitude) {
                return redirect()->route('student.presensi.index')
                    ->with('error', 'Lokasi GPS tidak terdeteksi. Pastikan GPS aktif dan izinkan akses lokasi.');
            }

            $radiusHijau = (float) Setting::get('radius_hijau', 30);
            $radiusKuning = (float) Setting::get('radius_kuning', 70);

            $distance = LocationHelper::calculateDistance(
                $request->latitude,
                $request->longitude,
                $user->latitude,
                $user->longitude
            );

            $zone = LocationHelper::getAttendanceZone($distance, $radiusHijau, $radiusKuning);

            if ($zone === 'merah') {
                return redirect()->route('student.presensi.index')
                    ->with('error', 'Presensi ditolak. Anda berada di zona merah (' . LocationHelper::formatDistance($distance) . ' dari lokasi magang). Mendekat ke lokasi magang diperlukan.');
            }

            if ($zone === 'kuning' && !$request->boolean('zone_confirmed')) {
                return redirect()->route('student.presensi.index')
                    ->with('error', 'Anda berada di zona kuning. Konfirmasi terlebih dahulu bahwa Anda ingin melanjutkan presensi (lebih dekat ke lokasi magang lebih baik).');
            }
        }

        $data = [
            'user_id' => $user->id,
            'tanggal' => $today,
            'status' => $request->status,
            'keterangan' => $request->keterangan,
            'jam_masuk' => Carbon::now()->format('H:i:s'),
            'latitude_masuk' => $request->latitude,
            'longitude_masuk' => $request->longitude,
        ];

        if ($request->hasFile('bukti_foto')) {
            $data['bukti_foto'] = $request->file('bukti_foto')->store('presensi', 'public');
        }

        Presensi::create($data);

        return redirect()->route('student.presensi.index')
            ->with('success', 'Presensi berhasil dicatat!');
    }

    public function checkout(Request $request)
    {
        $request->validate([
            'latitude' => 'nullable|numeric',
            'longitude' => 'nullable|numeric',
        ]);

        $user = Auth::user();
        $today = Carbon::today();

        $presensi = Presensi::where('user_id', $user->id)
            ->whereDate('tanggal', $today)
            ->first();

        if (!$presensi) {
            return redirect()->route('student.presensi.index')
                ->with('error', 'Anda belum melakukan presensi masuk hari ini.');
        }

        if ($presensi->jam_keluar) {
            return redirect()->route('student.presensi.index')
                ->with('info', 'Anda sudah melakukan checkout hari ini.');
        }

        $presensi->update([
            'jam_keluar' => Carbon::now()->format('H:i:s'),
            'latitude_keluar' => $request->latitude,
            'longitude_keluar' => $request->longitude,
        ]);

        return redirect()->route('student.presensi.index')
            ->with('success', 'Checkout berhasil dicatat!');
    }

    /**
     * Update Google Maps link untuk lokasi magang
     * Extract koordinat dari link dan update profile
     */
    public function updateGmapLink(Request $request)
    {
        $request->validate([
            'gmap_link' => 'required|url|max:500',
            'is_change_request' => 'nullable|boolean',
        ]);

        $user = Auth::user();
        $gmapLink = $request->input('gmap_link');

        // Log the attempt for debugging
        \Log::info('Attempting to extract coordinates from Google Maps link', [
            'user_id' => $user->id,
            'link' => $gmapLink,
            'link_length' => strlen($gmapLink),
        ]);

        $coordinates = LocationHelper::extractCoordinatesFromGoogleMapsUrl($gmapLink);

        if (!$coordinates) {
            // Log failed extraction
            \Log::warning('Failed to extract coordinates from Google Maps link', [
                'user_id' => $user->id,
                'link' => $gmapLink,
                'link_contains_q_param' => strpos($gmapLink, '?q=') !== false,
                'link_contains_at_symbol' => strpos($gmapLink, '/@') !== false,
                'link_contains_ll_param' => strpos($gmapLink, '?ll=') !== false || strpos($gmapLink, '&ll=') !== false,
                'link_contains_3d_4d' => strpos($gmapLink, '!3d') !== false && strpos($gmapLink, '!4d') !== false,
                'link_contains_goo_gl' => strpos($gmapLink, 'goo.gl') !== false,
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Link Google Maps tidak valid. Pastikan:' . "\n" .
                    '1. Link dari google.com/maps (bukan sumber lain)' . "\n" .
                    '2. Link berisi lokasi spesifik, bukan halaman search' . "\n" .
                    '3. Gunakan fitur "Bagikan" → "Salin Tautan"' . "\n\n" .
                    'Format link yang diterima:' . "\n" .
                    '• https://www.google.com/maps/place/.../@-6.xxx,106.xxx' . "\n" .
                    '• https://maps.app.goo.gl/xxxxx' . "\n" .
                    'Baca guide: GUIDE_VALID_GMAP_LINK.md',
                'details' => [
                    'link_provided' => substr($gmapLink, 0, 50) . (strlen($gmapLink) > 50 ? '...' : ''),
                ],
            ], 422);
        }

        // Log successful extraction
        \Log::info('Successfully extracted coordinates from Google Maps link', [
            'user_id' => $user->id,
            'latitude' => $coordinates['latitude'],
            'longitude' => $coordinates['longitude'],
        ]);

        $hasExistingLocation = $user->gmap_magang && $user->latitude && $user->longitude;

        if ($hasExistingLocation || $request->boolean('is_change_request')) {
            $pending = LocationChangeRequest::where('user_id', $user->id)->pending()->exists();
            if ($pending) {
                return response()->json([
                    'success' => false,
                    'message' => 'Anda masih memiliki permintaan ubah lokasi yang menunggu persetujuan admin.',
                ], 422);
            }

            $locationRequest = LocationChangeRequest::create([
                'user_id' => $user->id,
                'old_gmap_magang' => $user->gmap_magang,
                'old_latitude' => $user->latitude,
                'old_longitude' => $user->longitude,
                'new_gmap_magang' => $gmapLink,
                'new_latitude' => $coordinates['latitude'],
                'new_longitude' => $coordinates['longitude'],
                'status' => 'pending',
            ]);

            \App\Services\NotificationService::notifyLocationChangeRequest($locationRequest);

            NotificationService::create(
                $user->id,
                'Permintaan Terkirim',
                'Permintaan ubah titik koordinat lokasi magang telah dikirim ke admin. Tunggu persetujuan.',
                'info',
                'fa-map-pin',
                null,
                'location_change_request',
                $locationRequest->id
            );

            return response()->json([
                'success' => true,
                'message' => 'Permintaan ubah lokasi dikirim ke admin. Anda akan mendapat notifikasi setelah diproses.',
                'pending_approval' => true,
                'reload' => true,
            ]);
        }

        $user->update([
            'gmap_magang' => $gmapLink,
            'latitude' => $coordinates['latitude'],
            'longitude' => $coordinates['longitude'],
        ]);

        $user->syncStudentStatus();

        return response()->json([
            'success' => true,
            'message' => 'Lokasi magang berhasil disimpan! Tombol presensi sekarang dapat digunakan.',
            'coordinates' => $coordinates,
            'reload' => true,
        ]);
    }
}
