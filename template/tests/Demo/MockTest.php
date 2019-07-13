<?php

namespace Test\Demo;

use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Test\Demo\Sample\Observer;
use Test\Demo\Sample\Subject;

/**
 * 仿件(Mock)
 * 和桩件(Stub)不同的是，使用仿件时，测试目标是仿件本身（如检查仿件的某个方法是否被以某种方式调用了）
 * Class MockTest
 * @package Test\Demo
 */
class MockTest extends TestCase
{
    /**
     * @var MockObject
     */
    private $mock;

    public function setUp()
    {
        $this->mock = $this->getMockBuilder(Observer::class)->setMethods(['update'])->getMock();

        parent::setUp();
    }

    public function testUpdateBeInvoked()
    {
        $this->mock->expects($this->once())
            ->method('update')
            ->with($this->equalTo('something'));

        $subject = new Subject('name');
        $subject->attach($this->mock);
        $subject->doSomething();
    }
}
