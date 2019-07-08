<?php

namespace WecarSwoole\Middleware;

use WecarSwoole\Proxy;

/**
 * 该中间件提供了对 relObject 私有成员的代理访问能力，针对一些需要获取相关对象私有成员的（对象临时绑定）
 * 中间件的所有 handler 方法的第一个参数都是 Next 对象，每个中间件方法最后都要调用 $next(...)，否则后续中间件
 * 不会被调用。
 * Class Middleware
 */
class Middleware
{
    /**
     * 中间件中可以通过 proxy 调用 $relObject 的方法和属性，包括私有的
     * @var Proxy
     */
    protected $proxy;

    public function __construct($relObject = null)
    {
        $this->proxy = new Proxy($relObject);
    }
}
