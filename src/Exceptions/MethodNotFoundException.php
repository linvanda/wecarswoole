<?php

namespace WecarSwoole\Exceptions;

use Throwable;

class MethodNotFoundException extends Exception
{
    /**
     * 如果是类方法，则需要提供类名+方法名
     * MethodNotFoundException constructor.
     * @param string $methodName
     * @param int $code
     * @param Throwable|null $previous
     */
    public function __construct(string $methodName = "", int $code = 0, Throwable $previous = null)
    {
        parent::__construct("方法不存在：{$methodName}", $code, [], [], false, $previous);
    }
}
