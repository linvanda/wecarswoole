<?php

namespace WecarSwoole;

/**
 * 代理访问对象私有属性/方法
 * 注意：不要滥用该代理，除非你真的有充足的理由
 * Class Proxy
 * @package WecarSwoole\Middleware
 */
final class Proxy
{
    private $newThis;
    private $func;

    public function __construct($obj = null)
    {
        $this->func = function ($name, ...$arguments) {
            if (method_exists($this, $name)) {
                return $this->{$name}(...$arguments);
            } elseif (property_exists($this, $name)) {
                return $this->{$name};
            }

            return null;
        };

        $this->newThis = $obj;
    }

    public function __call($name, $arguments)
    {
        return $this->func->call($this->newThis, $name, ...$arguments);
    }

    public function __get($name)
    {
        return $this->func->call($this->newThis, $name);
    }

    public function setObject($obj)
    {
        $this->newThis = $obj;
    }
}
