<?php

namespace Test\Address;

use WecarSwoole\Entity;

class CountryEntity extends Entity
{
    /**
     * @field
     * @var string
     */
    protected $name;

    public function __construct(string $name)
    {
        $this->name = $name;
    }

    public function __get($name)
    {
        return $this->$name;
    }
}