<?php

namespace WecarSwoole\Client\Http\Middleware;

use Psr\Http\Message\ResponseInterface;
use Psr\Log\LoggerInterface;
use Psr\Log\LogLevel;
use WecarSwoole\Client\Config\HttpConfig;
use WecarSwoole\Client\Contract\IHttpRequestBean;
use WecarSwoole\Middleware\Next;

/**
 * 记录请求日志
 * Class LogRequestMiddleware
 * @package WecarSwoole\Client\Http\Middleware
 */
class LogRequestMiddleware implements IRequestMiddleware
{
    protected $logger;
    protected $startTime;

    public function __construct(LoggerInterface $logger)
    {
        $this->logger = $logger;
    }

    public function before(Next $next, HttpConfig $config, IHttpRequestBean $request)
    {
        $this->startTime = time();

        return $next($config, $request);
    }

    public function after(Next $next, HttpConfig $config, IHttpRequestBean $request, ResponseInterface $response)
    {
        $this->logger->log(
            $this->logLevel($response),
            'API 调用信息',
            $this->logContext($config, $request, $response)
        );

        return $next($config, $request, $response);
    }

    protected function logLevel(ResponseInterface $response): string
    {
        return $response->getStatusCode() >= 200 && $response->getStatusCode() < 400 ?
            LogLevel::INFO : LogLevel::CRITICAL;
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
