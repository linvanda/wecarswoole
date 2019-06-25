<?php

namespace WecarSwoole\Util;

/**
 * 自动属性赋值，根据方法传参自动赋值给同名属性
 * Trait AutoProperty
 * @package WecarSwoole\Util
 */
trait AutoProperty
{
    protected function setProperties(array $args, string $method = '__construct')
    {
        $params = (new \ReflectionMethod($this, $method))->getParameters();
        $index = 0;
        foreach ($params as $param) {
            if (property_exists($this, $param->getName()) && isset($args[$index])) {
                $this->{$param->getName()} = $args[$index];
                $index++;
            }
        }
    }
}
