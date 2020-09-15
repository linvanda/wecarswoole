<?php

namespace WecarSwoole\Client;

use WecarSwoole\Client\Config\HttpConfig;
use WecarSwoole\Client\Contract\IClient;
use WecarSwoole\Client\Contract\IHttpRequestAssembler;
use WecarSwoole\Client\Contract\IResponseParser;
use WecarSwoole\Client\Http\Middleware\IRequestMiddleware;
use WecarSwoole\Client\Http\HttpClient;
use WecarSwoole\Client\Config\Config;
use WecarSwoole\Container;

class ClientFactory
{
    /**
     * @param string $api
     * @param array $config
     * @return IClient
     * @throws \Exception
     * @throws \WecarSwoole\Exceptions\ConfigNotFoundException
     * @throws \Throwable
     */
    public static function build(string $api, array $config): IClient
    {
        $config = array_merge(Config::load($api), $config);

        // 处理重试次数
        if (isset($config['retry_num'])) {
            $config['retry_num'] = intval($config['retry_num']);
            if ($config['retry_num'] > 5) {
                $config['retry_num'] = isset($config['default_retry_num']) ? min(intval($config['default_retry_num']), 5) : 5;
            }
        }

        switch (strtolower($config['protocol'])) {
            case 'http':
            case 'https':
                return self::createHttpClient($config);
            default:
                throw new \Exception("未实现的 api 协议客户端：{$config['protocol']}");
        }
    }

    /**
     * @param array $conf
     * @return HttpClient
     * @throws \Throwable
     */
    private static function createHttpClient(array $conf): HttpClient
    {
        $config = new HttpConfig($conf);
        $requestAssembler = $responseParser = null;

        // request assembler
        $requestAsbCls = $config->requestAssembler;
        if (is_subclass_of($requestAsbCls, IHttpRequestAssembler::class)) {
            $requestAssembler = new $requestAsbCls($config);
        }

        // response parser
        $responseParserClass = $config->responseParser;
        if (is_subclass_of($responseParserClass, IResponseParser::class)) {
            $responseParser = new $responseParserClass($config);
        }

        // middleware
        $midws = [];
        foreach ($config->middlewares as $middleware) {
            if (is_subclass_of($middleware, IRequestMiddleware::class)) {
                $midws[] = Container::make($middleware);
            }
        }

        $client = new HttpClient($config, $requestAssembler, $responseParser);
        $client->setMiddlewares($midws);

        return $client;
    }
}
