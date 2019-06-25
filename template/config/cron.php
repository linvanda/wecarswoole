<?php

/**
 * 定时任务配置
 */

return [
    // 定时任务项目名，同名的多台服务器只会有一台启动定时任务，请务必给不同项目起不同的名字，否则会相互影响
    'name' => 'please-edit-name',
//    'ip' => ['192.168.0.23'], // 指定 这台服务器执行 crontab，ip 优先于 redis
    // 使用 redis 实现只有一台服务器启动 crontab
    'redis' => 'main',
    'tasks' => [
    ]
];
