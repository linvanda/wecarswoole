<?php

namespace Test\Demo;

use PHPUnit\Framework\TestCase;
use Test\Demo\Sample\FileNotFoundException;

/**
 * 测试异常
 * Class ExceptionTest
 * @package Test\Demo
 */
class ExceptionTest extends TestCase
{
    public function testException()
    {
        $this->expectException(FileNotFoundException::class);
        $this->expectExceptionCode(500);
        $this->expectExceptionMessageRegExp('/a test/');

        throw new FileNotFoundException("this is a test exception", 500);
    }

    public function testPHPError()
    {
        $this->expectException(\PHPUnit\Framework\Error\Error::class);

        include_once "a_not_exits_file.php";
    }
}
