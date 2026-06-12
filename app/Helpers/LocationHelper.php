<?php

namespace App\Helpers;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

/**
 * Helper untuk menghitung jarak berdasarkan koordinat GPS menggunakan Haversine Formula
 */
class LocationHelper
{
    /**
     * Hitung jarak antara dua koordinat dalam meter
     * 
     * @param float $lat1 Latitude lokasi pertama
     * @param float $lon1 Longitude lokasi pertama
     * @param float $lat2 Latitude lokasi kedua
     * @param float $lon2 Longitude lokasi kedua
     * @return float Jarak dalam meter
     */
    public static function calculateDistance($lat1, $lon1, $lat2, $lon2)
    {
        $earth_radius = 6371000; // Radius bumi dalam meter

        $dLat = deg2rad($lat2 - $lat1);
        $dLon = deg2rad($lon2 - $lon1);

        $a = sin($dLat / 2) * sin($dLat / 2) +
             cos(deg2rad($lat1)) * cos(deg2rad($lat2)) *
             sin($dLon / 2) * sin($dLon / 2);

        $c = 2 * asin(sqrt($a));
        $distance = $earth_radius * $c;

        return round($distance, 2); // Return dalam meter dengan 2 desimal
    }

    /**
     * Cek apakah lokasi siswa dalam jarak yang diizinkan
     * 
     * @param float $studentLat Latitude siswa
     * @param float $studentLon Longitude siswa
     * @param float $companylat Latitude perusahaan magang
     * @param float $companyLon Longitude perusahaan magang
     * @param float $maxDistance Jarak maksimal yang diizinkan (dalam meter)
     * @return bool True jika dalam jarak, false jika di luar jarak
     */
    public static function isWithinDistance($studentLat, $studentLon, $companylat, $companyLon, $maxDistance)
    {
        $distance = self::calculateDistance($studentLat, $studentLon, $companylat, $companyLon);
        return $distance <= $maxDistance;
    }

    /**
     * Format jarak untuk ditampilkan
     * 
     * @param float $distanceInMeter Jarak dalam meter
     * @return string Jarak terformat
     */
    public static function formatDistance($distanceInMeter)
    {
        if ($distanceInMeter < 1000) {
            return round($distanceInMeter, 2) . ' m';
        } else {
            return round($distanceInMeter / 1000, 2) . ' km';
        }
    }

    /**
     * Extract koordinat dari Google Maps URL
     * Support format:
     * - https://www.google.com/maps?q=-6.175392,106.827153
     * - https://www.google.com/maps/place/.../@-6.175392,106.827153,...
     * - https://maps.google.com/?q=-6.175392,106.827153
     * - https://www.google.com/maps/place/Nama+Tempat/@-6.175392,106.827153,...
     * - https://maps.app.goo.gl/xxxxx (short URLs - akan di-expand)
     * - https://goo.gl/xxxxx (short URLs - akan di-expand)
     * - Format dengan data parameter: !3d lat !4d lon
     * - Format dengan 3m5: coordinate di dalam parameter
     * 
     * @param string $gmapUrl URL Google Maps
     * @return array|null Array dengan 'latitude' dan 'longitude' atau null jika tidak valid
     */
    public static function extractCoordinatesFromGoogleMapsUrl($gmapUrl)
    {
        if (!$gmapUrl) {
            return null;
        }

        // Trim whitespace dan remove extra spaces
        $gmapUrl = trim($gmapUrl);
        
        // Decode URL encoded characters (e.g., %2C becomes ,)
        $gmapUrl = urldecode($gmapUrl);

        // Expand short URLs (goo.gl, maps.app.goo.gl, share.google)
        if (preg_match('/(goo\.gl|maps\.app\.goo\.gl|share\.google)/i', $gmapUrl)) {
            $expandedUrl = self::expandShortUrl($gmapUrl);
            if ($expandedUrl) {
                $gmapUrl = urldecode($expandedUrl);
            }
        }

        // Normalisasi domain Google Maps
        $gmapUrl = str_replace(
            ['google.co.id/maps', 'google.com/maps', 'm.google.com/maps'],
            'google.com/maps',
            $gmapUrl
        );

        $patterns = [
            // /search/lat,lon
            '/\/search\/(-?\d+\.?\d*)\s*,\s*(-?\d+\.?\d*)/',
            // api=1&query=lat,lon
            '/[?&]query=(-?\d+\.?\d*)\s*,\s*(-?\d+\.?\d*)/',
            // center= / destination= / ll=
            '/[?&](?:center|destination|ll)=(-?\d+\.?\d*)\s*,\s*(-?\d+\.?\d*)/',
            // ?q=lat,lon
            '/[?&]q=(-?\d+\.?\d*)\s*,\s*(-?\d+\.?\d*)/',
            // /@lat,lon
            '/@(-?\d+\.?\d*)\s*,\s*(-?\d+\.?\d*)/',
            // !3dLAT!4dLON (bisa ada karakter di antaranya)
            '/!3d(-?\d+\.?\d*).*?!4d(-?\d+\.?\d*)/',
            // !4d sebelum !3d (jarang)
            '/!4d(-?\d+\.?\d*).*?!3d(-?\d+\.?\d*)/',
        ];

        foreach ($patterns as $index => $pattern) {
            if (preg_match($pattern, $gmapUrl, $matches)) {
                $lat = (float) $matches[1];
                $lon = (float) $matches[2];
                // Pattern terakhir swap lat/lon jika format !4d...!3d
                if ($index === count($patterns) - 1) {
                    [$lat, $lon] = [$lon, $lat];
                }
                if (self::isValidCoordinate($lat, $lon)) {
                    return ['latitude' => $lat, 'longitude' => $lon];
                }
            }
        }

        // Scan semua pasangan koordinat di URL (fallback kuat)
        $scanned = self::findCoordinatePairsInUrl($gmapUrl);
        if ($scanned) {
            return $scanned;
        }

        // Geocoding dari nama tempat di path URL
        $placeName = self::extractPlaceNameFromUrl($gmapUrl);
        if ($placeName) {
            $geocoded = self::geocodePlaceName($placeName);
            if ($geocoded) {
                return $geocoded;
            }
        }

        return null;
    }

    private static function findCoordinatePairsInUrl(string $url): ?array
    {
        if (!preg_match_all('/(-?\d{1,3}\.\d{3,})\s*,\s*(-?\d{1,3}\.\d{3,})/', $url, $matches, PREG_SET_ORDER)) {
            return null;
        }

        $fallback = null;
        foreach ($matches as $match) {
            $lat = (float) $match[1];
            $lon = (float) $match[2];
            if (!self::isValidCoordinate($lat, $lon)) {
                continue;
            }
            if (self::isLikelyIndonesia($lat, $lon)) {
                return ['latitude' => $lat, 'longitude' => $lon];
            }
            $fallback ??= ['latitude' => $lat, 'longitude' => $lon];
        }

        return $fallback;
    }

    private static function isLikelyIndonesia(float $lat, float $lon): bool
    {
        return $lat >= -11 && $lat <= 6 && $lon >= 95 && $lon <= 141;
    }

    private static function extractPlaceNameFromUrl(string $url): ?string
    {
        if (preg_match('/\/place\/([^/@?]+)/', $url, $matches)) {
            return trim(str_replace('+', ' ', urldecode($matches[1])));
        }
        if (preg_match('/\/search\/([^/@?]+)/', $url, $matches)) {
            $name = urldecode($matches[1]);
            if (!preg_match('/^-?\d/', $name)) {
                return trim(str_replace('+', ' ', $name));
            }
        }
        return null;
    }

    private static function geocodePlaceName(string $placeName): ?array
    {
        try {
            $response = Http::withHeaders([
                'User-Agent' => 'SIMagang-Laravel/1.0 (contact@simagang.local)',
            ])->timeout(10)->get('https://nominatim.openstreetmap.org/search', [
                'q' => $placeName,
                'format' => 'json',
                'limit' => 1,
                'countrycodes' => 'id',
            ]);

            if ($response->successful() && !empty($response->json())) {
                $result = $response->json()[0];
                return [
                    'latitude' => (float) $result['lat'],
                    'longitude' => (float) $result['lon'],
                ];
            }
        } catch (\Exception $e) {
            Log::debug('Geocoding gagal: ' . $e->getMessage());
        }

        return null;
    }

    /**
     * Validasi apakah koordinat valid
     * Latitude: -90 to 90
     * Longitude: -180 to 180
     * 
     * @param float $lat Latitude
     * @param float $lon Longitude
     * @return bool True jika valid
     */
    private static function isValidCoordinate($lat, $lon)
    {
        return $lat >= -90 && $lat <= 90 && $lon >= -180 && $lon <= 180;
    }

    /**
     * Expand short URLs menggunakan cURL
     * 
     * @param string $shortUrl Short URL dari Google Maps
     * @return string|null Expanded URL atau null jika gagal
     */
    private static function expandShortUrl($shortUrl)
    {
        try {
            $ch = curl_init($shortUrl);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_HEADER, true);
            curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
            curl_setopt($ch, CURLOPT_NOBODY, true);
            curl_setopt($ch, CURLOPT_TIMEOUT, 5);
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
            curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36');
            
            curl_exec($ch);
            $expandedUrl = curl_getinfo($ch, CURLINFO_EFFECTIVE_URL);
            curl_close($ch);
            
            return $expandedUrl ?: null;
        } catch (\Exception $e) {
            return null;
        }
    }

    /**
     * Tentukan zona presensi berdasarkan jarak (meter).
     */
    public static function getAttendanceZone(float $distanceMeters, float $radiusHijau, float $radiusKuning): string
    {
        if ($distanceMeters <= $radiusHijau) {
            return 'hijau';
        }
        if ($distanceMeters <= $radiusKuning) {
            return 'kuning';
        }

        return 'merah';
    }
}
