<?php

namespace WecarSwoole\Middleware;

use EasySwoole\Http\Request;

/**
 * 路由中间件接口
 * Interface IRouteMiddleware
 * @package WecarSwoole\Middleware
 */
interface IRouteMiddleware
{
    /**
     * 抛出异常则终止请求执行
     * @param Request $request
     */
    public function handle(Request $request);
}