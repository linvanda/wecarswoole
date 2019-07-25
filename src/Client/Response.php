<?php

namespace WecarSwoole\Client;

class Response
{
    protected $body;
    protected $message;
    protected $status;
    protected $fromRealRequest; // 是否来自真正的请求，还是模拟的

    public function __construct($body = [], $status = 500, $message = '请求出错', $fromRealRequest = true)
    {
        $this->body = $body;
        $this->status = $status;
        $this->message = $message;
        $this->fromRealRequest = $fromRealRequest;
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

    public function fromRealRequest(): bool
    {
        return $this->fromRealRequest;
    }

    public function __toString()
    {
        return json_encode([
            'http_code' => $this->status,
            'message' => $this->message,
            'body' => $this->body,
            'from_real_request' => intval($this->fromRealRequest)
        ]);
    }
}
