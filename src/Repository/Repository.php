<?php

namespace WecarSwoole\Repository;

use WecarSwoole\Proxy;

/**
 * 仓储基类
 * Class Repository
 * @package WecarSwoole\Repository
 */
class Repository
{
    /**
     * 生成一个 $className 的空对象，且外面可以设置该对象的私有属性
     * 该方法用来从数据库等存储中恢复实体对象
     * @param string $className
     * @return mixed
     * @throws \ReflectionException
     */
    public function generateEmptyObject(string $className)
    {
        return new Proxy((new \ReflectionClass($className))->newInstanceWithoutConstructor());
    }
}
