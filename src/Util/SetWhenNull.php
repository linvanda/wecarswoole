<?php

namespace WecarSwoole\Util;

trait SetWhenNull
{
    public function setWhenNull(array $propertiesVal, int $whoIsNull)
    {
        $nulls = [null];

        if ($whoIsNull & EMPTY_ARRAY_AS_NULL === EMPTY_ARRAY_AS_NULL) {
            $nulls[] = [];
        }

        if ($whoIsNull & EMPTY_STR_AS_NULL === EMPTY_STR_AS_NULL) {
            $nulls[] = '';
        }

        if ($whoIsNull & ZERO_AS_NULL === ZERO_AS_NULL) {
            $nulls[] = 0;
        }

        foreach ($propertiesVal as $propertyName => $value) {
            if (property_exists($this, $propertyName) && in_array($this->{$propertyName}, $nulls, true)) {
                $this->{$propertyName} = $value;
            }
        }
    }
}
