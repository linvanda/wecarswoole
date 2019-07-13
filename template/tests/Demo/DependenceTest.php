<?php

namespace Test\Demo;

use PHPUnit\Framework\TestCase;

/**
 * 测试用例依赖关系申明
 * 注意：最好不要写此中有依赖关系的测试代码
 * Class DependenceTest
 * @see https://phpunit.readthedocs.io/zh_CN/latest/writing-tests-for-phpunit.html
 * @package Test\Demo
 */
class DependenceTest extends TestCase
{
    public function testArray()
    {
        $arr = [];
        $this->assertEmpty($arr);

        return $arr;
    }

    /**
     * 测试依赖关系
     * @param array $arr
     * @depends testArray
     * @return array
     */
    public function testDependence(array $arr)
    {
        array_push($arr, 4);
        $this->assertEquals(1, count($arr));

        return $arr;
    }

    /**
     * @param array $arr1
     * @param array $arr2
     * @depends testArray
     * @depends testDependence
     */
    public function testDependenceMore(array $arr1, array $arr2)
    {
        $this->assertEquals(1, count($arr2) - count($arr1));
    }
}
