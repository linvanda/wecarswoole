<?php

namespace WecarSwoole\OTA;

use EasySwoole\Utility\Str;

/**
 * 对象转成数组
 * Trait ObjectToArray
 * @package WecarSwoole\OTA
 */
trait ObjectToArray
{
    /**
     * @param bool $camelToSnake 是否将驼峰风格属性名转换成下划线风格
     * @param bool $withNull 是否包含 null 属性
     * @param bool $zip 是否将多维压成一维，如
     *              ['age'=>12,'address'=>['city'=>'深圳','area'=>'福田']] 会变成 ['age'=>12,'city'=>'深圳','area'=>'福田']
     * @return array
     */
    public function toArray(bool $camelToSnake = true, bool $withNull = true, bool $zip = false): array
    {
        $data = $this->getPropertiesValue($withNull);

        if (!$camelToSnake && !$zip) {
            return $data;
        }

        // 多维压成一维
        if ($zip) {
            $data = self::zip($data);
        }

        // 驼峰转 snake
        if ($camelToSnake) {
            $data = self::camelToSnake($data);
        }

        return $data;
    }

    protected function getPropertiesValue(bool $withNull): array
    {
        $values = get_object_vars($this);
        if (!$withNull) {
            $values = array_filter($values, function ($value) {
                return !is_null($value);
            });
        }

        return $values;
    }

    protected static function zip(array $data): array
    {
        $result = [];
        foreach ($data as $k => $val) {
            if (is_array($val)) {
                $result = array_merge($result, self::zip($val));
            } else {
                $result[$k] = $val;
            }
        }

        return $result;
    }

    protected static function camelToSnake(array $data): array
    {
        foreach ($data as $k => &$val) {
            if ($k !== Str::snake($k)) {
                $data[Str::snake($k)] = $val;
                unset($data[$k]);
            }

            if (is_array($val)) {
                $val = self::camelToSnake($val);
            }
        }

        return $data;
    }
}
