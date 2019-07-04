<?php

namespace WecarSwoole\Exceptions;

use Throwable;

class ConfigNotFoundException extends Exception
{
    public function __construct(string $configName = "", int $code = 0, Throwable $previous = null)
    {
        parent::__construct("未找到配置信息：{$configName}", $code, [], [], false, $previous);
    }
}
