<?php

namespace WecarSwoole\Http\Middlewares;

use EasySwoole\Http\Request;
use EasySwoole\Http\Response;
use WecarSwoole\Middleware\Next;

/**
 * api 鉴权中间件
 * Class ApiAuthMiddleware
 */
class ApiAuthMiddleware implements IRouteMiddleware
{
    /**
     * @param Request $request
     * @param Response $response
     * @return mixed
     * @throws \Exception
     */
    public function handle(Next $next, Request $request, Response $response)
    {
        // TODO
        return $next($request, $response);
    }
}
