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
     * @param Request $request
     * @param Response $response
     */
    public function handle(Request $request, Response $response)
    {

    }
}