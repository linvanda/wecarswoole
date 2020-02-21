<?php

namespace WecarSwoole\Redis;

class TmpRedisProxyCreator
{
    /**
     * 根据父类生成临时子类
     * 目前仅反射 Public 方法
     * @param string $parentClassName
     */
    public static function create(string $className)
    {
        if (class_exists($className)) {
            return;
        }

        $className = array_filter(explode('\\', $className));
        $shortName = array_pop($className);
        $namespace = implode('\\', $className);

        $reflection = new \ReflectionClass(\Redis::class);
        $class = "namespace $namespace {\nclass $shortName extends \WecarSwoole\Redis\RedisProxy {";
        $class .= self::createMethods($reflection);
        $class .= "\n}\n}";

        eval($class);
    }

    private static function createMethods(\ReflectionClass $reflection): string
    {
        $methods = "\n";
        $reflectMethods = $reflection->getMethods(\ReflectionMethod::IS_PUBLIC);

        foreach ($reflectMethods as $method) {
            // 剔除 __construct、__destruct
            if ($method->isConstructor() ||
                $method->isDestructor() ||
                strpos($method->getName(), '_') === 0
            ) {
                continue;
            }

            $methods .= "public " . ($method->isStatic() ? 'static ' : '') . 'function ' . $method->getName() . '(';
            $methods .= self::createMethodArgs($method) . ") {";
            $methods .= self::createMethodBody($method) . "}\n";
        }

        return $methods;
    }

    private static function createMethodArgs(\ReflectionMethod $method): string
    {
        $args = '';
        foreach ($method->getParameters() as $parameter) {
            if (version_compare(PHP_VERSION, '7.1', '>=')) {
                $args .= $parameter->allowsNull() && $parameter->getType() ? '?' : '';
            }

            if ($parameter->getType()) {
                $args .= $parameter->getType()->getName();
            }

            $args .= ' ' . ($parameter->isPassedByReference() ? '&' : '');
            $args .= self::isVariadic($parameter) ? '...' : '';

            $args .= '$' . $parameter->getName();

            if (self::hasDefaultValue($parameter)) {
                $args .= ' = ' . var_export(self::getDefaultValue($parameter), true);
            }

            $args .= ', ';
        }

        return rtrim($args, ', ');
    }

    private static function createMethodBody(\ReflectionMethod $method): string
    {
        return 'return $this->callMethod("' . $method->getName() . '", ...func_get_args());';
    }

    private static function hasDefaultValue(\ReflectionParameter $parameter)
    {
        if (self::isVariadic($parameter)) {
            return false;
        }

        if ($parameter->isDefaultValueAvailable()) {
            return true;
        }

        return $parameter->isOptional() || $parameter->allowsNull();
    }

    private static function isVariadic(\ReflectionParameter $parameter): bool
    {
        return PHP_VERSION_ID >= 50600 && $parameter->isVariadic();
    }

    private static function getDefaultValue(\ReflectionParameter $parameter)
    {
        if (!$parameter->isDefaultValueAvailable()) {
            return null;
        }

        return $parameter->getDefaultValue();
    }
}
