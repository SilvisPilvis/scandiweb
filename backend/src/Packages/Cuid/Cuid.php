<?php

namespace App\Packages\Cuid;

class Cuid
{
    private static $alphabet = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';

    public static function generate($length = 8)
    {
        return substr(str_shuffle(self::$alphabet), 0, $length);
    }
}
