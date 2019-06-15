<?php

namespace WecarSwoole\Tasks;

use WecarSwoole\Logger;
use EasySwoole\EasySwoole\Swoole\Task\AbstractAsyncTask;

/**
 * 记录日志
 * Class Log
 * @package WecarSwoole\Tasks
 */
class Log extends AbstractAsyncTask
{
    /**
     * @param array $taskData 格式 ['level' => $level, 'message' => $message, 'context' => $context]
     * @param $taskId
     * @param $fromWorkerId
     * @param null $flags
     * @return mixed
     */
    protected function run($taskData, $taskId, $fromWorkerId, $flags = null)
    {
        if (!$taskData['level'] || !$taskData['message']) {
            return false;
        }

        Logger::getMonoLogger()->log($taskData['level'], $taskData['message'], $taskData['context'] ?? []);
    }

    protected function finish($result, $task_id)
    {
        // nothing
    }
}
