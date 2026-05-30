<?php

namespace App\Helpers;

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

        // Expand short URLs (e.g. goo.gl, maps.app.goo.gl) using cURL
        if (strpos($gmapUrl, 'goo.gl') !== false || strpos($gmapUrl, 'maps.app.goo.gl') !== false) {
            $expandedUrl = self::expandShortUrl($gmapUrl);
            if ($expandedUrl) {
                $gmapUrl = $expandedUrl;
                $gmapUrl = urldecode($gmapUrl);
            }
        }

        // Pattern 1: q=lat,lon (query parameter with coordinates)
        // Supports: ?q=-6.175392,106.827153 or &q=-6.175392,106.827153
        if (preg_match('/[?&]q=(-?\d+\.?\d*),(-?\d+\.?\d*)/', $gmapUrl, $matches)) {
            if (isset($matches[1]) && isset($matches[2])) {
                $lat = (float) $matches[1];
                $lon = (float) $matches[2];
                if (self::isValidCoordinate($lat, $lon)) {
                    return [
                        'latitude' => $lat,
                        'longitude' => $lon
                    ];
                }
            }
        }

        // Pattern 2: /@lat,lon (at symbol with coordinates)
        // Supports: /@-6.175392,106.827153 or /@-6.175392,106.827153,15z
        if (preg_match('/@(-?\d+\.?\d+),(-?\d+\.?\d+)/', $gmapUrl, $matches)) {
            if (isset($matches[1]) && isset($matches[2])) {
                $lat = (float) $matches[1];
                $lon = (float) $matches[2];
                if (self::isValidCoordinate($lat, $lon)) {
                    return [
                        'latitude' => $lat,
                        'longitude' => $lon
                    ];
                }
            }
        }
        
        // Pattern 3: ll=lat,lon (ll parameter with coordinates)
        if (preg_match('/[?&]ll=(-?\d+\.?\d*),(-?\d+\.?\d*)/', $gmapUrl, $matches)) {
            if (isset($matches[1]) && isset($matches[2])) {
                $lat = (float) $matches[1];
                $lon = (float) $matches[2];
                if (self::isValidCoordinate($lat, $lon)) {
                    return [
                        'latitude' => $lat,
                        'longitude' => $lon
                    ];
                }
            }
        }

        // Pattern 4: !3d lat !4d lon (data parameter format)
        if (preg_match('/!3d(-?\d+\.?\d*)!4d(-?\d+\.?\d*)/', $gmapUrl, $matches)) {
            if (isset($matches[1]) && isset($matches[2])) {
                $lat = (float) $matches[1];
                $lon = (float) $matches[2];
                if (self::isValidCoordinate($lat, $lon)) {
                    return [
                        'latitude' => $lat,
                        'longitude' => $lon
                    ];
                }
            }
        }

        // Pattern 5: 3d=lat and 4d=lon (alternative data format)
        if (preg_match('/[?&]3d=(-?\d+\.?\d*)/', $gmapUrl, $matches1) && 
            preg_match('/[?&]4d=(-?\d+\.?\d*)/', $gmapUrl, $matches2)) {
            if (isset($matches1[1]) && isset($matches2[1])) {
                $lat = (float) $matches1[1];
                $lon = (float) $matches2[1];
                if (self::isValidCoordinate($lat, $lon)) {
                    return [
                        'latitude' => $lat,
                        'longitude' => $lon
                    ];
                }
            }
        }

        // Pattern 6: Inside embed parameter: 1s or similar coordinate patterns
        // Try to find any coordinate-like pattern in the URL
        if (preg_match('/[?&]([^=&]*?)=(-?\d+\.?\d+)(?:[,\s\/]|\s)(-?\d+\.?\d+)/', $gmapUrl, $matches)) {
            // This is a fallback for other coordinate-like patterns
            // Make sure it looks like coordinates (latitude range: -90 to 90, longitude: -180 to 180)
            $lat = (float) $matches[2];
            $lon = (float) $matches[3];
            if (self::isValidCoordinate($lat, $lon)) {
                return [
                    'latitude' => $lat,
                    'longitude' => $lon
                ];
            }
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
