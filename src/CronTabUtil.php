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

        if (!self::willRunCrontab($conf)) {
            return;
        }

        self::$createdCron = true;

        // 添加定时任务
        foreach ($conf['tasks'] as $cronTab) {
            Crontab::getInstance()->addTask($cronTab);
        }

        // 如果是 redis 分布式模式，需要设置 heartbeat
        if (!$conf['ip']) {
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
        if (!$conf['name']) {
            return '';
        }
        return 'crontab_d48fd_' . $conf['name'];
    }

    public static function flag()
    {
        return self::$flag;
    }

    public static function hasCreated()
    {
        return self::$createdCron;
    }

    /**
     * @param array $conf
     * @return bool
     * @throws Exceptions\ConfigNotFoundException
     */
    private static function willRunCrontab(array $conf): bool
    {
        if ($conf['ip']) {
            $conf['ip'] = is_string($conf['ip']) ? [$conf['ip']] : $conf['ip'];
            // ip 限制模式
            if (!array_intersect($conf['ip'], array_values(swoole_get_local_ip()))) {
                return false;
            }
            return true;
        }

        // redis 分布式模式
        $redis = RedisFactory::build($conf['redis'] ?? 'main');
        $flag = self::randomFlag();

        if ($redis->set(self::key(), $flag, ['nx', 'ex' => 8])) {
            self::$flag = $flag;
            return true;
        }

        return false;
    }

    private static function randomFlag()
    {
        mt_srand();
        return mt_rand(100, 10000000);
    }
}
