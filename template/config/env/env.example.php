<?php

use \WecarSwoole\Util\File;

return [
    /**
     * 数据库配置建议以数据库名作为 key
     * 如果没有读写分离，则可不分 read, write，直接在里面写配置信息
     */
    'mysql' => [
        'weicheche' => [
            // 读库使用二维数组配置，以支持多个读库
            'read' => [
                [
                    'host' => '192.168.85.135',
                    'port' => 3306,
                    'user' => 'root',
                    'password' => 'weicheche',
                    'database' => 'weicheche',
                    'charset' => 'utf8',
                ]
            ],
            // 仅支持一个写库
            'write' => [
                'host' => '192.168.85.135',
                'port' => 3306,
                'user' => 'root',
                'password' => 'weicheche',
                'database' => 'weicheche',
                'charset' => 'utf8',
            ],
            // 连接池配置
            'pool' => [
                'size' => 30
            ]
        ],
        // 可以不配置读写分离
        'user_center' => [
            'host' => '192.168.85.135',
            'port' => 3306,
            'user' => 'root',
            'password' => 'weicheche',
            'database' => 'user_center',
            'charset' => 'utf8',
            // 连接池配置
            'pool' => [
                'size' => 30
            ]
        ]
    ],
    'redis' => [
        'main' => [
            'host' => 'db.redis.wcc.cn',
            'port' => 6379,
            'auth' => 'XEXeh1l6nT3wHL0z'
        ],
        'cache' => [
            'host' => 'db.redis.wcc.cn',
            'port' => 6379,
            'auth' => 'XEXeh1l6nT3wHL0z',
        ],
    ],
    // 缓存配置
    'cache' => [
        'driver' => 'redis', // 可用：redis、file、array、null(一般测试时用来禁用缓存)
        'prefix' => 'usercenter',
        'expire' => 3600, // 缓存默认过期时间，单位秒
        'redis' => 'cache', // 当 driver = redis 时，使用哪个 redis 配置
        'dir' => File::join(EASYSWOOLE_ROOT, 'storage/cache'), // 当 driver = file 时，缓存存放目录
    ],
    // 最低记录级别：debug, info, warning, error, critical, off
    'log_level' => 'debug',
];