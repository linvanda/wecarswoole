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
    protected $context;
    protected $data;

    /**
     * Exception constructor.
     * @param string $message
     * @param int $code
     * @param array $context 额外上下文信息，记录到日志里面
     * @param array $data 需要返回给客户端的 data 信息
     * @param bool $shouldRetry 是否需要客户端重试
     * @param Throwable|null $previous
     */
    public function __construct(
        string $message = "",
        int $code = 0,
        array $context = [],
        array $data = [],
        bool $shouldRetry = false,
        Throwable $previous = null
    ) {
        $this->shouldRetry = $shouldRetry;
        $this->context = $context;
        $this->data = $data;
        parent::__construct($message, $code, $previous);
    }

    public function shouldRetry(bool $retry = true): Exception
    {
        $this->shouldRetry = $retry;
        return $this;
    }

    public function isShouldRetry(): bool
    {
        return $this->shouldRetry;
    }

    public function withContext(array $context): Exception
    {
        $this->context = $context;
        return $this;
    }

    public function getContext(): array
    {
        return $this->context;
    }

    public function withData(array $data): Exception
    {
        $this->data = $data;
        return $this;
    }

    public function getData(): array
    {
        return $this->data;
    }
}
