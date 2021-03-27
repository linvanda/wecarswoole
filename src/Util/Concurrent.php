<?php

namespace WecarSwoole\Util;

use Swoole\Coroutine\Channel;

/**
 * 并发执行多个业务逻辑，并等待所有业务逻辑执行完毕后一起返回所有的执行结果
 * 注意 params 需要和 tasks 一一对应，param 第二维被解析给对应的 task 作为参数。如
 * $params = [[1,2,3], ['a', 'b', 'c']]
 * $tasks = [$func1, $func2]
 * 则调用：
 * $r1 = $func1(1, 2, 3)，$r2 = $func2('a', 'b', 'c')
 * 返回：
 * 返回值数组里面依次存放有对应函数的返回结果：$rtns = [$r1, $r2]。
 * 如果任务抛出异常，则会将异常对象返回（\Throwable 实例），外面需要先判断返回值是否 \Throwable 类型
 */
class Concurrent
{
    private $params;
    private $tasks;

    public static function instance(): Concurrent
    {
        return new self();
    }

    /**
     * 添加参数
     */
    public function addParams(...$params): Concurrent
    {
        if (!is_array($this->params)) {
            $this->params = [];
        }

        $this->params = array_merge($this->params, $params);
        return $this;
    }

    /**
     * 添加待执行任务
     */
    public function addTasks(...$tasks): Concurrent
    {
        if (!is_array($this->tasks)) {
            $this->tasks = [];
        }

        $this->tasks = array_merge($this->tasks, $tasks);
        return $this;
    }

    /**
     * 对外接口
     * $tasks 待执行函数
     */
    public function exec()
    {
        return self::execTasks($this->tasks, $this->params);
    }

    /**
     * 便捷调用方法（不支持传参），调用方一般使用 use 传参
     */
    public static function simpleExec(...$tasks): array
    {
        return self::execTasks($tasks);
    }

    private static function execTasks(array $tasks, array $params = []): array
    {
        // 创建新协程执行单独的任务
        $channel = new Channel(count($tasks));
        $returns = [];
        $cnt = 0;
        foreach ($tasks as $index => $task) {
            if (!is_callable($task)) {
                continue;
            }

            $cnt++;
            go(function () use ($index, $task, $params, $channel, &$returns) {
                try {
                    $rtn = call_user_func($task, ...($params[$index] ?? []));
                } catch (\Throwable $e) {
                    // 抛异常，将异常返回
                    $rtn = $e;
                }
                $returns[$index] = $rtn;
                $channel->push(1);
            });
        }

        for (; $cnt > 0; $cnt--) {
            $channel->pop();
        }

        return $returns;
    }
}
