<?php
defined('YII_DEBUG') or define("YII_DEBUG", true);

$initConfig = [
    "components"  =>  [
        'errorHandler'  =>  [
            "sendTo"   =>  ["xxx1@demo.com","xxx2@demo.com"],
            "sendCC"    =>  [
                "xxxx@demo.com"=>"xxxx",
            ],
        ],
        'mailer' => [
            'transport' => [
                'username' => 'dev-demo@demo.com',
                'password' => 'abc123+_*',
            ],
            'messageConfig' => [
                'from'=>['dev-demo@demo.com'=>'no-reply']
            ],
        ],
    ],
    "params"    =>  [
        'root' => dirname(__DIR__),
        'pidfile_root' => '/var/log/rabbitMQ/',
        'elkIndexName' => [
            "error" =>  "error_demo_logs_dev",
            "warning" =>  "demo_logs_dev",
            "info" =>  "demo_logs_dev",
        ],
        "chat_socket" => "dev.chatsocket.demo.com",
        "chat_port" => 9605,
    ]
];

list($commonBaseConfig, $commonConfig) = include(__DIR__ . '/../../common/config/dev.php');
$baseConfig = include('base.php');

return [$commonBaseConfig, $commonConfig, $baseConfig, $initConfig];