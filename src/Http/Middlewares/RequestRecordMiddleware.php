<?php

namespace WecarSwoole\Http\Middlewares;

use EasySwoole\Http\Request;
use EasySwoole\Http\Response;
use Psr\Log\LoggerInterface;
use Psr\Log\LogLevel;
use WecarSwoole\Container;

/**
 * 请求信息记录
 * Class RequestRecordMiddleware
 */
class RequestRecordMiddleware implements IControllerMiddleware
{
    protected $startTime;

    public function before(Request $request, Response $response)
    {
        $this->startTime = time();
    }

    public function after(Request $request, Response $response)
    {
        $duration = time() - $this->startTime;
        $uri = $request->getUri()->getPath() . '?' . $request->getUri()->getQuery();
        $context = [
            'params' => $request->getRequestParam(),
            'response' => (string)$response->getBody(),
            'from' => $request->getRequestTarget(),
        ];

        $this->log($duration, $uri, $context);
    }

    public function gc()
    {
        unset($this->startTime);
    }

    protected function log(int $duration, string $uri, array $context)
    {
        Container::get(LoggerInterface::class)->log($this->logLevel($duration), "请求信息:{$uri}", $context);
    }

    protected function logLevel(int $duration): string
    {
        if ($duration < 2) {
            return LogLevel::INFO;
        }

        if ($duration < 6) {
            return LogLevel::WARNING;
        }

        return LogLevel::CRITICAL;
    }
}
