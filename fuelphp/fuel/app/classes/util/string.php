<?php

/**
* String tools
*/
class Util_String {
    public static function is_blank($str)
    {
        return empty(trim($str));
    }

    public static function null_if_blank($str)
    {
        return self::is_blank($str) ? null : $str;
    }

    public static function chop_datetime($datetime)
    {
        $re_datetime = '/(\d{4}-\d{2}-\d{2}) (\d{2}:\d{2}:\d{2})/';
        preg_match($re_datetime, $datetime, $matches);
        list(, $date, $time) = array_pad($matches, 3, null);
        return [$date, $time];
    }
}
