<?php

namespace WecarSwoole;

use EasySwoole\Component\Singleton;
use EasySwoole\EasySwoole\ServerManager;
use EasySwoole\EasySwoole\Swoole\Task\TaskManager;
use WecarSwoole\Tasks\SendSms;

/**
 * 短信服务
 * Class SMS
 * @package WecarSwoole
 */
class SMS
{
    use Singleton;

    protected function __construct()
    {
    }
    
    public function send(string $mobile, string $content, array $options = [])
    {
        $server = ServerManager::getInstance()->getSwooleServer();
        $sms = new SendSms(['mobile' => $mobile, 'content' => $content, 'options' => $options]);
        // 如果在工作进程中，则投递异步任务，否则直接执行（task进程不能投递异步任务）
        if (!$server->taskworker) {
            TaskManager::async($sms);
        } else {
            $sms->__onTaskHook($server->worker_id, $server->worker_id);
        }
    }
}
