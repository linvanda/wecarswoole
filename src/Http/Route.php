<?php

namespace WecarSwoole\Http;

use WecarSwoole\Middleware\MiddlewareHelper;
use FastRoute\RouteCollector;
use EasySwoole\Http\Request;
use EasySwoole\Http\Response;

/**
 * 路由基类
 */
abstract class Route
{
    use MiddlewareHelper;

    protected $routeCollector;
    // 路由前缀
    protected $prefix;

    public function __construct(RouteCollector $collector)
    {
        $this->routeCollector = $collector;
    }

    public function get(string $routePattern, string $handler)
    {
        $this->addRoute(['GET'], $routePattern, $handler);
    }

    public function post(string $routePattern, string $handler)
    {
        $this->addRoute(['POST'], $routePattern, $handler);
    }

    public function put(string $routePattern, string $handler)
    {
        $this->addRoute(['PUT'], $routePattern, $handler);
    }

    public function delete(string $routePattern, string $handler)
    {
        $this->addRoute(['DELETE'], $routePattern, $handler);
    }

    public function addRoute(array $methods, string  $routePattern, string $handler)
    {
        $this->routeCollector->addRoute(
            $methods,
            $this->prefix ? implode('/', [rtrim($this->prefix, '/'), ltrim($routePattern, '/')]) : $routePattern,
            function (Request $request, Response $response) use ($handler) {
                // 执行中间件
                try {
                    $this->execMiddlewares('handle', $request, $response);
                } catch (\Exception $e) {
                    $response->write(json_encode(['info' => $e->getMessage(), 'status' => $e->getCode() ?: 500]));
                    return false;
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
