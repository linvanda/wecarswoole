<?php

use \WecarSwoole\Util\File;

return [
    // 缓存配置
    'cache' => [
        'driver' => 'redis', // 可用：redis、file、array、null(一般测试时用来禁用缓存)
        'prefix' => 'usercenter',
        'expire' => 3600, // 缓存默认过期时间，单位秒
        'redis' => 'cache', // 当 driver = redis 时，使用哪个 redis 配置
        'dir' => File::join(STORAGE_ROOT, 'cache'), // 当 driver = file 时，缓存存放目录
    ],
    // 最低记录级别：debug, info, warning, error, critical, off
    'log_level' => 'debug',
];
