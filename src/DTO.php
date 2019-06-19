<?php

namespace WecarSwoole;

use WecarSwoole\ATO\ArrayToObject;
use WecarSwoole\ATO\IArrayBuildable;
use WecarSwoole\OTA\IExtractable;
use WecarSwoole\OTA\ObjectToArray;

/**
 * DTO 基类
 * DTO 属性可以设置字段注解(field)和值注解(mapping)
 *
 * Class DTO
 * @package WecarSwoole
 */
class DTO implements IExtractable, IArrayBuildable
{
    use ObjectToArray, ArrayToObject;

    public function __construct(array $data = [])
    {
        $this->buildFromArray($data);
    }
}
