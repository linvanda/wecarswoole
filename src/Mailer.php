<?php

namespace WecarSwoole;

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

    public function __construct(string $host = '', string $username = '', string $password = '')
    {
        $this->host = $host;
        $this->username = $username;
        $this->password = $password;
    }

    public function send(\Swift_Message $message)
    {
        // 投递异步任务
        TaskManager::async(new SendMail([
            'message' => $message,
            'host' => $this->host,
            'username' => $this->username,
            'password' => $this->password
        ]));
    }

    public static function getSwiftMailer(string $host = '', string $username = '', string $password = ''): \Swift_Mailer
    {
        $config = Config::getInstance()->getConf('mailer');
        $config = $config['default'] ?? $config;

        $transport = new \Swift_SmtpTransport($host ?: $config['host']);
        $transport->setUsername($username ?: $config['username'])->setPassword($password ?: $config['password']);

        return new \Swift_Mailer($transport);
    }
}