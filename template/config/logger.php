<?php

use WecarSwoole\Util\File;

return [
    'debug' => [
        'file' => File::join(STORAGE_ROOT, 'logs/info.log'),
    ],
    'info' => [
        'file' => File::join(STORAGE_ROOT, 'logs/info.log'),
    ],
    'warning' => [
        'file' => File::join(STORAGE_ROOT, 'logs/warning.log'),
    ],
    'error' => [
        'file' => File::join(STORAGE_ROOT, 'logs/error.log'),
    ],
    'critical' => [
        'mailer' => [
            'driver' => 'default',
            'subject' => '喂车告警',
            'to' => [
            ]
        ],
        'file' => File::join(STORAGE_ROOT, 'logs/error.log'),
    ],
    'emergency' => [
        'mailer' => [
            'driver' => 'default',
            'subject' => '喂车告警',
            'to' => [
                // 邮箱列表，格式：'songlin.zhang@weicheche.cn' => '张松林'
            ]
        ],
        'file' => File::join(STORAGE_ROOT, 'logs/error.log'),
        'sms' => [
            // 手机号列表，格式：'18987674848' => '张松林'
        ]
    ],
];
