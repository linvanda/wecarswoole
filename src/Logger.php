<?php

namespace WecarSwoole;

use EasySwoole\EasySwoole\ServerManager;
use WecarSwoole\Tasks\Log;
use EasySwoole\EasySwoole\Config;
use EasySwoole\EasySwoole\Swoole\Task\TaskManager;
use Monolog\Handler\NullHandler;
use Psr\Log\AbstractLogger;
use Monolog\Logger as MonoLogger;
use Monolog\Handler\StreamHandler;
use Monolog\Handler\SwiftMailerHandler;

/**
 * 日志
 * Class Logger
 * @package WecarSwoole
 */
class Logger extends AbstractLogger
{
    protected static $levels = [
        'DEBUG' => MonoLogger::DEBUG,
        'INFO' => MonoLogger::INFO,
        'NOTICE' => MonoLogger::NOTICE,
        'WARNING' => MonoLogger::WARNING,
        'ERROR' => MonoLogger::ERROR,
        'CRITICAL' => MonoLogger::CRITICAL,
        'ALERT' => MonoLogger::ALERT,
        'EMERGENCY' => MonoLogger::EMERGENCY,
    ];

    public function log($level, $message, array $context = array())
    {
        $server = ServerManager::getInstance()->getSwooleServer();
        $log = new Log(['level' => $level, 'message' => $message, 'context' => $context]);
        // 如果在工作进程中，则投递异步任务，否则直接执行（task进程不能投递异步任务）
        if (!$server->taskworker) {
            TaskManager::async($log);
        } else {
            $log->__onTaskHook($server->worker_id, $server->worker_id);
        }
    }

    /**
     * Monolog 工厂方法
     * @return MonoLogger
     */
    public static function getMonoLogger()
    {
        $minLevel = Config::getInstance()->getConf('log_level') ?? 'error';

        if ($minLevel !== 'off' && !array_key_exists(strtoupper($minLevel), self::$levels)) {
            $minLevel = 'error';
        }

        $logger = new MonoLogger('app');

        foreach (self::handlers($minLevel) as $handler) {
            $logger->pushHandler($handler);
        }

        return $logger;
    }

    private static function handlers($minLevel): array
    {
        if ($minLevel == 'off') {
            return [new NullHandler(MonoLogger::DEBUG)];
        }

        if (!($tmpConfig = Config::getInstance()->getConf('logger'))) {
            return [];
        }

        $levelConfig = [];
        foreach ($tmpConfig as $levelName => $conf) {
            $levelName = strtoupper($levelName);
            if (!array_key_exists($levelName, self::$levels)) {
                continue;
            }

            $levelConfig[self::$levels[$levelName]] = $conf;
        }
        unset($tmpConfig);

        // 低级别放前面
        ksort($levelConfig);
        $minLevelNum = self::$levels[strtoupper($minLevel)];

        $handles = [];
        foreach ($levelConfig as $levelNum => $config) {
            if ($levelNum < $minLevelNum) {
                continue;
            }

            $cnt = 0;
            foreach ($config as $handleType => $val) {
                if ($handleType == 'file' && $val) {
                    $handle = new StreamHandler($val, $levelNum);
                } elseif ($handleType == 'mailer' || $handleType == 'email') {
                    $handle = self::emailHandler($val, $levelNum);
                }

                if (!$handle) {
                    continue;
                }

                // 第一个设置 buddle = false
                if ($cnt++ == 0) {
                    $handle->setBubble(false);
                }

                $handles[] = $handle;
            }
        }

        // 如果是命令行调试模式，则增加 StreamHandler
        if (DEBUG_MODEL) {
            $handles[] = new StreamHandler(STDOUT);
        }

        return $handles;
    }

    private static function emailHandler(array $config, int $levelNum): ?SwiftMailerHandler
    {
        $mailerConfig = $config['driver'] ? Config::getInstance()->getConf("mailer.{$config['driver']}") : null;
        if (!$mailerConfig || !$config['to']) {
            return null;
        }

        $mailer = Mailer::getSwiftMailer(
            $mailerConfig['host'] ?? '',
            $mailerConfig['username'] ?? '',
            $mailerConfig['password'] ?? ''
        );

        $messager = new \Swift_Message($config['subject'] ?? "日志邮件告警");
        $messager->setFrom(["{$mailerConfig['username']}" => $config['subject'] ?? "日志邮件告警"])->setTo($config['to']);
        $emailHandler = new SwiftMailerHandler($mailer, $messager, $levelNum, false);

        return $emailHandler;
    }
}
