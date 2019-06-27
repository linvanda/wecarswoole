<?php

namespace WecarSwoole\Exceptions;

use Throwable;

class PropertyCannotBeNullException extends Exception
{
    public function __construct(string $className, string $propertyName, int $code = 0, Throwable $previous = null)
    {
        parent::__construct("属性不能为空：{$className}::{$propertyName}", $code, $previous);
    }
}
