<?php

namespace WecarSwoole\Client;

class Response
{
    protected $body;
    protected $message;
    protected $status;

    public function __construct($body, $status, $message)
    {
        $this->body = $body;
        $this->status = $status;
        $this->message = $message;
    }

    public function getMessage(): array
    {
        return $this->message;
    }

    public function setMessage(array $message): void
    {
        $this->message = $message;
    }

    /**
     * 注意此 status 是协议层状态码，业务层定义的状态码（如果有）是在 message 中
     * @return int
     */
    public function getStatus(): int
    {
        return $this->status;
    }

    public function setStatus(int $status): void
    {
        $this->status = $status;
    }

    public function getBody()
    {
        return $this->body;
    }

    public function setBody($body)
    {
        $this->body = $body;
    }
}
