<?php

namespace WecarSwoole\Http;

use FastRoute\RouteCollector;
use WecarSwoole\Http\Middlewares\ApiAuthMiddleware;

/**
 * api 路由基类，该基类需走 api 鉴权
 * Class ApiRoute
 * @package WecarSwoole\Http
 */
abstract class ApiRoute extends Route
{
    public function __construct(RouteCollector $collector)
    {
        $this->appendMiddlewares(ApiAuthMiddleware::class);

        parent::__construct($collector);
    }
}
