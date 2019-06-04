<?php

namespace WecarSwoole;

use WecarSwoole\Process\CronHeartBeatProcess;
use EasySwoole\EasySwoole\Config;
use EasySwoole\EasySwoole\Crontab\Crontab;
use EasySwoole\EasySwoole\ServerManager;

class CronTabUtil
{
    private static $createdCron = false;
    private static $flag;

    /**
     * 注册定时任务，多台服务器只能有一台注册
     * @throws \WecarSwoole\Exceptions\ConfigNotFoundException
     */
    public static function register()
    {
        if (self::$createdCron) {
            return;
        }

        $conf = Config::getInstance()->getConf('cron_config');
        if (!$conf || !$conf['name'] || !$conf['tasks']) {
            return;
        }

        $redis = RedisFactory::build($conf['redis'] ?? 'main');

        $flag = self::randomFlag();
        if ($redis->setnx(self::key(), $flag)) {
            $redis->expire(self::key(), 8);

            self::$flag = $flag;
            self::$createdCron = true;

            // 添加定时任务
            foreach ($conf['tasks'] as $cronTab) {
                Crontab::getInstance()->addTask($cronTab);
            }

            // 自定义进程进行 heartbeat
            ServerManager::getInstance()->getSwooleServer()
                ->addProcess((new CronHeartBeatProcess('crontab_heartbeat'))->getProcess());
        }
    }

    /**
     * @throws \WecarSwoole\Exceptions\ConfigNotFoundException
     */
    public static function clean()
    {
        if (!self::$createdCron) {
            return;
        }

        $conf = Config::getInstance()->getConf('cron_config');
        RedisFactory::build($conf['redis'] ?? 'main')->del(self::key());
    }

    public static function key()
    {
        $conf = Config::getInstance()->getConf('cron_config');
        return 'crontab_d48fd_' . $conf['name'] ?? '';
    }

    public static function flag()
    {
        return self::$flag;
    }

    public static function hasCreated()
    {
        return self::$createdCron;
    }

    private static function randomFlag()
    {
        mt_srand();
        return mt_rand(100, 10000000);
    }
}