<?php

use WecarSwoole\Client\Http\Component\WecarHttpRequestAssembler;
use WecarSwoole\Client\Http\Component\JsonResponseParser;

/**
 * 喂车内部子系统 api 定义
 */
return [
    'config' => [
        // 请求参数组装器
        'request_assembler' => WecarHttpRequestAssembler::class,
        // 响应参数解析器
        'response_parser' => JsonResponseParser::class,
    ],
    // api 定义
    'api' => [
        // 不要删这个，告警短信用到
        'sms.send' => [
            'server' => 'DX',
            'path' => 'v1.0/sms/send',
            'method' => 'POST'
        ]
    ]
];
