<?php

namespace WecarSwoole;
use EasySwoole\EasySwoole\Swoole\Task\TaskManager;
use WecarSwoole\Tasks\SendSms;

/**
 * 短信服务
 * Class Sms
 * @package WecarSwoole
 */
class Sms
{
    public function send(string $mobile, string $content, array $options = [])
    {
        // 投递异步任务
        TaskManager::async(new SendSms(['mobile' => $mobile, 'content' => $content, 'options' => $options]));
    }
}
