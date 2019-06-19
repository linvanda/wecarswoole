<?php

namespace WecarSwoole\Client\Http\Hook;

use Psr\Http\Message\ResponseInterface;
use Psr\Log\LoggerInterface;
use Psr\Log\LogLevel;
use WecarSwoole\Client\Config\HttpConfig;
use WecarSwoole\Client\Contract\IHttpRequestBean;

/**
 * 记录请求日志
 * Class LogRequestDecorator
 * @package WecarSwoole\Client\Http\Hook
 */
class LogRequestDecorator implements IRequestDecorator
{
    protected $logger;
    protected $startTime;

    public function __construct(LoggerInterface $logger)
    {
        $this->logger = $logger;
    }

    public function before(HttpConfig $config, IHttpRequestBean $request): bool
    {
        $this->startTime = time();
        return true;
    }

    public function after(HttpConfig $config, IHttpRequestBean $request, ResponseInterface $response)
    {
        $level = $this->logLevel($response);
        $context = $this->logContext($config, $request, $response);

        $this->logger->log($level, '请求信息', $context);
    }

    protected function logLevel(ResponseInterface $response): string
    {
        return $response->getStatusCode() >= 200 && $response->getStatusCode() < 400 ?
            LogLevel::INFO : LogLevel::EMERGENCY;
    }

    protected function logContext(HttpConfig $config, IHttpRequestBean $request, ResponseInterface $response): array
    {
        $body = (string)$response->getBody();
        $body = mb_strlen($body) > 1024 * 400 ? mb_strcut($body, 0, 1024 * 400) : $body;

        return [
            'use_time' => time() - $this->startTime,
            'request' => (string)$request,
            'response' => [
                'http_code' => $response->getStatusCode(),
                'reason' => $response->getReasonPhrase(),
                'headers' => $response->getHeaders(),
                'body' => $body,
            ]
        ];
    }
}
