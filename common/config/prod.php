<?php
defined("ENV") || define("ENV", "prod");
$baseConfig = include('base.php');

$commonConfig = array(
    'components' => [
        'cache' => [
            'host' => '',
            'port' => 6379,
            'keyPrefix' => '',
        ],
        'demoDB'  => [
            'dsn' => '',
            'username' => '',
            'password' => '',
        ],
        'kafkaProducer' => [
            "metadata" => [
                "brokerList" => "192.168.40.122:9200",
            ],
            "requireAck" => 0,
        ],
        'queue' => [
            'credentials' => [
                'host' => '',
                'port' => '5672',
                'login' => '',
                'password' => ''
            ]
        ],
    ],
    'params' => [
        'ticket' => [
            'api_url' => 'http://prod.demo.com',
            'api_secret' => 'prod6bcd4341d373cade4e832456b4f7',
        ],
    ],
    "configService" => [
        "filePath" => "/config/prod/",
        "fileExt" => "json",
    ]
);

return [$baseConfig, $commonConfig];