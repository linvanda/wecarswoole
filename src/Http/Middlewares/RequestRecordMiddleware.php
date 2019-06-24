<?php

namespace WecarSwoole\Http\Middlewares;

use EasySwoole\EasySwoole\Config;
use EasySwoole\Http\Request;
use EasySwoole\Http\Response;
use Psr\Log\LoggerInterface;
use Psr\Log\LogLevel;
use WecarSwoole\Container;
use WecarSwoole\Http\Controller;

/**
 * 请求信息记录
 * Class RequestRecordMiddleware
 */
class RequestRecordMiddleware implements IControllerMiddleware
{
    protected static $on;
    protected $logTheRequest;
    protected $startTime;

    public function before(Controller $controller, Request $request, Response $response)
    {
        if (self::$on === false) {
            return true;
        }

        $conf = Config::getInstance()->getConf('request_log');
        if (!$conf || !isset($conf['onoff']) || $conf['onoff'] == 'off' || !$conf['methods']) {
            self::$on = false;
            return true;
        }

        self::$on = true;

        if (!in_array(strtoupper($request->getMethod()), array_map(function ($item) {return strtoupper($item);}, $conf['methods']))) {
            $this->logTheRequest = false;
            return true;
        }

        $this->logTheRequest = true;
        $this->startTime = time();

        return true;
    }

    public function after(Controller $controller, Request $request, Response $response)
    {
        if (self::$on === false || !$this->logTheRequest) {
            return;
        }

        $uri = $request->getUri()->getPath() . '?' . $request->getUri()->getQuery();
        $context = [
            'params' => $request->getRequestParam(),
            'response' => (string)$response->getBody(),
            'from' => $request->getServerParams()['remote_addr'],
            'use_time' => time() - $this->startTime
        ];

        $this->log($uri, $context);
    }

    public function gc()
    {
        $this->logTheRequest = null;
        $this->startTime = null;
    }

    protected function log(string $uri, array $context)
    {
        Container::get(LoggerInterface::class)->log(LogLevel::INFO, "请求信息:{$uri}", $context);
    }
}
