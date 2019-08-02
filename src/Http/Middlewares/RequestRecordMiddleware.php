<?php

namespace WecarSwoole\Http\Middlewares;

use EasySwoole\EasySwoole\Config;
use EasySwoole\Http\Request;
use EasySwoole\Http\Response;
use Psr\Log\LoggerInterface;
use Psr\Log\LogLevel;
use WecarSwoole\Container;
use WecarSwoole\Middleware\Next;

/**
 * 请求信息记录
 * Class RequestRecordMiddleware
 */
class RequestRecordMiddleware implements IControllerMiddleware
{
    protected static $on;
    protected $logTheRequest;
    protected $startTime;
    protected $beforeDone = false;

    /**
     * @param Next $next
     * @param Request $request
     * @param Response $response
     * @return bool|mixed
     */
    public function before(Next $next, Request $request, Response $response)
    {
        $this->beforeDone = true;

        if (self::$on === false) {
            goto last;
        }

        $conf = Config::getInstance()->getConf('request_log');
        if (!$conf || !isset($conf['onoff']) || $conf['onoff'] == 'off' || !$conf['methods']) {
            self::$on = false;
            goto last;
        }

        self::$on = true;

        if (!in_array(strtoupper($request->getMethod()), array_map(function ($item) {return strtoupper($item);}, $conf['methods']))) {
            $this->logTheRequest = false;
            goto last;
        }

        $this->logTheRequest = true;
        $this->startTime = time();

        last:
        return $next($request, $response);
    }

    public function after(Next $next, Request $request, Response $response)
    {
        if (!$this->beforeDone || self::$on === false || !$this->logTheRequest) {
            return $next($request, $response);
        }

        $uri = $request->getUri()->getPath() . '?' . $request->getUri()->getQuery();
        $context = [
            'params' => $request->getRequestParam(),
            'response' => (string)$response->getBody(),
            'from' => $request->getServerParams()['remote_addr'],
            'use_time' => time() - $this->startTime
        ];

        $this->log($uri, $context);

        return $next($request, $response);
    }

    public function gc(Next $next)
    {
        $this->logTheRequest = null;
        $this->startTime = null;
        $this->beforeDone = false;
        return $next();
    }

    protected function log(string $uri, array $context)
    {
        Container::get(LoggerInterface::class)->log(LogLevel::INFO, "请求信息:{$uri}", $context);
    }
}
