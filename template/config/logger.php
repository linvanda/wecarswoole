<?php

use WecarSwoole\Util\File;

return [
    'debug' => [
        'file' => File::join(EASYSWOOLE_ROOT, 'storage/logs/info_%Y-%m-%d.log'),
    ],
    'info' => [
        'file' => File::join(EASYSWOOLE_ROOT, 'storage/logs/info_%Y-%m-%d.log'),
    ],
    'warning' => [
        'file' => File::join(EASYSWOOLE_ROOT, 'storage/logs/warning_%Y-%m-%d.log'),
    ],
    'error' => [
        'file' => File::join(EASYSWOOLE_ROOT, 'storage/logs/error_%Y-%m-%d.log'),
    ],
    'critical' => [
        'mailer' => [
            'driver' => 'default',
            'subject' => '喂车告警',
            'to' => [
            ]
        ],
        'file' => File::join(EASYSWOOLE_ROOT, 'storage/logs/error_%Y-%m-%d.log'),
    ],
    'emergency' => [
        'mailer' => [
            'driver' => 'default',
            'subject' => '喂车告警',
            'to' => [
                // 邮箱列表，格式：'songlin.zhang@weicheche.cn' => '张松林'
            ]
        ],
        'file' => File::join(EASYSWOOLE_ROOT, 'storage/logs/error_%Y-%m-%d.log'),
        'sms' => [
            // 手机号列表，格式：'18987674848' => '张松林'
        ]
    ],
];
