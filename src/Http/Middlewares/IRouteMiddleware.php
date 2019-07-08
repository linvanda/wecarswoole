<?php

namespace WecarSwoole\Http\Middlewares;

use EasySwoole\Http\Request;
use EasySwoole\Http\Response;
use WecarSwoole\Middleware\Next;

/**
 * 路由中间件接口
 * Interface IRouteMiddleware
 */
interface IRouteMiddleware
{
    /**
     * 抛出异常或者返回 Response 对象则终止请求执行
     * @param Next $next
     * @param Request $request
     * @param Response $response
     * @return mixed
     */
    public function handle(Next $next, Request $request, Response $response);
}
