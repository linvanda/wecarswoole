<?php

namespace WecarSwoole\Util;

class Random
{
    public static function str(int $len): string
    {
        $str = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789';
        $strLen = strlen($str);

        if ($len > $strLen) {
            $str = str_repeat($str, ceil($len / $strLen));
        }

        return substr(str_shuffle($str), 0, $len);
    }
}
