<?php

namespace WecarSwoole\Client\Http\Middleware;

use WecarSwoole\Client\Config\HttpConfig;
use WecarSwoole\Client\Contract\IHttpRequestBean;
use Psr\Http\Message\ResponseInterface;

/**
 * 请求装饰器
 * Interface IRequestMiddleware
 * @package WecarSwoole\Client\Http\Hook
 */
interface IRequestMiddleware
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
     * 注意：由于目前使用的第三方插件有 bug，请在 hook 中不要执行 $response->getBody()->read($size)，否则即使再调用
     * $response->getBody()->rewind() 也拿不到 body 数据了（插件貌似并没有实现 rewind() 方法）。
     * 请用 (string)$response->getBody() 的方式拿请求数据
     * @param HttpConfig $config
     * @param IHttpRequestBean $request
     * @param ResponseInterface $response
     * @return mixed
     */
    public function after(HttpConfig $config, IHttpRequestBean $request, ResponseInterface $response);
}
