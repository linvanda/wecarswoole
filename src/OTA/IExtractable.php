<?php

namespace WecarSwoole\OTA;

/**
 * 可抽取成数组的对象接口
 * Interface IExtractable
 * @package WecarSwoole\OTA
 */
interface IExtractable
{
    /**
     * 对象转数组
     * @param bool $camelToSnake 是否将驼峰风格属性名转换成下划线风格
     * @param bool $withNull 是否包含 null 属性
     * @param bool $zip 是否将多维压成一维，如
     *              ['age'=>12,'address'=>['city'=>'深圳','area'=>'福田']] 会变成 ['age'=>12,'city'=>'深圳','area'=>'福田']
     * @param array $exFields 需要排除的字段
     * @return array
     */
    public function toArray(
        bool $camelToSnake = true,
        bool $withNull = true,
        bool $zip = false,
        array $exFields = []
    ): array;
}
