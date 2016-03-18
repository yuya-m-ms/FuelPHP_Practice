<?php

/**
* Model dealing with business logics
*/
class Model_Todo_Logic
{
    static $status_cache;
    static $status_map;
    static $status_bimap;

    private function __construct() {
        // static member only
    }

    public static function initialize()
    {
        self::$status_cache = array_map(
            function ($row) {
                return $row->name;
            }, Model_Todo_Status::query()->select('name')->get()
        );
        self::$status_map   = Util_Array::to_map('ucwords', self::$status_cache);
        self::$status_bimap = Util_Array::bimap(self::$status_cache);
    }
}

// initiaize static member
Model_Todo_Logic::initialize();