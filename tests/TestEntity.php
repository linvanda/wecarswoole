<?php

namespace Test;

use WecarSwoole\Entity;
use Test\Address\AddressEntity;

class TestEntity extends Entity
{
    /**
     * @var int
     */
    protected $id;
    /**
     * @var string
     */
    protected $name;
    /**
     * @var int
     */
    protected $age;
    /**
     * @var string
     */
    protected $sex;

    /**
     * @field
     */
    protected $birthDay;

    /**
     * @var \Test\Address\AddressEntity
     */
    protected $address;

    /**
     * @var [\Test\Address\AddressEntity]
     */
    protected $shouHuoAddresses;

    public function __construct(string $name, string $sex, string $birthDay)
    {
        $this->name = $name;
        $this->sex = $sex;
        $this->birthDay = $birthDay;
//        $this->address = new AddressEntity('深圳', '南山');
//        $this->shouHuoAddresses = [
//            new AddressEntity('广州', '天河'),
//            new AddressEntity('广西', '桂林'),
//        ];
    }

    public function name()
    {
        return $this->name;
    }

    public function sex()
    {
        return $this->sex;
    }
}