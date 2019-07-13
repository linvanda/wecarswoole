<?php

namespace Test\Demo;

use PHPUnit\Framework\TestCase;

/**
 * 数据提供器
 * Class DataProviderTest
 * @package Test\Demo
 */
class DataProviderTest extends TestCase
{
    /**
     * 测试数据供给
     * @param $a
     * @param $b
     * @param $expected
     * @dataProvider dataProvider
     */
    public function testDataProvider($param1, $param2, $expected)
    {
        $this->assertEquals($expected, $param1 + $param2);
    }

    /**
     * 数据供给器必须返回二维数组，或者迭代器，其元素为数组
     * 可以使用字符串下标将其语义化
     * 第二维的每个元素对应接收方的形参
     * @return array
     */
    public function dataProvider()
    {
        return [
            [1, 2, 3],
            [2, 3, 5],
            ['param1' => 4, 'param2' => 6, 'expected' => 10]
        ];
    }
}
