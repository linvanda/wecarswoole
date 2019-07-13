<?php

namespace Test\Demo;

use PHPUnit\Framework\TestCase;

/**
 * 基本用法
 * Class BaseTest
 * @package Test
 */
class BaseTest extends TestCase
{
    public function testArray()
    {
        $arr = [];

        $this->assertEmpty($arr);
        $arr[] = 3;
        $this->assertEquals(1, count($arr));
    }

//    public function testSomething()
//    {
//        $this->markTestIncomplete("此测试尚未实现");
//    }
}
