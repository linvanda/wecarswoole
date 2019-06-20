<?php

namespace WecarSwoole\Tasks;

use EasySwoole\EasySwoole\Swoole\Task\AbstractAsyncTask;
use WecarSwoole\Client\Client;

/**
 * 短信发送异步任务
 * Class SendSms
 * @package WecarSwoole\Tasks
 */
class SendSms extends AbstractAsyncTask
{
    /**
     * @param $taskData ['mobile'=>13989876587,'content'=>'短信内容','options'=>[...]]
     * @param $taskId
     * @param $fromWorkerId
     * @param null $flags
     * @return mixed
     */
    protected function run($taskData, $taskId, $fromWorkerId, $flags = null)
    {
        if (!$taskData['mobile'] || !$taskData['content']) {
            return false;
        }

        $data = array_merge(
            [
                'mobile' => $taskData['mobile'],
                'content' => $taskData['content'],
                'channel_id' => 10002,
                'signature' => "智慧油站",
            ],
            $taskData['options'] ?? []
        );

        return Client::call('weiche:sms.send', $data);
    }

    protected function finish($result, $task_id)
    {
        // nothing
    }
}
