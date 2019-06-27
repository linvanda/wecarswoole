<?php

namespace WecarSwoole\Exceptions;
use Throwable;

/**
 * 框架异常基类
 * Class Exception
 * @package WecarSwoole\Exceptions
 */
class Exception extends \Exception
{
    protected $shouldRetry;

    public function __construct(
        string $message = "",
        int $code = 0,
        Throwable $previous = null,
        bool $shouldRetry = false
    ) {
        $this->shouldRetry = $shouldRetry;
        parent::__construct($message, $code, $previous);
    }

    public function retry(bool $retry = true): Exception
    {
        $this->shouldRetry = $retry;
        return $this;
    }

    public function shouldRetry(): bool
    {
        return $this->shouldRetry;
    }
}
