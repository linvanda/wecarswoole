<?php

namespace WecarSwoole\Process;

use EasySwoole\Component\Process\AbstractProcess;
use EasySwoole\Component\Timer;
use EasySwoole\EasySwoole\Config;
use WecarSwoole\RedisFactory;
use WecarSwoole\CronTabUtil;

class CronHeartBeatProcess extends AbstractProcess
{
    /**
     * @param $arg
     */
    public function run($arg)
    {
        if (!CronTabUtil::hasCreated()) {
            return;
        }

        $conf = Config::getInstance()->getConf('cron_config');
        $redis = RedisFactory::build($conf['redis'] ?? 'main');
        // 定时设置 redis 锁，防止其它服务器启动定时任务
        Timer::getInstance()->loop(5 * 1000, function () use ($redis) {
            $key = CronTabUtil::key();
            $flag = $redis->get($key);

            if ($flag) {
                if ($flag == CronTabUtil::flag()) {
                    $redis->expire($key, 8);
                } else {
                    // 其它服务器启动了 crontab，清除本服务器的定时任务检测
                    Timer::getInstance()->clear('crontab_heartbeat');
                }
            } else {
                // 没有设置，有可能是被异常清除了，试图设置
                if (CronTabUtil::flag() && $redis->setnx($key, CronTabUtil::flag())) {
                    $redis->expire($key, 8);
                } else {
                    Timer::getInstance()->clear('crontab_heartbeat');
                }
            }
        }, 'crontab_heartbeat');
    }

    public function onShutDown()
    {
        CronTabUtil::clean();
    }

    public function onReceive(string $str)
    {
    }
}
