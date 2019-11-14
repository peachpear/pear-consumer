<?php
defined('YII_DEBUG') or define("YII_DEBUG", false);

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
                'username' => 'pre-demo@demo.com',
                'password' => 'abc123+_*',
            ],
            'messageConfig' => [
                'from'=>['pre-demo@demo.com'=>'no-reply']
            ],
        ],
    ],
    "params"    =>  [
        'root' => dirname(__DIR__),
        'pidfile_root' => '/var/log/rabbitMQ/',
        'elkIndexName' => [
            "error" =>  "error_demo_logs_pre",
            "warning" =>  "demo_logs_pre",
            "info" =>  "demo_logs_pre",
            "trace" =>  "demo_logs_pre",
        ],
        "chat_socket" => "pre.chatsocket.demo.com",
        "chat_port" => 9605,
    ]
];
list($commonBaseConfig, $commonConfig) = include(__DIR__ . '/../../common/config/pre.php');
$baseConfig = include('base.php');

return [$commonBaseConfig, $commonConfig, $baseConfig, $initConfig];
