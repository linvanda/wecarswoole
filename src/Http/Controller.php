<?php

namespace WecarSwoole\Http;

use Psr\Log\LoggerInterface;
use EasySwoole\Http\AbstractInterface\Controller as EsController;
use WecarSwoole\Container;
use WecarSwoole\RedisFactory;
use WecarSwoole\Middleware\MiddlewareHelper;
use WecarSwoole\Exceptions\{
    EmergencyErrorException, CriticalErrorException, Exception
};
use WecarSwoole\Http\Middlewares\{LockerMiddleware, RequestRecordMiddleware, RequestTimeMiddleware, ValidateMiddleware};
use Dev\MySQL\Exception\DBException;

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

    /**
     * Controller constructor.
     * @throws \WecarSwoole\Exceptions\ConfigNotFoundException
     */
    public function __construct()
    {
        $this->appendMiddlewares(
            [
                new LockerMiddleware($this),
                new ValidateMiddleware($this),
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
    protected function lockerRules(): array
    {
        return [
            '__default' => 'default'
        ];
    }

    /**
     * 验证器规则定义
     * 格式同 easyswoole 的格式定义，如
     * [
     *      // action
     *      'addUser' => [
     *          // param-name => rules
     *          'user_flag' => ['alpha', 'between' => [10, 20], 'length' => ['arg' => 12, 'msg' => '长度必须为12位']],
     *       ],
     * ]
     * 即：
     *      如果仅提供了字符串型（key是整型），则认为 arg 和 msg 都是空
     *      如果提供了整型下标数组，则认为改数组是 arg，msg 为空
     *      完全形式是如上面 length 的定义
     *
     * @see http://www.easyswoole.com/Manual/3.x/Cn/_book/Components/validate.html
     * @return array
     */
    protected function validateRules(): array
    {
        return [];
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
        if (!$this->execMiddlewares('before', $this->request(), $this->response())) {
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
                is_array($this->responseData)
                    ? json_encode($this->responseData, JSON_UNESCAPED_UNICODE)
                    : (string)$this->responseData
            );
        }

        $this->execMiddlewares('after', $this->request(), $this->response());
    }

    protected function gc()
    {
        $this->execMiddlewares('gc');
        $this->responseData = null;
    }

    /**
     * 发生异常：记录错误日志，返回错误
     * @param \Throwable $throwable
     */
    protected function onException(\Throwable $throwable): void
    {
        $logger = Container::get(LoggerInterface::class);
        $displayMsg = $message = $throwable->getMessage();
        $context = ['trace' => $throwable->getTraceAsString()];
        $retry = 0;

        if ($throwable instanceof DBException) {
            // 数据库错误，需要隐藏详情
            $errFlag = mt_rand(10000, 1000000) . mt_rand(10000, 10000000);
            $displayMsg = "数据库错误，错误标识：{$errFlag}";
            $context['db_err_flag'] = $errFlag;
            $logger->critical($message, $context);
        } elseif ($throwable instanceof CriticalErrorException) {
            $logger->critical($message, $context);
        } elseif ($throwable instanceof EmergencyErrorException) {
            $logger->emergency($message, $context);
        } else {
            $logger->error($message, $context);
        }

        if ($throwable instanceof Exception) {
            $retry = (int)$throwable->shouldRetry();
        }

        $this->return([], $throwable->getCode() ?: 500, $displayMsg, $retry);
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
     * @param int $retry 告诉客户端是否需要重试
     */
    protected function return($data = [], int $status = 200, string $msg = '', int $retry = 0): void
    {
        $this->responseData = [
            'status' => $status,
            'msg' => $msg,
            'data' => $data ? (is_array($data) ? json_encode($data, JSON_UNESCAPED_UNICODE) : (string)$data) : [],
            'retry' => $retry
        ];
    }
}
