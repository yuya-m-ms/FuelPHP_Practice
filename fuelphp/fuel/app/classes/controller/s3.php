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

        $data['buckets'] = $s3->listBuckets();

        $data['objects'] = $s3->listObjects([
            'Bucket' => $bucket,
        ]);

        $key = 'practice-4/test.json';
        $data['test_json'] = $s3->getObject([
            'Bucket' => $bucket,
            'Key'    => $key,
        ])['Body'];

        $data["subnav"] = array('index'=> 'active' );
        $this->template->title = 'S3 &raquo; Index';
        $this->template->content = View::forge('s3/index', $data);
    }

}
