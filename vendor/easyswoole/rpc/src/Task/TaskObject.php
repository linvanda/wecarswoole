<?php
/**
 * Created by PhpStorm.
 * User: yf
 * Date: 2019-01-27
 * Time: 19:15
 */

namespace EasySwoole\Rpc\Task;


use EasySwoole\Rpc\ServiceNode;
use EasySwoole\Spl\SplBean;
use EasySwoole\Utility\Random;

class TaskObject extends SplBean
{
    protected $action;
    protected $arg;
    protected $onSuccess;
    protected $onFail;
    protected $timeout = 3.0;
    protected $taskId;
    /** @var $execNode ServiceNode */
    protected $execNode = null;

    /**
     * @return mixed
     */
    public function getAction()
    {
        return $this->action;
    }

    /**
     * 设置调用的action
     * @param mixed $action
     * @return TaskObject
     */
    public function setAction($action): TaskObject
    {
        $this->action = $action;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getArg()
    {
        return $this->arg;
    }

    /**
     * 设置参数
     * @param mixed $arg
     * @return TaskObject
     */
    public function setArg($arg): TaskObject
    {
        $this->arg = $arg;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getOnSuccess()
    {
        return $this->onSuccess;
    }

    /**
     * 设置执行成功后回调
     * @param mixed $onSuccess
     * @return TaskObject
     */
    public function setOnSuccess($onSuccess): TaskObject
    {
        $this->onSuccess = $onSuccess;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getOnFail()
    {
        return $this->onFail;
    }

    /**
     * 设置执行失败后的回调
     * @param mixed $onFail
     * @return TaskObject
     */
    public function setOnFail($onFail): TaskObject
    {
        $this->onFail = $onFail;
        return $this;
    }

    /**
     * @return float
     */
    public function getTimeout(): float
    {
        return $this->timeout;
    }

    /**
     * 设置超时
     * @param float $timeout
     * @return TaskObject
     */
    public function setTimeout(float $timeout): TaskObject
    {
        $this->timeout = $timeout;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getTaskId()
    {
        return $this->taskId;
    }

    /**
     * @param mixed $taskId
     * @return TaskObject
     */
    public function setTaskId($taskId): TaskObject
    {
        $this->taskId = $taskId;
        return $this;
    }

    /**
     * @return ServiceNode
     */
    public function getExecNode(): ?ServiceNode
    {
        return $this->execNode;
    }

    /**
     * 设置执行节点
     * @param ServiceNode $execNode
     */
    public function setExecNode(?ServiceNode $execNode = null): void
    {
        $this->execNode = $execNode;
    }

    /**
     * 初始化任务ID
     */
    protected function initialize(): void
    {
        if (empty($this->taskId)) {
            $this->taskId = Random::character(32);
        }
    }
}