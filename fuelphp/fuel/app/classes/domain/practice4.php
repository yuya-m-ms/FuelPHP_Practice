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
        $token = Security::generate_token();
        Session::set('google_oauth.state', $token);
        $cs = static::get('client_secret');
        return Uri::create(Arr::get($cs, 'web.auth_uri'), [], [
            'client_id'     => Arr::get($cs, 'web.client_id'),
            'response_type' => 'code',
            'scope'         => 'openid email',
            'redirect_uri'  => Arr::get($cs, 'web.redirect_uris.0'),
            'state'         => $token,
        ]);
    }
}