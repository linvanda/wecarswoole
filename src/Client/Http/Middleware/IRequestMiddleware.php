<?php

namespace WecarSwoole\Client\Http\Middleware;

use WecarSwoole\Client\Config\HttpConfig;
use WecarSwoole\Client\Contract\IHttpRequestBean;
use Psr\Http\Message\ResponseInterface;
use WecarSwoole\Middleware\Next;

/**
 * 请求中间件
 * Interface IRequestMiddleware
 * @package WecarSwoole\Client\Http\Hook
 */
interface IRequestMiddleware
{
    /**
     * 请求前执行的钩子函数，在请求解析器解析请求参数后执行
     * 如果返回的是 Psr\Http\Message\ResponseInterface，则以此 Response 作为返回（可用此模拟请求）
     * @param Next $next
     * @param HttpConfig $config
     * @param IHttpRequestBean $request
     * @return mixed
     */
    public function before(Next $next, HttpConfig $config, IHttpRequestBean $request);

    /**
     * 请求后执行的钩子函数，在响应解析器解析响应参数前执行
     * 注意：由于目前使用的第三方插件有 bug，请在 hook 中不要执行 $response->getBody()->read($size)，否则即使再调用
     * $response->getBody()->rewind() 也拿不到 body 数据了（插件貌似并没有实现 rewind() 方法）。
     * 请用 (string)$response->getBody() 的方式拿请求数据
     * @param Next $next
     * @param HttpConfig $config
     * @param IHttpRequestBean $request
     * @param ResponseInterface $response
     * @return mixed
     */
    public function after(Next $next, HttpConfig $config, IHttpRequestBean $request, ResponseInterface $response);
}
