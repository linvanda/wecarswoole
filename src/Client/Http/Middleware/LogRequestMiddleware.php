<?php

namespace WecarSwoole\Client\Http\Middleware;

use EasySwoole\EasySwoole\Config;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Log\LoggerInterface;
use Psr\Log\LogLevel;
use Swlib\Http\BufferStream;
use WecarSwoole\Client\Config\HttpConfig;
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

    public function before(Next $next, HttpConfig $config, RequestInterface $request)
    {
        $this->startTime = time();

        return $next($config, $request);
    }

    public function after(Next $next, HttpConfig $config, RequestInterface $request, ResponseInterface $response)
    {
        if (Config::getInstance()->getConf("api_invoke_log") == 'off') {
            return;
        }
        
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

    protected function logContext(HttpConfig $config, RequestInterface $request, ResponseInterface $response): array
    {
        $responseBody = $response->getBody()->getContents();
        // buffer 重新写入供后面用
        $buff = new BufferStream(strlen($responseBody));
        $buff->write($responseBody);
        $response->withBody($buff);

        $responseBody = mb_strlen($responseBody) > 1024 * 400 ? mb_strcut($responseBody, 0, 1024 * 400) : $responseBody;

        $requestBody = $request->getBody()->getContents();
        // buffer 重新写入供后面用
        $buff = new BufferStream(strlen($requestBody));
        $buff->write($requestBody);
        $request->withBody($buff);

        return [
            'use_time' => time() - $this->startTime,
            'request' => [
                'url' => strval($request->getUri()),
                'body' => $requestBody
            ],
            'response' => [
                'http_code' => $response->getStatusCode(),
                'reason' => $response->getReasonPhrase(),
                'headers' => $response->getHeaders(),
                'body' => $responseBody,
            ]
        ];
    }
}
