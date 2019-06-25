<?php

namespace WecarSwoole\OTA;
use WecarSwoole\DTO;

/**
 * DTO 集合
 * Class DTOCollection
 * @package WecarSwoole\OTA
 */
class DTOCollection extends Collection
{
    private $class;

    /**
     * 通过传入二维数组自动创建 DTO 对象
     * DTOCollection constructor.
     * @param string $class DTO子类
     * @param array $array 可构建 DTO 对象的二维数组
     * @throws \Exception
     */
    public function __construct(string $class, array $array = [])
    {
        $this->class = $class;
        $cls = new \ReflectionClass($class);
        if (!$cls->isSubclassOf(DTO::class)) {
            throw new \Exception("$class is not sub class of DTO");
        }

        $objArr = [];
        foreach ($array as $row) {
            $objArr[] = $cls->newInstance($row);
        }

        parent::__construct($objArr);
    }

    /**
     * @param mixed $value
     * @throws \Exception
     */
    public function append($value)
    {
        if (is_array($value)) {
            $value = (new \ReflectionClass($this->class))->newInstance($value);
        }

        parent::append($value);
    }
}
