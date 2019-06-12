<?php

namespace WecarSwoole\Middleware;

use EasySwoole\Http\Request;
use EasySwoole\Http\Response;

/**
 * api 鉴权中间件
 * Class ApiAuthMiddleware
 * @package WecarSwoole\Middleware
 */
class ApiAuthMiddleware implements IRouteMiddleware
{
    /**
     * 注意：路由中间件不能修改 Request 对象信息（即使可以修改），因为一个 Controller 可能会对应多个路由，则如果多个路由修改不一致，Controller 就会出错
     * 抛出异常则终止请求执行
     * @param Request $request
     * @param Response $response
     */
    public function handle(Request $request, Response $response)
    {

    }
}