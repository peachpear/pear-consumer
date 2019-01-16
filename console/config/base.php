<?php
return array(
	'name' => 'demo',
	'id'   =>   "demo-console",
	'basePath' => dirname(__DIR__),
    'controllerNamespace'   =>  "console\controllers",
    'aliases' => [
        '@console' => realpath(__DIR__."/../"),
    ],
    "components" =>  [
        'errorHandler' => [
            'class' => 'console\components\LConsoleErrorHandler',
        ],
        'mailer' => [
            'useFileTransport' =>false,  // false发送邮件；true只是生成邮件在runtime文件夹下，不发邮件
            'transport' => [
                'class' => 'Swift_SmtpTransport',
                'host' => 'smtp.exmail.qq.com',  // 每种邮箱的host配置不一样
                'port' => '465',
                'encryption' => 'ssl',
            ],
            'messageConfig' => [
                'charset'=>'UTF-8',
            ],
        ],
    ],
    'params' => [
        'CLog' => [
            'queue' => 'queue_logs',
            'exchange' => 'logstash',
            'routing' => 'logs'
        ],
        'CMail' => [
            'queue' => 'queue_mail',
            'exchange' => 'async',
            'routing' => 'mail'
        ],
        'CTicket' => [
            'queue' => 'queue_ticket',
            'exchange' => 'async',
            'routing' => 'ticket'
        ],
        'DTicket' => [
            'queue' => 'queue_delay_ticket',
            'exchange' => 'delay',
            'routing' => 'delay_ticket',
            'x_exchange' => 'async',
            'x_routing' => 'ticket'
        ],
        'CPushSocket' => [
            'queue' => 'queue_push_socket',
            'exchange' => 'async',
            'routing' => 'push_socket'
        ],
    ],
);