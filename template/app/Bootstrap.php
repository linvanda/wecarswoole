<?php

namespace App;

use \WecarSwoole\Bootstrap as BaseBootstrap;

/**
 * bootstrap 启动脚本会在 work/task 进程启动时执行
 * Class Bootstrap
 * @package App
 */
class Bootstrap extends BaseBootstrap
{
    public static function boot()
    {
        parent::boot();
        // 可以在此处添加自己项目的 boot 脚本
    }
}
