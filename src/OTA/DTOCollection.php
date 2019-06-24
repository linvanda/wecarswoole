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
    /**
     * 通过传入二维数组自动创建 DTO 对象
     * DTOCollection constructor.
     * @param string $classType DTO 具体的类型
     * @param array $array 可构建 DTO 对象的二维数组
     * @throws \Exception
     */
    public function __construct(string $classType, array $array = [])
    {
        $class = new \ReflectionClass($classType);
        if (!$class->isSubclassOf(DTO::class)) {
            throw new \Exception("$classType is not sub class of DTO");
        }

        $objArr = [];
        foreach ($array as $row) {
            $objArr[] = $class->newInstance($row);
        }

        parent::__construct($objArr);
    }
}
