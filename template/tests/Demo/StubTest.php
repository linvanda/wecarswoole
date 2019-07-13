<?php

namespace Test\Demo;

use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Test\Demo\Sample\SomeClass;
use Test\Demo\Sample\FileNotFoundException;

/**
 * 桩件。一般用桩件（Stub）来模拟外部依赖（如数据库对象）
 * Class StubTest
 * @package Test\Demo
 */
class StubTest extends TestCase
{
    /**
     * @var MockObject
     */
    private $stub;

    public function setUp()
    {
        $this->stub = $this->createMock(SomeClass::class);
        parent::setUp();
    }

    public function testStub()
    {
        $this->stub->method('run')->willReturn("result");
        $this->assertEquals('result', $this->stub->run());
    }

    public function testStubReturnArgument()
    {
        $this->stub->method('run')->will($this->returnArgument(0));
        $this->assertEquals('foo', $this->stub->run('foo'));
        $this->assertEquals('bar', $this->stub->run('bar'));
    }

    public function testStubReturnSelf()
    {
        $this->stub->method('run')->will($this->returnSelf());
        $this->assertEquals($this->stub, $this->stub->run());
    }

    /**
     * @param array $map
     */
    public function testStubReturnValueMap()
    {
        $map = [
            ['a', 'b', 1],
            ['d', 'e', 2]
        ];
        $this->stub->method('run')->will($this->returnValueMap($map));
        $this->assertEquals(1, $this->stub->run('a', 'b'));
        $this->assertEquals(2, $this->stub->run('d', 'e'));
    }

    public function testStubThrowException()
    {
        $this->expectException(FileNotFoundException::class);

        $this->stub->method('run')->will($this->throwException(new FileNotFoundException("未找到文件")));
        $this->stub->run();
    }
}
