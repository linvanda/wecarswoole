<?php

use WecarSwoole\Client\Http\Component\WecarHttpRequestAssembler;
use WecarSwoole\Client\Http\Component\JsonResponseParser;
use WecarSwoole\Client\Http\Middleware\LogRequestMiddleware;
use WecarSwoole\Client\Http\Middleware\MockRequestMiddleware;

/**
 * 外部 api 定义
 * 可支持多种协议（典型如 http 协议，rpc 协议）
 * api 外部使用方式：group_name:apiname
 */
return [
    'config' => [
        // 请求协议
        'protocol' => 'http', // 支持的协议：http、rpc（尚未实现）
        // http 协议请求默认配置
        'http' => [
            // 请求参数组装器
            'request_assembler' => WecarHttpRequestAssembler::class,
            // 响应参数解析器
            'response_parser' => JsonResponseParser::class,
            // 请求中间件，必须实现 \WecarSwoole\Client\Http\Middleware\IRequestMiddleware 接口
            'middlewares' => [
                LogRequestMiddleware::class,
                MockRequestMiddleware::class
            ],
            'throw_exception' => true, // 当返回不是 20X 时是否抛出异常
            // https ssl 相关配置
            'ssl' => [
                // CA 文件路径
                'cafile' => '',
                // 是否验证服务器端证书
                'ssl_verify_peer' => false,
                // 是否允许自签名证书
                'ssl_allow_self_signed' => true
            ]
        ],
    ],
    // 组
    'weiche' => include_once __DIR__ . '/weicheche.php',
    'sscard' => include_once __DIR__ . '/sscard.php',
];
