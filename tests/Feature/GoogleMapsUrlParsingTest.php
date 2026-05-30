<?php

// Test script untuk debug Google Maps URL parsing
namespace Tests\Feature;

use App\Helpers\LocationHelper;

class GoogleMapsUrlParsingTest
{
    public function testVariousGoogleMapsFormats()
    {
        $testCases = [
            // Format 1: Query parameter (q=lat,lon)
            [
                'url' => 'https://www.google.com/maps?q=-6.175392,106.827153',
                'name' => 'Query Parameter Format',
                'expected_lat' => -6.175392,
                'expected_lon' => 106.827153,
            ],
            // Format 2: Place with @ symbol
            [
                'url' => 'https://www.google.com/maps/place/Jakarta/@-6.175392,106.827153,17z/data=!4m6!3m5!1s0x2e69f3e945e34d01:0x5371bf0fdad9a3a!8m2!3d-6.175392!4d106.827153',
                'name' => 'Place with @ and full params',
                'expected_lat' => -6.175392,
                'expected_lon' => 106.827153,
            ],
            // Format 3: LL parameter
            [
                'url' => 'https://maps.google.com/?ll=-6.175392,106.827153',
                'name' => 'LL Parameter',
                'expected_lat' => -6.175392,
                'expected_lon' => 106.827153,
            ],
            // Format 4: Modern Google Maps with place name
            [
                'url' => 'https://www.google.com/maps/place/PT.+MAJU+JAYA/@-6.175392,106.827153,15z',
                'name' => 'Modern place format',
                'expected_lat' => -6.175392,
                'expected_lon' => 106.827153,
            ],
            // Format 5: With zoom level
            [
                'url' => 'https://www.google.com/maps/@-6.175392,106.827153,15z',
                'name' => 'Modern @ format with zoom',
                'expected_lat' => -6.175392,
                'expected_lon' => 106.827153,
            ],
            // Format 6: Data parameter format (embedded)
            [
                'url' => 'https://www.google.com/maps/place/data=!4m6!3m5!1s0x...!8m2!3d-6.175392!4d106.827153',
                'name' => 'Data parameter with !3d !4d',
                'expected_lat' => -6.175392,
                'expected_lon' => 106.827153,
            ],
            // Format 7: Indonesian location format
            [
                'url' => 'https://www.google.com/maps/place/Jl.+Merdeka+No.+123,+Jakarta/@-6.175392,106.827153',
                'name' => 'Indonesian street address',
                'expected_lat' => -6.175392,
                'expected_lon' => 106.827153,
            ],
            // Format 8: Q parameter with place name
            [
                'url' => 'https://www.google.com/maps?q=Perusahaan+ABC+-6.175392,106.827153',
                'name' => 'Q parameter with place and coords',
                'expected_lat' => -6.175392,
                'expected_lon' => 106.827153,
            ],
        ];

        echo "\n=== TESTING GOOGLE MAPS URL PARSING ===\n\n";

        $passed = 0;
        $failed = 0;

        foreach ($testCases as $test) {
            echo "Test: " . $test['name'] . "\n";
            echo "URL: " . $test['url'] . "\n";

            $result = LocationHelper::extractCoordinatesFromGoogleMapsUrl($test['url']);

            if ($result && 
                abs($result['latitude'] - $test['expected_lat']) < 0.0001 && 
                abs($result['longitude'] - $test['expected_lon']) < 0.0001) {
                echo "✅ PASSED\n";
                echo "   Lat: " . $result['latitude'] . ", Lon: " . $result['longitude'] . "\n";
                $passed++;
            } else {
                echo "❌ FAILED\n";
                if ($result) {
                    echo "   Got: Lat " . $result['latitude'] . ", Lon " . $result['longitude'] . "\n";
                } else {
                    echo "   Got: NULL\n";
                }
                echo "   Expected: Lat " . $test['expected_lat'] . ", Lon " . $test['expected_lon'] . "\n";
                $failed++;
            }
            echo "\n";
        }

        echo "\n=== SUMMARY ===\n";
        echo "Passed: $passed\n";
        echo "Failed: $failed\n";
        echo "Total: " . ($passed + $failed) . "\n";

        return $failed === 0;
    }
}

// Run test if called from command line
if (php_sapi_name() === 'cli') {
    $test = new GoogleMapsUrlParsingTest();
    $result = $test->testVariousGoogleMapsFormats();
    exit($result ? 0 : 1);
}
