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
        $dir  = realpath(DOCROOT.'../..');
        $path = glob($dir.'/client_secret_*.json')[0];
        $json = File::read($path, true);
        $data = Format::forge($json, 'json')->to_array();
        static::set('client_secret', $data);
    }

    public static function forge_login_url()
    {
        return 'https://i.wanna.log.in';
    }
}