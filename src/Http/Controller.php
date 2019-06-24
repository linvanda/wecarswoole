<?php

namespace WecarSwoole\Http;

use EasySwoole\Http\AbstractInterface\Controller as EsController;
use Psr\Log\LoggerInterface;
use WecarSwoole\Container;
use WecarSwoole\Exceptions\EmergencyErrorException;
use WecarSwoole\Exceptions\CriticalErrorException;
use WecarSwoole\Http\Middlewares\LockerMiddleware;
use WecarSwoole\Http\Middlewares\RequestRecordMiddleware;
use WecarSwoole\Http\Middlewares\RequestTimeMiddleware;
use WecarSwoole\MiddlewareHelper;
use WecarSwoole\RedisFactory;

/**
 * 控制器基类
 * 不要在基类控制器写很多代码，建议抽离成单独的类处理
 * 禁止在基类控制器中写 public 方法，会造成后面难以维护
 * Class Controller
 * @package WecarSwoole\Http
 */
class Controller extends EsController
{
    use MiddlewareHelper;

    protected $responseData;

    public function __construct()
    {
        $this->appendMiddlewares(
            [
                new LockerMiddleware(),
                new RequestTimeMiddleware(RedisFactory::build('main'), Container::get(LoggerInterface::class)),
                new RequestRecordMiddleware()
            ]
        );
        parent::__construct();
    }

    /**
     * 并发锁定义，定义用哪些请求信息生成锁的 key。默认采用"客户端 ip + 请求url + 请求数据"生成 key
     * 格式：[请求action=>[请求字段数组]]。
     * 注意，如果提供了该方法，默认是严格按照该方法的定义实现锁的，即如果请求action没有出现在该方法中，就不会加锁，
     * 除非加上 '__default' => 'default'，表示如果没有出现在该方法中，就使用默认策略加锁（客户端 ip + 请求url + 请求数据）。
     * 例：
     * // 只有 addUser 会加锁：
     * [
     *      'addUser' => ['phone', 'parter_id']
     * ]
     * // addUser 按照指定策略加锁，其它 action 按照默认策略加锁
     * [
     *      'addUser' => ['phone', 'parter_id'],
     *      '__default' => 'default'
     * ]
     * // addUser 不加锁，其它按照默认策略加锁
     * [
     *      'addUser' => 'none',
     *      '__default' => 'default'
     * ]
     */
    public function lockers(): array
    {
        return [
            '__default' => 'default'
        ];
    }

    public function index()
    {
        // do nothing
    }

    /**
     * 请求执行前
     * @param null|string $action
     * @return bool|null
     * @throws \Exception
     */
    protected function onRequest(?string $action): ?bool
    {
        if (!$this->execMiddlewares('before', $this, $this->request(), $this->response())) {
            return false;
        }

        return true;
    }

    /**
     * 请求执行后
     * @param null|string $action
     */
    protected function afterAction(?string $action): void
    {
        if ($this->responseData) {
            $this->response()->write(
                is_string($this->responseData)
                    ? $this->responseData : json_encode($this->responseData, JSON_UNESCAPED_UNICODE)
            );
        }

        $this->execMiddlewares('after', $this, $this->request(), $this->response());
    }

    protected function gc()
    {
        $this->execMiddlewares('gc');
    }

    /**
     * 发生异常：记录错误日志，返回错误
     * @param \Throwable $throwable
     */
    protected function onException(\Throwable $throwable): void
    {
        $logger = Container::get(LoggerInterface::class);
        $message = $throwable->getMessage();
        $context = ['trace' => $throwable->getTraceAsString()];

        if ($throwable instanceof CriticalErrorException) {
            $logger->critical($message, $context);
        } elseif ($throwable instanceof EmergencyErrorException) {
            $logger->emergency($message, $context);
        } else {
            $logger->error($message, $context);
        }

        $this->return([], $throwable->getCode() ?: 500, $throwable->getMessage());
    }

    /**
     * 获取请求参数
     * @param null $key
     * @return array|mixed
     */
    protected function params($key = null)
    {
        $params = $this->request()->getRequestParam();
        if (isset($params['data'])) {
            $params = is_string($params['data']) ? json_decode($params['data'], true) : $params['data'];
        }

        return isset($key) ? $params[$key] : $params;
    }

    /**
     * 以 json 格式返回数据
     * @param array $data
     * @param int $status
     * @param string $msg
     */
    protected function return($data = [], int $status = 200, string $msg = ''): void
    {
        $this->responseData = ['status' => $status, 'msg' => $msg, 'data' => $data];
    }
}
