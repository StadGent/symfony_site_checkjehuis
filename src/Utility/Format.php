<?php

namespace App\Utility;

class Format 
{
    public static function price($number)
    {
        return number_format($number, 2, ',', ' ');
    }

    public static function energy($number)
    {
        return round($number);
    }

    public static function CO2($number)
    {
        return round($number);
    }
} 