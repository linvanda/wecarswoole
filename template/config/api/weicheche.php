<?php

/**
 * 喂车内部子系统 api 定义
 */
return [
    // api 定义
    'api' => [
        'sms.send' => [
            'server' => 'DX',
            'path' => 'v1.0/sms/send',
            'method' => 'POST'
        ]
    ]
];
