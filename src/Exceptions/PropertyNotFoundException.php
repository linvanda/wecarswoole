<?php

namespace WecarSwoole\Exceptions;

use Throwable;

class PropertyNotFoundException extends \Exception
{
    public function __construct(string $className, string $propertyName, int $code = 0, Throwable $previous = null)
    {
        parent::__construct("属性不存在：{$className}::{$propertyName}", $code, $previous);
    }
}
