<?php
namespace App\Support;

class MapTolerance
{
    public static function forZoom(int $z): float
    {
        return match (true) {
            $z <= 8  => 0.20,    //   keep only a dot (centroid)
            $z <= 10 => 0.05,    // ~ 5 km resolution
            $z <= 12 => 0.005,   // ~ 500 m
            default  => 0.0005,  // fine detail ≥ zoom 13
        };
    }
}
