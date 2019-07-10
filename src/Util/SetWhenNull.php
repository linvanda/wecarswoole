<?php

namespace WecarSwoole\Util;

trait SetWhenNull
{
    public function setWhenNull(array $propertiesVal)
    {
        foreach ($propertiesVal as $propertyName => $value) {
            if (property_exists($this, $propertyName) && $this->{$propertyName} === null) {
                $this->{$propertyName} = $value;
            }
        }
    }
}
