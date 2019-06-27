<?php

namespace WecarSwoole\ATO;

use EasySwoole\Utility\Str;
use WecarSwoole\Util\AnnotationAnalyser;

/**
 * 根据数组数据构建对象属性值
 * 允许的属性注解：
 *          field 字段别名，如 @field gender，表示使用数组中 gender 字段的值作为此属性值
 *          mapping 值映射，如 @mapping 1=>女,2=>男 表示数组中的 1 将被映射成属性值"男"。仅支持基本类型(number/string/bool)映射
 *          var 属性类型，需写类全名（包括命名空间）。当类型是 WecarSwoole/ATO/IArrayBuildable 时，会尝试根据数组自动构建此属性
 * 注意：不要有循环依赖，否则自动构建会出现死循环
 * Trait ArrayToObject
 * @package WecarSwoole\ATO
 */
trait ArrayToObject
{
    /**
     * @param array $data
     * @param bool $strict 当 true 时，表示除非 $data 中指定了相关属性的值，否则不会进行深层对象解析
     * @param bool $mapping 是否解析 mapping 注解
     */
    public function buildFromArray(array $data, bool $strict = true, bool $mapping = true)
    {
        foreach ($this->map($data, $strict, $mapping) as $field => $value) {
            if (property_exists($this, $field)) {
                $this->$field = $value;
            }
        }
    }

    protected function map(array $data, bool $strict, bool $mapping = true): array
    {
        return $this->valueMapping($this->fieldMapping($data), $strict, $mapping);
    }

    /**
     * 处理值映射：mapping 注解、var 注解
     * @param array $data
     * @param bool $mapping 是否解析 mapping 注解
     * @return array
     */
    protected function valueMapping(array $data, bool $strict, bool $mapping = true): array
    {
        $anno = AnnotationAnalyser::getPropertyAnnotations(get_called_class(), ['mapping', 'var']);

        foreach ($anno as $propertyName => $annoInfo) {
            // mapping 注解
            if ($mapping && isset($annoInfo['mapping'])) {
                self::valueMappingByMapping(
                    $propertyName,
                    AnnotationAnalyser::mappingToArray($annoInfo['mapping']),
                    $data
                );
            }

            // var 注解
            if (isset($annoInfo['var'])) {
                self::valueMappingByVar($propertyName, $annoInfo['var'], $data, $strict);
            }
        }

        return $data;
    }

    /**
     * 处理字段映射：下划线转驼峰、field 注解
     * @param array $data
     * @return array
     */
    protected function fieldMapping(array $data): array
    {
        $data = $this->snakeToCamel($data);

        // 解析 field 注解
        $anno = AnnotationAnalyser::getPropertyAnnotations(get_called_class(), ['field']);
        foreach ($anno as $propertyName => $annoInfo) {
            if (!array_key_exists($propertyName, $data) && array_key_exists($annoInfo['field'], $data)) {
                $data[$propertyName] = $data[$annoInfo['field']];
            }
        }

        return $data;
    }

    private static function valueMappingByMapping(string $prop, array $mapping, array &$data)
    {
        if (array_key_exists($data[$prop], $mapping)) {
            $data[$prop] = $mapping[$data[$prop]];
        }
    }

    private static function valueMappingByVar(string $propertyName, string $type, &$data, bool $strict)
    {
        $type = trim($type);

        if (!$type || self::isScalar($type)) {
            return;
        }

        if ($strict && !array_key_exists($propertyName, $data)) {
            return;
        }

        if (!class_exists($type)) {
            return;
        }

        $reflectionClass = new \ReflectionClass($type);
        if (!$reflectionClass->isSubclassOf(IArrayBuildable::class)) {
            return;
        }

        // 递归解析
        $object = $reflectionClass->newInstanceWithoutConstructor();
        $object->buildFromArray($data[$propertyName] ?? $data, $strict);
        $data[$propertyName] = $object;
    }

    private static function isScalar(string $typeName)
    {
        if (in_array(
            strtolower(trim($typeName, ' \\')),
            ['int', 'integer', 'float', 'double', 'string', 'bool', 'boolean', 'array']
        )) {
            return true;
        }

        return false;
    }

    /**
     * 传入的 snake 格式字段如果没有对应的属性，则试图找对应的 camel 格式的属性
     * @param array $data
     * @return array
     */
    private function snakeToCamel(array $data): array
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
