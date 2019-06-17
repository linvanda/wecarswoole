<?php

namespace WecarSwoole\Http;

use EasySwoole\EasySwoole\Trigger;
use EasySwoole\Http\AbstractInterface\Controller as EsController;

/**
 * 控制器基类
 * 不要在基类控制器写很多代码，建议抽离成单独的类处理
 * 禁止在基类控制器中写 public 方法，会造成后面难以维护
 * Class Controller
 * @package WecarSwoole\Http
 */
class Controller extends EsController
{
    protected $responseData;

    public function index()
    {
        // do nothing default
    }

    protected function afterAction(?string $action): void
    {
        if ($this->responseData) {
            $this->response()->write(is_string($this->responseData) ? $this->responseData : json_encode($this->responseData, JSON_UNESCAPED_UNICODE));
        }
    }

    /**
     * 发生异常：记录错误日志，返回错误
     * @param \Throwable $throwable
     */
    protected function onException(\Throwable $throwable): void
    {
        Trigger::getInstance()->error($throwable->getMessage(), $throwable->getCode());
        $this->return([], 500, $throwable->getMessage());
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
