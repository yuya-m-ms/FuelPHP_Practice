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
        ;
    }

    public static function forge_login_url()
    {
        return 'https://i.wanna.log.in';
    }
}