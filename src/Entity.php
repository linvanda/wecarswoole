<?php

namespace WecarSwoole;

use WecarSwoole\Exceptions\InvalidOperationException;
use WecarSwoole\Exceptions\PropertyNotFoundException;

/**
 * 实体基类
 * 虽然实体一般都需要存储，但设计原则是实体不要知晓存储的任何信息，实体存储相关的事情应全部交给仓储处理
 * 一般情况下（针对数据库自增id的情况）实体新建时没有提供id，添加到存储后获取并设置其id
 * 具体是创建时就分配id还是写入存储后再获取并设置id，由具体业务决定
 * Class Entity
 * @package WecarSwoole
 */
class Entity
{
    protected $id;

    /**
     * 默认实现
     * 要求实体对象必须有 id 属性，否则子类必须自己实现该方法
     * @return int|mixed|string
     * @throws PropertyNotFoundException
     */
    public function getId()
    {
        if (!property_exists($this, 'id')) {
            throw new PropertyNotFoundException(get_called_class(), 'id');
        }

        return $this->id;
    }

    /**
     * 默认实现
     * 要求实体对象必须有 id 属性，否则子类必须自己实现该方法
     * @param $id
     * @throws PropertyNotFoundException
     * @throws InvalidOperationException
     */
    public function setId($id)
    {
        if (!property_exists($this, 'id')) {
            throw new PropertyNotFoundException(get_called_class(), 'id');
        }

        if (!is_null($this->id)) {
            throw new InvalidOperationException("不可修改实体的id值");
        }

        $this->id = $id;
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