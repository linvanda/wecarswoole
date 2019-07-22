<?php

namespace App;

use Swoole\Server;
use \WecarSwoole\Bootstrap as BaseBootstrap;

/**
 * bootstrap 启动脚本会在 work/task 进程启动时执行
 * Class Bootstrap
 * @package App
 */
class Bootstrap extends BaseBootstrap
{
    /**
     * @param Server $server
     * @param $workerId
     * @throws \Throwable
     */
    public static function boot(Server $server, $workerId)
    {
        parent::boot($server, $workerId);
        // 可以在此处添加自己项目的 boot 脚本
    }
}
