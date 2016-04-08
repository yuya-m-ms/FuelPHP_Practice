<?php

use Aws\Credentials\CredentialProvider;
use Aws\S3\S3Client;

class Controller_S3 extends Controller_Template
{

    public function action_index()
    {
        $bucket = 'google-oauth-practice';

        $s3 = S3Client::factory([
            'profile' => 'default',
            'region'  => 'ap-northeast-1',
            'version' => 'latest',
        ]);

        $key = 'practice-4/test.json';
        $data['test_json'] = $s3->getObject([
            'Bucket' => $bucket,
            'Key'    => $key,
        ])['Body'];

        $key_put = 'practice-4/test_put.json';
        try {
            $data['ObjectURL'] = $s3->putObject([
                'Bucket' => $bucket,
                'Key'    => $key_put,
                'Body'   => json_encode(['test' => 'put'], JSON_PRETTY_PRINT),
                'ACL'    => 'public-read',
            ])['ObjectURL'];
        } catch (S3Exception $e) {
            Arr::set($data, 'errors.s3', $e->getMessage());
        }

        $data["subnav"] = array('index'=> 'active' );
        $this->template->title = 'S3 &raquo; Index';
        $this->template->content = View::forge('s3/index', $data);
    }

}
