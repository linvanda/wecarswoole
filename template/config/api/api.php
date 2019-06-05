<?php

use WecarSwoole\Client\Http\Component\DefaultHttpServerParser;
use WecarSwoole\Client\Http\Component\DefaultHttpRequestAssembler;
use WecarSwoole\Client\Http\Component\JsonResponseParser;

/**
 * 外部 api 定义
 * 可支持多种协议（典型如 http 协议，rpc 协议）
 * api 外部使用方式：group_name:apiname
 */
return [
    'config' => [
        // 请求协议
        'protocol' => 'http', // 支持的协议：http、rpc（尚未实现）
        // 当前项目 app_id
        'app_id' => 10000,
        // http 协议请求默认配置
        'http' => [
            // 服务器地址解析器，必须是 IHttpServerParser 类型
            'server_parser' => DefaultHttpServerParser::class,
            // 请求参数组装器
            'request_assembler' => DefaultHttpRequestAssembler::class,
            // 响应参数解析器
            'response_parser' => JsonResponseParser::class,
            // 请求发送前的拦截器(尚未实现)
            'before_handle' => [],
            // 收到响应后的拦截器（尚未实现）
            'after_handle' => [],
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
    'weiche' => include __DIR__ . '/weicheche.php',
];