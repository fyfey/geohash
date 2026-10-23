<?php

declare(strict_types=1);

namespace Fyfey\GeoHash;

class GeoHash
{
    private const MAX_LAT = 90;
    private const MIN_LAT = -90;
    private const MAX_LON = 180;
    private const MIN_LON = -180;

    private const TABLE = [
        '0', '1', '2', '3', '4', '5', '6', '7', '8', '9', 'b', 'c', 'd', 'e', 'f', 'g',
        'h', 'j', 'k', 'm', 'n', 'p', 'q', 'r', 's', 't', 'u', 'v', 'w', 'x', 'y', 'z'
    ];

    public function decode(string $hash, ?int $precision = null): array
    {
        $bits = [];
        foreach (str_split($hash) as $char) {
            $dec = array_search($char, self::TABLE);
            array_push($bits, ...str_split(str_pad(decbin($dec), 5, '0', STR_PAD_LEFT)));
        }

        $lat = array_values(array_filter($bits, fn($i) => $i % 2 !== 0, ARRAY_FILTER_USE_KEY));
        $lon = array_values(array_filter($bits, fn($i) => $i % 2 === 0, ARRAY_FILTER_USE_KEY));

        $latMin = self::MIN_LAT;
        $latMax = self::MAX_LAT;
        foreach ($lat as $bit) {
            $this->bisect($bit, $latMin, $latMax);
        }

        $lonMin = self::MIN_LON;
        $lonMax = self::MAX_LON;
        foreach ($lon as $bit) {
            $this->bisect($bit, $lonMin, $lonMax);
        }

        $latVal = ($latMin + $latMax) / 2;
        $lonVal = ($lonMin + $lonMax) / 2;

        if ($precision !== null) {
            $latVal = round($latVal, $precision);
            $lonVal = round($lonVal, $precision);
        }

        return [$latVal, $lonVal];
    }

    public function encode(float $lat, float $lon, int $precision = 5): string
    {
        $hash = [];
        $bits = 0;
        $bitsTotal = 0;
        $maxLon = self::MAX_LON;
        $minLon = self::MIN_LON;
        $maxLat = self::MAX_LAT;
        $minLat = self::MIN_LAT;
        $hashVal = 0;

        while (count($hash) < $precision) {
            if ($bitsTotal % 2 === 0) {
                $mid = ($maxLon + $minLon) / 2;
                if ($lon > $mid) {
                    $hashVal = ($hashVal << 1) + 1;
                    $minLon = $mid;
                } else {
                    $hashVal = ($hashVal << 1) + 0;
                    $maxLon = $mid;
                }
            } else {
                $mid = ($maxLat + $minLat) / 2;
                if ($lat > $mid) {
                    $hashVal = ($hashVal << 1) + 1;
                    $minLat = $mid;
                } else {
                    $hashVal = ($hashVal << 1) + 0;
                    $maxLat = $mid;
                }
            }

            $bits++;
            $bitsTotal++;
            if ($bits === 5) {
                $hash[] = self::TABLE[$hashVal];
                $bits = 0;
                $hashVal = 0;
            }
        }

        return implode('', $hash);
    }

    private function bisect(string $bit, float &$min, float &$max): void
    {
        $mid = ($min + $max) / 2;
        if ($bit === '1') {
            $min = $mid;
        } else {
            $max = $mid;
        }
    }
}
