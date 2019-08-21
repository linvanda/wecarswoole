<?php

namespace WecarSwoole;

use EasySwoole\EasySwoole\Config;
use EasySwoole\EasySwoole\Crontab\Crontab;
use WecarSwoole\Util\File;

class CronTabUtil
{
    private static $createdCron = false;

    /**
     * 注册定时任务，多台服务器只能有一台注册
     * @throws \WecarSwoole\Exceptions\ConfigNotFoundException
     */
    public static function register()
    {
        if (self::$createdCron) {
            return;
        }

        Config::getInstance()->loadFile(File::join(CONFIG_ROOT, 'cron.php'), false);
        $conf = Config::getInstance()->getConf('cron');
        if (!$conf || !$conf['ip'] || !$conf['tasks'] || !self::willRunCrontab($conf)) {
            return;
        }

        self::$createdCron = true;

        // 添加定时任务
        foreach ($conf['tasks'] as $cronTab) {
            Crontab::getInstance()->addTask($cronTab);
        }
    }

    /**
     * @param array $conf
     * @return bool
     * @throws Exceptions\ConfigNotFoundException
     */
    public static function willRunCrontab(array $conf): bool
    {
        if (!isset($conf['ip']) || !$conf['ip']) {
            return false;
        }

        $ips = is_string($conf['ip']) ? [$conf['ip']] : $conf['ip'];
        $env = defined('ENVIRON') ? ENVIRON : 'produce';
        reset($ips);
        if (!is_int(key($ips))) {
            $ips = [$ips[$env]];
        }

        if (!$ips || !array_intersect($ips, array_values(swoole_get_local_ip()))) {
            return false;
        }
        return true;
    }
}
