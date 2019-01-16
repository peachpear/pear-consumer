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
                'username' => 'demo@demo.com',
                'password' => 'abc123+_*',
            ],
            'messageConfig' => [
                'from'=>['demo@demo.com'=>'no-reply']
            ],
        ],
    ],
    "params"    =>  [
        'elkIndexName' => [
            "error" =>  "error_demo_logs",
            "warning" =>  "demo_logs",
            "info" =>  "demo_logs",
            "trace" =>  "demo_logs",
        ],
        "chat_socket" => "chatsocket.demo.com",
        "chat_port" => 9605,
    ]
];
list($commonBaseConfig, $commonConfig) = include(__DIR__ . '/../../common/config/prod.php');
$baseConfig = include('base.php');

return [$commonBaseConfig, $commonConfig, $baseConfig, $initConfig];
