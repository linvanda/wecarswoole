<?php

namespace WecarSwoole\Exceptions;

use Throwable;

class ParamsCannotBeNullException extends \Exception
{
    public function __construct(string $paramName, int $code = 0, Throwable $previous = null)
    {
        parent::__construct("参数不能为空：{$paramName}", $code, $previous);
    }
}