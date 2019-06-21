<?php

namespace WecarSwoole\Http\Middlewares;

use EasySwoole\Http\Request;
use EasySwoole\Http\Response;

/**
 * api 鉴权中间件
 * Class ApiAuthMiddleware
 */
class ApiAuthMiddleware implements IRouteMiddleware
{
    /**
     * @param Request $request
     * @param Response $response
     * @throws \Exception
     */
    public function handle(Request $request, Response $response)
    {
        // TODO
    }
}
