<?php

namespace WecarSwoole\Http;

use WecarSwoole\Middleware\MiddlewareHelper;
use WecarSwoole\Middleware\IRouteMiddleware;
use FastRoute\RouteCollector;
use EasySwoole\Http\Request;
use EasySwoole\Http\Response;

/**
 * 路由基类
 * 中间件的注册方式：
 *  1. 类全局注册：在子类的$middleware数组中配置中间件类名，则此类中定义的所有路由共用该中间件
 *  2. 路由注册：在设置路由时于参数中指定中间件类名，则仅用于该路由
 * 中间件执行顺序取决于注册顺序，类全局的先于特定路由的
 * Interface IRoute
 */
abstract class Route
{
    use MiddlewareHelper;

    protected $routeCollector;

    public function __construct(RouteCollector $collector)
    {
        $this->routeCollector = $collector;
    }

    public function get(string $routePattern, string $handler, array $middleware = [])
    {
        $this->addRoute(['GET'], $routePattern, $handler, $middleware);
    }

    public function post(string $routePattern, string $handler, array $middleware = [])
    {
        $this->addRoute(['POST'], $routePattern, $handler, $middleware);
    }

    public function put(string $routePattern, string $handler, array $middleware = [])
    {
        $this->addRoute(['PUT'], $routePattern, $handler, $middleware);
    }

    public function delete(string $routePattern, string $handler, array $middleware = [])
    {
        $this->addRoute(['DELETE'], $routePattern, $handler, $middleware);
    }

    public function addRoute(array $methods, string  $routePattern, string $handler, array $middleware = [])
    {
        $this->routeCollector->addRoute(
            $methods,
            $routePattern,
            function (Request $request, Response $response) use ($handler, $middleware) {
                // 执行中间件
                $middlewares = self::buildMiddlewareStatic(array_merge($this->getMiddleware(), $middleware));
                foreach ($middlewares as $midw) {
                    if (!($midw instanceof IRouteMiddleware)) {
                        continue;
                    }

                    try {
                        $result = $midw->handle($request, $response);
                        if ($result instanceof Response) {
                            return false;
                        }
                    } catch (\Exception $e) {
                        $response->write(json_encode(['info' => $e->getMessage(), 'status' => $e->getCode() ?: 500]));
                        return false;
                    }
                }

                return $handler;
            }
        );
    }

    /**
     * 子类在此处添加路由
     * @return mixed
     */
    abstract public function map();
}
