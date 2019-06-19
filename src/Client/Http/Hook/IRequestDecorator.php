<?php

namespace WecarSwoole\Client\Http\Hook;

use WecarSwoole\Client\Config\HttpConfig;
use WecarSwoole\Client\Contract\IHttpRequestBean;
use Psr\Http\Message\ResponseInterface;

/**
 * 请求装饰器
 * Interface IRequestDecorator
 * @package WecarSwoole\Client\Http\Hook
 */
interface IRequestDecorator
{
    /**
     * 请求前执行的钩子函数，在请求解析器解析请求参数后执行
     * @param HttpConfig $config
     * @param IHttpRequestBean $request
     * @return bool 返回 false 或者抛出异常则忽略后续钩子且中断请求执行
     */
    public function before(HttpConfig $config, IHttpRequestBean $request): bool;

    /**
     * 请求后执行的钩子函数，在响应解析器解析响应参数前执行
     * @param HttpConfig $config
     * @param IHttpRequestBean $request
     * @param ResponseInterface $response
     * @return mixed
     */
    public function after(HttpConfig $config, IHttpRequestBean $request, ResponseInterface $response);
}
