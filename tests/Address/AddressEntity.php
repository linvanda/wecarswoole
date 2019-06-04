<?php

namespace Test\Address;

use WecarSwoole\Entity;

class AddressEntity extends Entity
{
    /**
     * @field
     * @var string
     */
    protected $city;
    /**
     * @field
     * @var string
     */
    protected $area;

    /**
     * @field
     * @var \Test\Address\CountryEntity
     */
    protected $country;

    public function __construct(string $city, string $area)
    {
        $this->city = $city;
        $this->area = $area;
        $this->country = new CountryEntity('中国');
    }

    public function __get($name)
    {
        return $this->$name;
    }
}