<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Helpers\LocationHelper;
use Illuminate\Http\Request;

/**
 * Controller untuk debug dan test Google Maps URL parsing
 * Hanya accessible untuk admin
 */
class LocationDebugController extends Controller
{
    /**
     * Test page untuk debug Google Maps URL
     */
    public function testGmapUrl(Request $request)
    {
        $testUrl = $request->input('url', '');
        $result = null;
        $isValid = false;

        if ($testUrl) {
            $result = LocationHelper::extractCoordinatesFromGoogleMapsUrl($testUrl);
            $isValid = $result !== null;
        }

        $testCases = [
            [
                'name' => 'Query Parameter Format',
                'url' => 'https://www.google.com/maps?q=-6.175392,106.827153',
            ],
            [
                'name' => 'Place with @ (Most Common)',
                'url' => 'https://www.google.com/maps/place/Jakarta/@-6.175392,106.827153,17z',
            ],
            [
                'name' => 'Short URL (maps.app.goo.gl)',
                'url' => 'https://maps.app.goo.gl/xxxxxxxxx',
            ],
            [
                'name' => 'LL Parameter',
                'url' => 'https://maps.google.com/?ll=-6.175392,106.827153',
            ],
            [
                'name' => 'Data Parameter (!3d !4d)',
                'url' => 'https://www.google.com/maps/place/data=!4m6!3m5!1s0x...!8m2!3d-6.175392!4d106.827153',
            ],
        ];

        return view('admin.location-debug.test-gmap-url', compact('testUrl', 'result', 'isValid', 'testCases'));
    }

    /**
     * API endpoint untuk test URL extraction
     */
    public function apiTestGmapUrl(Request $request)
    {
        $request->validate([
            'url' => 'required|url',
        ]);

        $url = $request->input('url');
        $result = LocationHelper::extractCoordinatesFromGoogleMapsUrl($url);

        return response()->json([
            'url' => $url,
            'success' => $result !== null,
            'coordinates' => $result,
            'message' => $result 
                ? 'Koordinat berhasil di-extract: Lat ' . $result['latitude'] . ', Lon ' . $result['longitude']
                : 'Gagal extract koordinat dari URL',
        ]);
    }
}
