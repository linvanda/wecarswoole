<?php

namespace WecarSwoole\OTA;

/**
 * 抽取属性值作为数组返回
 * 如果需要递归抽取，被抽取的类需要实现 IExtractable 接口
 * Trait ExtractProperty
 * @package WecarSwoole\OTA
 */
trait ExtractProperty
{
    public function getPropertiesValue(bool $withNull): array
    {
        $values = get_object_vars($this);
        if (!$withNull) {
            $values = array_filter($values, function ($value) {
                return !is_null($value);
            });
        }

        foreach ($values as $propName => &$propValue) {
            if (!is_scalar($propValue) && !is_array($propValue) && !$propValue instanceof IExtractable) {
                unset($values[$propName]);
                continue;
            }

            if (is_bool($propValue)) {
                $propValue = intval($propValue);
            } elseif ($propValue instanceof IExtractable) {
                $propValue = $propValue->toArray();
            }
        }

        return $values;
    }
}
