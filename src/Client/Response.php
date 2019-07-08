<?php

namespace WecarSwoole\Client;

class Response
{
    protected $body;
    protected $message;
    protected $status;
    protected $isFromMock;

    public function __construct($body = [], $status = 500, $message = '请求出错', bool $isFromMock = false)
    {
        $this->body = $body;
        $this->status = $status;
        $this->message = $message;
        $this->isFromMock = $isFromMock;
    }

    public function getMessage()
    {
        return $this->message;
    }

    public function setMessage(array $message): void
    {
        $this->message = $message;
    }

    /**
     * 注意此 status 是协议层状态码，业务层定义的状态码（如果有）是在 body 中
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

    public function isMock(): bool
    {
        return $this->isFromMock;
    }

    public function __toString()
    {
        return json_encode([
            'http_code' => $this->status,
            'message' => $this->message,
            'body' => $this->body,
            'is_mock' => $this->isFromMock
        ]);
    }
}
