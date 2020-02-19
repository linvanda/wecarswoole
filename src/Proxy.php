<?php

namespace WecarSwoole;

/**
 * 代理访问对象私有属性/方法
 * 注意：不要滥用该代理，除非你真的有充足的理由
 * 另外，由于使用了闭包绑定，Proxy 对象无法自动被回收（存在循环引用），需要手动调用对象的 destroy() 方法销毁
 * Class Proxy
 * @package WecarSwoole\Middleware
 */
final class Proxy
{
    private $newThis;
    private $funcGet;
    private $funcSet;

    public function __construct($obj = null)
    {
        $this->funcGet = function ($name, ...$arguments) {
            if (method_exists($this, $name)) {
                return $this->{$name}(...$arguments);
            } elseif (property_exists($this, $name)) {
                return $this->{$name};
            }

            return null;
        };

        $this->funcSet = function ($name, $value) {
            if (property_exists($this, $name)) {
                $this->{$name} = $value;
            }
        };

        $this->newThis = $obj;
    }

    public function __call($name, $arguments)
    {
        return $this->funcGet->call($this->newThis, $name, ...$arguments);
    }

    public function __get($name)
    {
        return $this->funcGet->call($this->newThis, $name);
    }

    public function __set($name, $value)
    {
        $this->funcSet->call($this->newThis, $name, $value);
    }

    public function getObject()
    {
        return $this->newThis;
    }

    public function destroy()
    {
        unset($this->funcSet);
        unset($this->funcGet);
        unset($this->newThis);
    }
}
