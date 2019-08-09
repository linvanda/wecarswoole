<?php

namespace WecarSwoole;

use EasySwoole\EasySwoole\ServerManager;
use WecarSwoole\Tasks\SendMail;
use EasySwoole\EasySwoole\Config;
use EasySwoole\EasySwoole\Swoole\Task\TaskManager;

/**
 * 邮件发送服务，基于 SwiftMailer
 * Class Mailer
 * @package WecarSwoole
 */
class Mailer
{
    private $host;
    private $username;
    private $password;
    private $port;

    public function __construct(string $host = '', string $username = '', string $password = '', int $port = 25)
    {
        $this->host = $host;
        $this->username = $username;
        $this->password = $password;
        $this->port = $port;
    }

    public function send(\Swift_Message $message)
    {
        $server = ServerManager::getInstance()->getSwooleServer();
        $mailer = new SendMail([
            'message' => $message,
            'host' => $this->host,
            'username' => $this->username,
            'password' => $this->password,
            'port' => $this->port
        ]);
        // 如果在工作进程中，则投递异步任务，否则直接执行（task进程不能投递异步任务）
        if (!$server->taskworker) {
            TaskManager::async($mailer);
        } else {
            $mailer->__onTaskHook($server->worker_id, $server->worker_id);
        }
    }

    public static function getSwiftMailer(
        string $host = '',
        string $username = '',
        string $password = '',
        int $port = 25
    ): \Swift_Mailer {
        $config = Config::getInstance()->getConf('mailer');
        $config = $config['default'] ?? $config;

        $transport = new \Swift_SmtpTransport($host ?: $config['host'], $port ?: $config['port']);
        $transport->setUsername($username ?: $config['username'])->setPassword($password ?: $config['password']);

        return new \Swift_Mailer($transport);
    }
}
