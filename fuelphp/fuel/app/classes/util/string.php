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

    public static function random_alphanum($length = 8) {
        static $chars;
        if (!$chars) {
            $chars = array_merge(range('a', 'z'), range('A', 'Z'), range('0', '9'));
        }
        $str = '';
        for ($i = 0; $i < $length; ++$i) {
            $str .= $chars[mt_rand(0, count($chars) - 1)];
        }
        return $str;
    }

    public static function is_json($string)
    {
        json_decode($string);
        return json_last_error() == JSON_ERROR_NONE;
    }
}
