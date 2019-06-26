<?php

namespace WecarSwoole;

use EasySwoole\Component\Singleton;
use EasySwoole\EasySwoole\ServerManager;
use Monolog\Handler\RotatingFileHandler;
use WecarSwoole\LogHandler\SmSHandler;
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
    use Singleton;

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

    protected function __construct()
    {
    }

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

        $logger = new MonoLogger(Config::getInstance()->getConf('app_flag') ?? 'app');

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
                switch ($handleType) {
                    case 'file':
                        $handle = new RotatingFileHandler($val, 0, $levelNum, true, null, true);
                        break;
                    case 'mailer':
                    case 'email':
                        $handle = self::emailHandler($val, $levelNum);
                        break;
                    case 'sms':
                        $handle = self::smsHandler($val, $levelNum);
                        break;
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
        $messager->setFrom(["{$mailerConfig['username']}" => $config['subject'] ?? "日志邮件告警"])
            ->setTo(array_keys($config['to']));
        $emailHandler = new SwiftMailerHandler($mailer, $messager, $levelNum, false);

        return $emailHandler;
    }

    private static function smsHandler(array $config, int $levelNum): ?SmSHandler
    {
        if (!$config) {
            return null;
        }

        return new SmSHandler(array_keys($config), $levelNum);
    }
}
