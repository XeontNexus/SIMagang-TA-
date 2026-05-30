<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Mitra;
use App\Helpers\LocationHelper;
use Illuminate\Http\Request;

class LocationComparisonController extends Controller
{
    /**
     * Tampilkan daftar perbandingan lokasi siswa dan mitra
     */
    public function index(Request $request)
    {
        $query = User::where('role', 'siswa')
            ->whereNotNull('latitude')
            ->whereNotNull('longitude')
            ->with('mitra');

        // Filter by mitra if provided
        if ($request->filled('mitra_id')) {
            $query->where('mitra_id', $request->mitra_id);
        }

        // Filter by status
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        // Search by nama atau nisn
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('nama_lengkap', 'like', "%{$search}%")
                  ->orWhere('nisn', 'like', "%{$search}%");
            });
        }

        $students = $query->paginate(20);

        // Hitung perbandingan untuk setiap siswa
        foreach ($students as $student) {
            $student->location_comparison = $this->compareLocations($student);
        }

        $mitras = Mitra::orderBy('nama_mitra')->get();
        $statuses = ['active', 'inactive', 'pending', 'completed'];

        return view('admin.location-comparison.index', compact('students', 'mitras', 'statuses'));
    }

    /**
     * Tampilkan detail perbandingan lokasi untuk satu siswa
     */
    public function show(User $user)
    {
        if ($user->role !== 'siswa') {
            abort(404);
        }

        $comparison = $this->compareLocations($user);
        $mitras = Mitra::whereHas('siswa', function ($q) use ($user) {
            $q->where('users.id', $user->id);
        })->get();

        return view('admin.location-comparison.show', compact('user', 'comparison', 'mitras'));
    }

    /**
     * Update Google Maps link untuk mitra
     */
    public function updateMitraLocation(Request $request, Mitra $mitra)
    {
        $request->validate([
            'gmap_link' => 'required|url',
        ]);

        $coordinates = LocationHelper::extractCoordinatesFromGoogleMapsUrl($request->gmap_link);

        if (!$coordinates) {
            return response()->json([
                'success' => false,
                'message' => 'Link Google Maps tidak valid. Pastikan link berisi koordinat yang jelas.',
            ], 422);
        }

        $mitra->update([
            'gmap_link' => $request->gmap_link,
            'latitude' => $coordinates['latitude'],
            'longitude' => $coordinates['longitude'],
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Lokasi mitra berhasil diperbarui.',
        ]);
    }

    /**
     * Bandingkan lokasi siswa dengan lokasi mitra
     */
    private function compareLocations(User $user)
    {
        $comparison = [
            'student_lat' => $user->latitude,
            'student_lon' => $user->longitude,
            'student_gmap' => $user->gmap_magang,
            'mitra_id' => $user->mitra_id,
            'mitra_name' => null,
            'mitra_lat' => null,
            'mitra_lon' => null,
            'mitra_gmap' => null,
            'distance' => null,
            'similarity_percentage' => 0,
            'status_zone' => 'unknown',
            'has_complete_info' => false,
        ];

        if ($user->mitra) {
            $mitra = $user->mitra;
            $comparison['mitra_name'] = $mitra->nama_mitra;
            $comparison['mitra_lat'] = $mitra->latitude;
            $comparison['mitra_lon'] = $mitra->longitude;
            $comparison['mitra_gmap'] = $mitra->gmap_link;

            // Hitung jarak jika kedua lokasi ada
            if ($mitra->latitude && $mitra->longitude) {
                $distance = LocationHelper::calculateDistance(
                    $user->latitude,
                    $user->longitude,
                    $mitra->latitude,
                    $mitra->longitude
                );

                $comparison['distance'] = $distance;
                $comparison['has_complete_info'] = true;
                
                // Tentukan similarity percentage berdasarkan jarak
                // Asumsi: 0m = 100%, 500m = 80%, 1000m = 60%, 2000m+ = 0%
                $similarity = max(0, 100 - ($distance / 20));
                $comparison['similarity_percentage'] = round($similarity, 2);

                // Tentukan zone berdasarkan default settings
                $comparison['status_zone'] = $this->getZoneStatus($distance);
            }
        }

        return $comparison;
    }

    /**
     * Tentukan zona berdasarkan jarak
     */
    private function getZoneStatus($distance)
    {
        $radiusHijau = 30; // meter
        $radiusKuning = 70; // meter

        return LocationHelper::getAttendanceZone($distance, $radiusHijau, $radiusKuning);
    }

    /**
     * Export data perbandingan lokasi ke CSV
     */
    public function export(Request $request)
    {
        $query = User::where('role', 'siswa')
            ->whereNotNull('latitude')
            ->whereNotNull('longitude')
            ->with('mitra');

        if ($request->filled('mitra_id')) {
            $query->where('mitra_id', $request->mitra_id);
        }

        $students = $query->get();

        $filename = 'location_comparison_' . date('Y-m-d_H-i-s') . '.csv';

        $headers = array(
            'Content-Type' => 'text/csv; charset=utf-8',
            'Content-Disposition' => "attachment; filename=\"{$filename}\"",
        );

        $callback = function () use ($students) {
            $file = fopen('php://output', 'w');

            // Header CSV
            fputcsv($file, [
                'NISN',
                'Nama Siswa',
                'Mitra Magang',
                'Lat Siswa',
                'Lon Siswa',
                'Lat Mitra',
                'Lon Mitra',
                'Jarak (meter)',
                'Kesamaan Lokasi (%)',
                'Status Zone',
                'Link Gmap Siswa',
                'Link Gmap Mitra',
            ]);

            // Data
            foreach ($students as $student) {
                $comparison = $this->compareLocations($student);

                fputcsv($file, [
                    $student->nisn,
                    $student->nama_lengkap,
                    $comparison['mitra_name'] ?? '-',
                    $comparison['student_lat'],
                    $comparison['student_lon'],
                    $comparison['mitra_lat'] ?? '-',
                    $comparison['mitra_lon'] ?? '-',
                    $comparison['distance'] ? round($comparison['distance'], 2) : '-',
                    $comparison['similarity_percentage'] . '%',
                    $comparison['status_zone'],
                    $student->gmap_magang ?? '-',
                    $comparison['mitra_gmap'] ?? '-',
                ]);
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }
}
