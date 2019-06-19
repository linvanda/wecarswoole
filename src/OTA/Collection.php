<?php

namespace WecarSwoole\OTA;

class Collection extends \ArrayIterator
{
    /**
     * Collection constructor.
     * @param array $array
     * @throws \Exception
     */
    public function __construct(array $array = [])
    {
        foreach ($array as $item) {
            if (!$item instanceof IExtractable) {
                throw new \Exception("OTA Collection can accept IExtractable only");
            }
        }

        parent::__construct($array, 0);
    }

    /**
     * @param mixed $value
     * @throws \Exception
     */
    public function append($value)
    {
        if (!$value instanceof IExtractable) {
            throw new \Exception("OTA Collection can accept IExtractable only");
        }

        parent::append($value);
    }

    public function toArray(): array
    {
        $arr = $this->getArrayCopy();
        foreach ($arr as &$obj) {
            $obj = $obj->toArray();
        }

        return $arr;
    }
}
