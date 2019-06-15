<?php

namespace WecarSwoole;

use EasySwoole\Spl\SplBean;
use EasySwoole\Utility\Str;

/**
 * 针对数据库行记录的 Bean，最为仓储用例查询优化的 DTO 对象使用
 * Class DbBean
 * @package WecarSwoole
 */
class DbBean extends SplBean
{
    // 是否将传入的下划线格式字段（数据库字段）自动对应到 Bean 的驼峰字段
    protected $snackToCamel = true;

    public function __construct(array $data = null, $autoCreateProperty = false)
    {
        if ($this->snackToCamel) {
            foreach ($data as $field => $val) {
                if (!property_exists($this, $field)) {
                    $camelField = Str::camel($field);
                    if (property_exists($this, $camelField)) {
                        $data[$camelField] = $val;
                        unset($data[$field]);
                    }
                }
            }
        }

        if ($valueMapping = $this->setValueMapping()) {
            foreach ($valueMapping as $key => $map) {
                if (array_key_exists($key, $data) && array_key_exists($data[$key], $map)) {
                    $data[$key] = $map[$data[$key]];
                }
            }
        }

        parent::__construct($data, $autoCreateProperty);
    }

    /**
     * 子类可以重写该方法实现值映射，主要针对数据库查出来的值映射成更有意义的值。
     * 格式：sex => [0 => '男', 1 => '女']，构造函数传入 sex => 0, 1 将被转成 男、女。其中 sex 是 Bean 的字段名
     * @return array
     */
    protected function setValueMapping(): array
    {
        return [];
    }
}
