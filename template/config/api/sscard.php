<?php

use \App\Foundation\Client\SSCardHttpRequestAssembler;

/**
 * ss 储值卡相关接口
 */
return [
    'config' => [
        'server' => 'CD',
        // 请求参数组装器
        'request_assembler' => SSCardHttpRequestAssembler::class,
    ],
    'api' => [
        'key.sync' => [
            'name' => '更换密钥',
            'path' => '/Sys/KeyNegotiation',
            'method' => 'POST'
        ]
    ]
];
