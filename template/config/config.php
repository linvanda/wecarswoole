<?php

use \WecarSwoole\Util\File;

$baseConfig = [
    // 日志配置，可配置：file（后面对应目录），mailer（后面对应邮件配置）
    'logger' => [
        'debug' => [
            'file' => File::join(EASYSWOOLE_ROOT, 'storage/logs/debug_info.log'),
        ],
        'info' => [
            'file' => File::join(EASYSWOOLE_ROOT, 'storage/logs/debug_info.log'),
        ],
        'warning' => [
            'file' => File::join(EASYSWOOLE_ROOT, 'storage/logs/warning.log'),
        ],
        'error' => [
            'file' => File::join(EASYSWOOLE_ROOT, 'storage/logs/error.log'),
        ],
        'critical' => [
            'mailer' => [
                'driver' => 'default',
                'subject' => '喂车邮件告警',
                'to' => [
                ]
            ],
            'file' => File::join(EASYSWOOLE_ROOT, 'storage/logs/error.log'),
        ]
    ],
    // 邮件。可以配多个
    'mailer' => [
        'default' => [
            'host' => 'smtp.exmail.qq.com',
            'username' => 'robot@weicheche.cn',
            'password' => 'Chechewei123'
        ]
    ],
];

return array_merge(
    $baseConfig,
    ['cron_config' => require_once __DIR__ . '/cron.php'],
    ['api_config' => require_once __DIR__ . '/api/api.php'],
    ['subscriber' => require_once __DIR__ . '/subscriber/subscriber.php'],
    require_once __DIR__ . '/env/' . ENVIRON . '.php'
);