<?php

namespace WecarSwoole;

use WecarSwoole\Exceptions\PropertyNotFoundException;
use WecarSwoole\OTA\ExtractProperty;
use WecarSwoole\OTA\IExtractable;
use WecarSwoole\OTA\ObjectToArray;

/**
 * 实体基类
 * 虽然实体一般都需要存储，但设计原则是实体不要知晓存储的任何信息，实体存储相关的事情应全部交给仓储处理
 * Class Entity
 * @package WecarSwoole
 */
class Entity implements IExtractable
{
    use ObjectToArray, ExtractProperty {
        ExtractProperty::getPropertiesValue insteadof ObjectToArray;
        getPropertiesValue as protected;
    }

    /**
     * @param $name
     * @return mixed
     * @throws PropertyNotFoundException
     */
    public function __get($name)
    {
        if (!property_exists($this, $name)) {
            throw new PropertyNotFoundException(get_called_class(), $name);
        }

        return $this->$name;
    }

    /**
     * @param $name
     * @param $value
     * @throws PropertyNotFoundException
     */
    public function __set($name, $value)
    {
        if (!property_exists($this, $name)) {
            throw new PropertyNotFoundException(get_called_class(), $name);
        }

        $this->$name = $value;
    }
}
