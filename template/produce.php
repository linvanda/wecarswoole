<?php

return [
    'SERVER_NAME' => "EasySwoole",
    'MAIN_SERVER' => [
        'LISTEN_ADDRESS' => '0.0.0.0',
        'PORT' => 9501,
        'SERVER_TYPE' => EASYSWOOLE_WEB_SERVER,
        'SOCK_TYPE' => SWOOLE_TCP,
        'RUN_MODEL' => SWOOLE_PROCESS,
        'SETTING' => [
            'worker_num' => 2,
            'task_worker_num' => 10,
            'reload_async' => true,
            'max_wait_time' => 5,
            'max_request' => 10000,
            'task_enable_coroutine' => true,
            'task_max_request' => 10000,
            'dispatch_mode' => 1,
            'enable_reuse_port' => 1,
            'pid_file' => \WecarSwoole\Util\File::join(STORAGE_ROOT, 'temp/master.pid')
        ],
    ],
    'TEMP_DIR' => 'storage/temp',
    'LOG_DIR' => 'storage/logs',
    'CONSOLE' => [
        'ENABLE' => true,
        'LISTEN_ADDRESS' => '127.0.0.1',
        'HOST' => '127.0.0.1',
        'PORT' => 9500,
        'USER' => 'root',
        'PASSWORD' => '123456'
    ],
    'DISPLAY_ERROR' => true,
    'PHAR' => [
        'EXCLUDE' => ['.idea', 'log', 'temp', 'easyswoole', 'easyswoole.install']
    ]
];
