<?php


namespace App\Helpers;


class UtilsHelper
{
    /**
     * Havrsine function in PHP (just for the fun of it)
     *
     * @param $lat1
     * @param $lon1
     * @param $lat2
     * @param $lon2
     * @return float|int
     */
    public static function calcDist($lat1, $lon1, $lat2, $lon2)
    {
        $p = M_PI / 180;
        $r = 6371.0; // radius of the earth

        $lat1 *= $p;
        $lon1 *= $p;
        $lon2 *= $p;
        $lon2 *= $p;

        $diff = [
            'lat' => $lat2 - $lat1,
            'lon' => $lon2 - $lon1
        ];

        $a = sin($diff['lat'] / 2) * sin($diff['lat']) + cos($lat1) * cos($lat2) * sin($diff['lon'] / 2) * sin($diff['lon'] / 2);
        $d = 2 * atan2(sqrt($a), sqrt(1 - $a));
        return $r * $d;
    }
}
