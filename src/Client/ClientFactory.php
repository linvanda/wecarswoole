<?php

namespace WecarSwoole\Client;

use WecarSwoole\Client\Config\HttpConfig;
use WecarSwoole\Client\Contract\IClient;
use WecarSwoole\Client\Contract\IHttpRequestAssembler;
use WecarSwoole\Client\Contract\IResponseParser;
use WecarSwoole\Client\Http\Middleware\IRequestMiddleware;
use WecarSwoole\Client\Http\HttpClient;
use WecarSwoole\Client\Config\Config;
use EasySwoole\EasySwoole\Config as EsConfig;
use WecarSwoole\Container;

class ClientFactory
{
    /**
     * @param string $api
     * @param array $config
     * @return IClient
     * @throws \Exception
     * @throws \WecarSwoole\Exceptions\ConfigNotFoundException
     */
    public static function build(string $api, array $config): IClient
    {
        $config = array_merge(Config::load($api), $config);

        // 当前项目的 app_id
        if (!isset($config['app_id']) && ($serverFlag = EsConfig::getInstance()->getConf('app_flag'))) {
            $config['app_id'] = EsConfig::getInstance()->getConf("server.$serverFlag")['app_id'];
        }

        if (!isset($config['app_id'])) {
            throw new \Exception("当前项目没有配置合法的app_id");
        }

        $config['api_name'] = $api;

        switch (strtolower($config['protocol'])) {
            case 'http':
            case 'https':
                return self::createHttpClient($config);
            default:
                throw new \Exception("未实现的 api 协议客户端：{$config['protocol']}");
        }
    }

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
