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
}
