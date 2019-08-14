<?php

use function WecarSwoole\Config\apollo;
use WecarSwoole\Util\File;

$baseConfig = [
    'app_name' => '用户系统',
    // 应用标识
    'app_flag' => 'YH',
    'app_id' => 10017,
    'request_id_key' => 'wcc-request-id',
    'server' => [
        'modules' => apollo('fw.modules'),
        'app_ids' => apollo('fw.appids'),
    ],
    // 邮件。可以配多个
    'mailer' => [
        'default' => [
            'host' => apollo('fw.mail', 'mail.host'),
            'username' => apollo('fw.mail', 'mail.username'),
            'password' => apollo('fw.mail', 'mail.password'),
            'port' => apollo('fw.mail', 'mail.port') ?: 25,
        ]
    ],
    // 并发锁配置
    'concurrent_locker' => [
        'onoff' => apollo('application', 'concurrent_locker.onoff') ?: 'off',
        'redis' => apollo('application', 'concurrent_locker.redis') ?: 'main',
    ],
    // 请求日志配置。默认是关闭的，如果项目需要开启，则自行修改为 on
    'request_log' => [
        'onoff' => apollo('application', 'request_log.onoff') ?: 'off',
        // 记录哪些请求类型的日志
        'methods' => explode(',', apollo('application', 'request_log.methods'))
    ],
    /**
     * 数据库配置建议以数据库名作为 key
     * 如果没有读写分离，则可不分 read, write，直接在里面写配置信息
     */
    'mysql' => [
        'weicheche' => [
            // 读库使用二维数组配置，以支持多个读库
            'read' => [
                [
                    'host' => apollo('fw.mysql.weicheche.ro', 'weicheche_read.host'),
                    'port' => apollo('fw.mysql.weicheche.ro', 'weicheche.port'),
                    'user' => apollo('fw.mysql.weicheche.ro', 'weicheche_read.username'),
                    'password' => apollo('fw.mysql.weicheche.ro', 'weicheche_read.password'),
                    'database' => apollo('fw.mysql.weicheche.ro', 'weicheche_read.dbname'),
                    'charset' => apollo('fw.mysql.weicheche.ro', 'weicheche_read.charset'),
                ]
            ],
            // 仅支持一个写库
            'write' => [
                'host' => apollo('fw.mysql.weicheche.rw', 'weicheche.host'),
                'port' => apollo('fw.mysql.weicheche.rw', 'weicheche.port'),
                'user' => apollo('fw.mysql.weicheche.rw', 'weicheche.username'),
                'password' => apollo('fw.mysql.weicheche.rw', 'weicheche.password'),
                'database' => apollo('fw.mysql.weicheche.rw', 'weicheche.dbname'),
                'charset' => apollo('fw.mysql.weicheche.rw', 'weicheche.charset'),
            ],
            // 连接池配置
            'pool' => [
                'size' => apollo('application', 'mysql.weicheche.pool_size') ?: 15
            ]
        ],
    ],
    'redis' => [
        'main' => [
            'host' => apollo('fw.redis.01', 'redis.host'),
            'port' => apollo('fw.redis.01', 'redis.port'),
            'auth' => apollo('fw.redis.01', 'redis.auth'),
            // 连接池配置
            '__pool' => [
                'max_object_num' => apollo('application', 'redis.pool.main.max_num') ?? 15,
                'min_object_num' => apollo('application', 'redis.pool.main.min_num') ?? 1,
                'max_idle_time' => apollo('application', 'redis.pool.main.idle_time') ?? 300,
            ]
        ],
        'cache' => [
            'host' => apollo('fw.redis.01', 'redis.host'),
            'port' => apollo('fw.redis.01', 'redis.port'),
            'auth' => apollo('fw.redis.01', 'redis.auth'),
            // 连接池配置
            '__pool' => [
                'max_object_num' => apollo('application', 'redis.pool.cache.max_num') ?? 15,
                'min_object_num' => apollo('application', 'redis.pool.cache.min_num') ?? 1,
                'max_idle_time' => apollo('application', 'redis.pool.cache.idle_time') ?? 300,
            ]
        ],
    ],
    // 缓存配置
    'cache' => [
        'driver' => apollo('application', 'cache.driver'),// 可用：redis、file、array、null(一般测试时用来禁用缓存)
        'prefix' => 'usercenter',
        'expire' => 3600, // 缓存默认过期时间，单位秒
        'redis' => 'cache', // 当 driver = redis 时，使用哪个 redis 配置
        'dir' => File::join(EASYSWOOLE_ROOT, 'storage/cache'), // 当 driver = file 时，缓存存放目录
    ],
    // 最低记录级别：debug, info, warning, error, critical, off
    'log_level' => apollo('application', 'log_level') ?: 'info',
    'base_url' => apollo('application', 'base_url'),
];

return array_merge(
    $baseConfig,
    ['logger' => include_once __DIR__ . '/logger.php'],
    ['api' => require_once __DIR__ . '/api/api.php'],
    ['subscriber' => require_once __DIR__ . '/subscriber/subscriber.php']
);
