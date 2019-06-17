<?php

namespace WecarSwoole\DTO;

use EasySwoole\Spl\SplBean;
use EasySwoole\Utility\Str;

/**
 * DTO 基类
 *
 * Class DTO
 * @package WecarSwoole
 */
class DTO extends SplBean
{
    public function __construct(array $data = null)
    {
        parent::__construct($this->valueMapping($this->snackToCamel($data)), false);
    }

    /**
     * 解析属性注解，处理值映射
     * @return array
     */
    protected function valueMapping(array $data): array
    {
        foreach ($valueMapping as $key => $map) {
            if (array_key_exists($key, $data) && array_key_exists($data[$key], $map)) {
                $data[$key] = $map[$data[$key]];
            }
        }

        return $data;
    }


    protected function fieldMapping(array $data): array
    {

    }

    /**
     * 传入的 snake 格式字段如果没有对应的属性，则试图找对应的 camel 格式的属性
     * @param array $data
     * @return array
     */
    protected function snackToCamel(array $data): array
    {
        foreach ($data as $field => $val) {
            if (property_exists($this, $field)) {
                continue;
            }

            $camelField = Str::camel($field);
            if (property_exists($this, $camelField)) {
                $data[$camelField] = $val;
                unset($data[$field]);
            }
        }

        return $data;
    }
}
