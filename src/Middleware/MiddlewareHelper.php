<?php

namespace WecarSwoole\Middleware;

/**
 * 中间件操作助手
 * Trait MiddlewareHelper
 * @package WecarSwoole\Middleware
 */
trait MiddlewareHelper
{
    private $middleware = [];
    private $middlewareObjects = [];

    /**
     * 设置中间件列表，该方法会重置之前设置过的值
     * @param array $middlewareNameList
     */
    public function setMiddleware(array $middlewareNameList)
    {
        $this->middleware = $middlewareNameList;
    }

    /**
     * 返回中间件类名数组
     * @return array
     */
    public function getMiddleware()
    {
        return $this->middleware;
    }

    /**
     * 追加中间件
     * @param string|array $middlewareName
     */
    public function appendMiddleware($middlewareName)
    {
        if (is_string($middlewareName)) {
            $this->middleware[] = $middlewareName;
        } else {
            $this->middleware = array_merge($this->middleware, $middlewareName);
        }
    }

    /**
     * 删除中间件
     * @param string $middlewareName
     */
    public function removeMiddleware(string $middlewareName)
    {
        $index = array_search($middlewareName, $this->middleware);
        if ($index !== false) {
            unset($this->middleware[$index]);
        }
    }

    /**
     * @param array ...$args
     * @return array
     * @throws \Exception
     */
    public function getMiddlewareObjects(...$args)
    {
        $needBuilds = array_diff($this->middleware, array_keys($this->middlewareObjects));
        if ($needBuilds) {
            $newObjects = self::buildMiddlewareStatic($this->middleware, ...$args);
            $this->middlewareObjects = array_merge($this->middlewareObjects, $newObjects);
        }

        return $this->middlewareObjects;
    }

    /**
     * @param array $middlewares
     * @param array ...$args
     * @return array
     * @throws \Exception
     */
    public static function buildMiddlewareStatic(array $middlewares, ...$args)
    {
        $result = [];
        foreach ($middlewares as $middleware) {
            if (!class_exists($middleware)) {
                throw new \Exception("中间件类不存在：{$middleware}");
            }

            $midw = new $middleware(...$args);

            $result[$middleware] = $midw;
        }

        return $result;
    }
}
