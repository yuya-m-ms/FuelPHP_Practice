<?php

use Aws\S3\S3Client;

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

        static::set('bucket', 'google-oauth-practice');
        static::get('s3') ?: static::set('s3', S3Client::factory([
            'profile' => 'default',
            'region'  => 'ap-northeast-1',
            'version' => 'latest',
        ]));
    }

    public static function forge_login_url($state = '')
    {
        $cs = static::get('client_secret');
        return Uri::create(Arr::get($cs, 'web.auth_uri'), [], [
            'client_id'     => Arr::get($cs, 'web.client_id'),
            'response_type' => 'code',
            'scope'         => 'openid email',
            'redirect_uri'  => Arr::get($cs, 'web.redirect_uris.0'),
            'state'         => $state,
        ]);
    }

    /**
     * Fetch token from Google by a given code
     * @param  string $code given
     * @return array       returned data of token request
     */
    public static function fetch_token($code = '')
    {
        $cs   = static::get('client_secret');
        $curl = Request::forge(Arr::get($cs, 'web.token_uri'), 'curl');
        $curl->set_method('post');
        $curl->set_params([
            'code'          => $code,
            'client_id'     => Arr::get($cs, 'web.client_id'),
            'client_secret' => Arr::get($cs, 'web.client_secret'),
            'redirect_uri'  => Arr::get($cs, 'web.redirect_uris.0'),
            'grant_type'    => 'authorization_code',
        ]);
        $res = $curl->execute()->response();
        return ($res->status == 200) ? Format::forge($res->body, 'json')->to_array() : [];
    }

    public static function fetch_user_info($token = '')
    {
        $curl = Request::forge('https://www.googleapis.com/oauth2/v1/tokeninfo', 'curl');
        $curl->set_method('get');
        $curl->set_params(['id_token' => $token, ]);
        $res = $curl->execute()->response();
        return ($res->status == 200) ? Format::forge($res->body, 'json')->to_array() : [];
    }

    /**
     * [Model] Fetch data from AWS S3
     *
     * @param  string $key ID of data
     * @return json        string
     */
    public static function fetch_from_AWS_S3($key)
    {
        try {
            return static::get('s3')->getObject([
                'Bucket' => static::get('bucket'),
                'Key'    => $key,
            ])['Body'];
        } catch (Aws\S3\Exception\S3Exception $e) {
            return '';
        }
    }

    /**
     * [Model] Store data to AWS S3
     *
     * @param  string $key  ID of data
     * @param  json   $json to be stored
     * @return string       ObjectURL
     */
    public static function store_to_AWS_S3($key, $json)
    {
        try {
            return static::get('s3')->putObject([
                'Bucket' => static::get('bucket'),
                'Key'    => $key,
                'Body'   => $json,
                'ACL'    => 'public-read',
            ])['ObjectURL'];
        } catch (Aws\S3\Exception\S3Exception $e) {
            return '';
        }
    }
}