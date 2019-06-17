<?php

namespace WecarSwoole;

use EasySwoole\Utility\Str;
use WecarSwoole\OTA\ExtractProperty;
use WecarSwoole\OTA\IExtractable;
use WecarSwoole\OTA\ObjectToArray;
use WecarSwoole\Util\AnnotationAnalyser;

/**
 * DTO 基类
 * DTO 属性可以设置字段注解(field)和值注解(mapping)
 *
 * Class DTO
 * @package WecarSwoole
 */
class DTO implements IExtractable
{
    use ObjectToArray, ExtractProperty {
        ExtractProperty::getPropertiesValue insteadof ObjectToArray;
        getPropertiesValue as protected;
    }

    public function __construct(array $data = [])
    {
        foreach ($this->valueMapping($this->fieldMapping($data)) as $field => $value) {
            if (property_exists($this, $field)) {
                $this->$field = $value;
            }
        }
    }

    /**
     * 解析属性注解，处理值映射
     * @param array $data
     * @return array
     */
    protected function valueMapping(array $data): array
    {
        $anno = AnnotationAnalyser::getPropertyAnnotations(get_called_class(), ['mapping']);
        foreach ($anno as $propertyName => $annoInfo) {
            $mapping = AnnotationAnalyser::mappingToArray($annoInfo['mapping']);

            if (array_key_exists($data[$propertyName], $mapping)) {
                $data[$propertyName] = $mapping[$data[$propertyName]];
            }
        }

        return $data;
    }

    /**
     * 解析属性注解，处理字段映射
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

    /**
     * 传入的 snake 格式字段如果没有对应的属性，则试图找对应的 camel 格式的属性
     * @param array $data
     * @return array
     */
    protected function snakeToCamel(array $data): array
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
