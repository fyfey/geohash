<?php

namespace Fyfey\GeoHash;

class GeoHash
{
    const MAX_LAT = 90;
    const MIN_LAT = -90;
    const MAX_LON = 180;
    const MIN_LON = -180;

    protected $table;

    public function __construct()
    {
        $this->table = array(
            '0', '1', '2', '3', '4', '5', '6', '7', '8', '9', 'b', 'c', 'd', 'e', 'f', 'g',
            'h', 'j', 'k', 'm', 'n', 'p', 'q', 'r', 's', 't', 'u', 'v', 'w', 'x', 'y', 'z'
        );
    }

    public function decode($hash, $precision = null)
    {
        $bits = [];
        for ($i = 0; $i < strlen($hash); $i++) {
            $dec = array_search($hash[$i], $this->table);
            $code = sprintf('%05d', base_convert($dec, 10, 2));
            foreach (str_split($code, 1) as $bit) {
                $bits[] = $bit;
            }
        }

        $lat = [];
        $lon = [];
        for ($i = 0; $i < count($bits); $i++) {
            if ($i % 2 == 0) {
                $lon[] = $bits[$i];
            } else {
                $lat[] = $bits[$i];
            }
        }

        $vars = [
            'val' => 0,
            'min' => self::MIN_LAT,
            'max' => self::MAX_LAT
        ];
        for ($i = 0; $i < count($lat); $i++) {
            $vars = $this->calc($lat[$i], $vars['min'], $vars['val'], $vars['max']);
        }
        $lat = $vars['val'];

        $vars = [
            'val' => 0,
            'min' => self::MIN_LON,
            'max' => self::MAX_LON
        ];
        for ($i = 0; $i < count($lon); $i++) {
            $vars = $this->calc($lon[$i], $vars['min'], $vars['val'], $vars['max']);
        }
        $lon = $vars['val'];

        if ($precision) {
            $lon = round($lon, $precision);
            $lat = round($lat, $precision);
        }

        return array($lat, $lon);
    }

    public function encode($lat, $lon, $precision = 5)
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
                $hash[] = $this->table[$hashVal];
                $bits = 0;
                $hashVal = 0;
            }
        }

        return join($hash, '');
    }

    protected function calc($bit, $min, $mid, $max)
    {
        if ($bit == 1) {
            $min = $mid;
            $max = $max;
            $val = ($mid + $max) / 2;
        } else {
            $min = $min;
            $max = $mid;
            $val = ($mid + $min) / 2;
        }

        return [
            'min' => $min,
            'max' => $max,
            'val' => $val
        ];
    }
}
