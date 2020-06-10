<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Default Filesystem Disk
    |--------------------------------------------------------------------------
    |
    | Here you may specify the default filesystem disk that should be used
    | by the framework. The "local" disk, as well as a variety of cloud
    | based disks are available to your application. Just store away!
    |
    */

    'default' => env('FILESYSTEM_DRIVER', 'local'),

    /*
    |--------------------------------------------------------------------------
    | Default Cloud Filesystem Disk
    |--------------------------------------------------------------------------
    |
    | Many applications store files both locally and in the cloud. For this
    | reason, you may specify a default "cloud" driver here. This driver
    | will be bound as the Cloud disk implementation in the container.
    |
    */

    'cloud' => env('FILESYSTEM_CLOUD', 's3'),

    /*
    |--------------------------------------------------------------------------
    | Filesystem Disks
    |--------------------------------------------------------------------------
    |
    | Here you may configure as many filesystem "disks" as you wish, and you
    | may even configure multiple disks of the same driver. Defaults have
    | been setup for each driver as an example of the required options.
    |
    | Supported Drivers: "local", "ftp", "sftp", "s3", "rackspace"
    |
    */

    'disks' => [

        'local' => [
            'driver' => 'local',
            'root' => storage_path('app'),
        ],
        'public' => [
            'driver' => 'local',
            'root' => storage_path('app/public'),
            'url' => env('APP_URL').'/storage',
            'visibility' => 'public',
        ],
        'headImg' => [
            'driver' => 'local',
            'root' =>public_path('app/head_image'),
        ],
        'Sto' => [
            'driver' => 'local',
            'root' =>public_path('app/sto'),
        ],
        'complain' => [
            'driver' => 'local',
            'root' =>public_path('app/complain'),
            'url' =>'app/complain',
        ],
        'userIdentify' => [
            'driver' => 'local',
            'root' =>public_path('app/user_identify'),
        ],
        'c2cTransfer' => [
            'driver' => 'local',
            'root' =>public_path('app/c2c_transferimg'),
        ],
        'outside' => [
            'driver' => 'local',
            'root' =>public_path('app/outside'),
        ],
        's3' => [
            'driver' => 's3',
            'key' => env('AWS_ACCESS_KEY_ID'),
            'secret' => env('AWS_SECRET_ACCESS_KEY'),
            'region' => env('AWS_DEFAULT_REGION'),
            'bucket' => env('AWS_BUCKET'),
            'url' => env('AWS_URL'),
        ],

        'qiniu' => [
            'driver'  => 'qiniu',
            'domains' => [
                'default'   => 'oldmall.yunzupu.online', //你的七牛域名
                'https'     => '',         //你的HTTPS域名
                'custom'    => '',     //你的自定义域名
            ],
            'access_key'=> '5-n555E3nSxtmJbMaNnX6KIfs-OAV98l2V_J47_E',  //AccessKey
            'secret_key'=> 'VCBLbDea1ukY-FbqNWrV1oeF_4jlu3k6vhMhc6mR',  //SecretKey
            'bucket'    => 'mall',  //Bucket名字
            'notify_url'=> '',  //持久化处理回调地址
        ],

    ],

];
