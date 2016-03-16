<?php

/**
* String tools
*/
class Util_StrTool {
    public static function is_blank($str)
    {
        return empty(trim($str));
    }

    public static function null_if_blank($str)
    {
        return Util_StrTool::is_blank($str) ? null : $str;
    }
}
