<?php

namespace WecarSwoole\HealthCheck;

use EasySwoole\EasySwoole\ServerManager;
use Psr\Log\LoggerInterface;
use Psr\Log\LogLevel;

/**
 * 系统健康情况监测
 * Class HealthCheck
 * @package WecarSwoole\HealthCheck
 */
class HealthCheck
{
    private const BUCKETS_SIZE = 60;
    private const TICK_FREQ = 60000;

    /**
     * @var LoggerInterface
     */
    private static $logger;
    private static $started = false;
    private static $memoryLimit;
    private static $memoryBuckets;
    private static $peakMemory = 0;
    private static $currThreshold;
    private static $avgThreshold;
    private static $lastCriticalLogTime = 0;
    private static $lastEmergencyLogTime = 0;
    private static $processType;

    public static function watch(
        LoggerInterface $logger,
        float $currThreshold = 0.8,
        float $avgThreshold = 0.7,
        string $processType = null
    ) {
        if (self::$started) {
            return;
        }

        self::$started = true;
        self::$logger = $logger;
        self::$memoryLimit = self::memoryLimit();
        self::$memoryBuckets = new Buckets(self::BUCKETS_SIZE);
        self::$currThreshold = $currThreshold;
        self::$avgThreshold = $avgThreshold;
        self::$processType = $processType;

        // 启动定时器定时监测内存使用情况
        swoole_timer_tick(self::TICK_FREQ, function () {
            self::memoryCheck();
        });
    }

    private static function memoryCheck()
    {
        self::$peakMemory = max(self::$peakMemory, memory_get_peak_usage(true));
        $currMemoryUsed = memory_get_usage(true);
        self::$memoryBuckets->push($currMemoryUsed);

        $errMsg = '';

        // 当前内存使用量是否超过内存限制的预警百分比
        $currPer = $currMemoryUsed / self::$memoryLimit;
        if ($currPer > self::$currThreshold) {
            $errMsg .= sprintf(
                "当前内存使用量超过 %d%%。内存限制：%d,已使用 %d。",
                intval($currPer * 100),
                self::$memoryLimit,
                $currMemoryUsed
            );
        }

        // 10 分钟内的内存使用量均值是否超过预警
        $avg = array_sum(self::$memoryBuckets->toArray()) / min(self::$memoryBuckets->size(), self::$memoryBuckets->current());
        $avgPer = $avg / self::$memoryLimit;
        if ($avgPer > self::$avgThreshold) {
            $errMsg .= sprintf(
                "10 分钟内内存使用平均值超过 %d%%。内存限制：%d,10分钟均值 %d。",
                intval($avgPer * 100),
                self::$memoryLimit,
                intval($avg)
            );
        }

        if ($errMsg) {
            $server = ServerManager::getInstance()->getSwooleServer();
            $pid = $server->worker_id;
            $ptype = self::$processType ?? ($server->taskworker ? 'task进程' : 'work进程');

            $ip = array_values(swoole_get_local_ip())[0];
            $errMsg = "ip:{$ip},{$ptype}pid:{$pid}。内存峰值：" . self::$peakMemory . "。$errMsg";
            self::log($errMsg);
        }
    }

    /**
     * 每次都发送 error 级别告警(普通日志)，1分钟 一次 critical（邮件），5分钟一次 emergency（短信）
     * @param string $str
     */
    private static function log(string $str)
    {
        $time = time();

        $logLevel = LogLevel::ERROR;
        if ($time - self::$lastEmergencyLogTime >= 300) {
            $logLevel = LogLevel::EMERGENCY;
            self::$lastEmergencyLogTime = $time;
        } elseif ($time - self::$lastCriticalLogTime >= 60) {
            $logLevel = LogLevel::CRITICAL;
            self::$lastCriticalLogTime = $time;
        }

        self::$logger->log($logLevel, $str);
    }

    private static function memoryLimit(): int
    {
        $limit = ini_get('memory_limit');
        $size = substr($limit, 0, -1);
        switch (strtoupper(substr($limit, -1, 1))) {
            case 'K':
                return $size * 1024;
            case 'M':
                return $size * 1024 * 1024;
            case 'G':
                return $size * 1024 * 1024 * 1024;
        }

        return intval($limit);
    }
}
