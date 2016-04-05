<?php

/**
* Of Login
*/
class Domain_Practice4
{
    use Trait_Naughton {
        set as protected;
    }

    public static function before()
    {
        static::set('foo', 'bar');
    }
}