<?php

namespace WecarSwoole\Middleware;

use EasySwoole\Http\Request;
use EasySwoole\Http\Response;
use WecarSwoole\Exceptions\AuthException;
use WecarSwoole\Signer\WecarSigner;
use WecarSwoole\Util\Config;

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
     * @throws \Exception
     */
    public function handle(Request $request, Response $response)
    {
        // TODO
    }
}