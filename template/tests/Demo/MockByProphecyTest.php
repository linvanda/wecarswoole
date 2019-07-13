<?php

namespace Test\Demo;

use PHPUnit\Framework\TestCase;
use Prophecy\Prophet;
use Test\Demo\Sample\SomeClass;

/**
 * 使用 Prophecy 创建 Mock
 * Class MockByProphecyTest
 * @package Test\Demo
 */
class MockByProphecyTest extends TestCase
{
    /**
     * @var Prophet
     */
    private $prophet;

    public function setUp()
    {
        $this->prophet = new Prophet();
    }

    public function testProphet()
    {
        $someObj = $this->prophet->prophesize(SomeClass::class);
        $someObj->run()->willReturn(false);
        $this->assertEquals(false, $someObj->reveal()->run());
    }

    public function tearDown()
    {
        $this->prophet->checkPredictions();
    }
}
