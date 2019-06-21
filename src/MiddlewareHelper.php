<?php

namespace WecarSwoole;

/**
 * 中间件操作助手
 * Trait MiddlewareHelper
 * @package WecarSwoole
 */
trait MiddlewareHelper
{
    private $middlewares = [];

    /**
     * 设置中间件列表，该方法会重置之前设置过的值
     * @param array $middlewares
     */
    public function setMiddlewares(array $middlewares)
    {
        $this->middlewares = $this->buildMiddlewares($middlewares);
    }

    /**
     * 追加中间件
     * @param string|array $middlewares
     */
    public function appendMiddlewares($middlewares)
    {
        $this->middlewares = array_merge($this->middlewares, $this->buildMiddlewares($middlewares));
    }

    /**
     * 返回中间件类名数组
     * @return array
     */
    public function getMiddlewares()
    {
        return $this->middlewares;
    }

    /**
     * 执行中间件
     * 如果执行返回 false 则停止后续执行
     * @param string $method
     * @param array ...$params
     * @return bool
     */
    protected function execMiddlewares(string $method, ...$params): bool
    {
        foreach ($this->getMiddlewares() as $middleware) {
            if (!method_exists($middleware, $method)) {
                continue;
            }

            if (call_user_func([$middleware, $method], ...$params) === false) {
                return false;
            }
        }

        return true;
    }

    private function buildMiddlewares($middlewares): array
    {
        if (!is_array($middlewares)) {
            return is_string($middlewares) ? [new $middlewares()] : [$middlewares];
        }

        return array_map(function ($middleware) {
            return is_string($middleware) ? new $middleware() : $middleware;
        }, $middlewares);
    }
}
