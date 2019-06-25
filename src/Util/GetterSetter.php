<?php

namespace WecarSwoole\Util;

use WecarSwoole\Exceptions\PropertyNotFoundException;

trait GetterSetter
{
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

        return $this->{$name};
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

        $this->{$name} = $value;
    }
}
