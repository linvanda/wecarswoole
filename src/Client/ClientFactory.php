<?php

namespace WecarSwoole\Client;

use WecarSwoole\Client\Config\HttpConfig;
use WecarSwoole\Client\Contract\IClient;
use WecarSwoole\Client\Contract\IHttpRequestAssembler;
use WecarSwoole\Client\Contract\IResponseParser;
use WecarSwoole\Client\Contract\IHttpServerParser;
use WecarSwoole\Client\Http\Component\HttpServerParser;
use WecarSwoole\Client\Http\Hook\IRequestDecorator;
use WecarSwoole\Client\Http\HttpClient;
use WecarSwoole\Client\Config\Config;
use EasySwoole\EasySwoole\Config as EsConfig;
use WecarSwoole\Container;

class ClientFactory
{
    /**
     * @param string $api
     * @return IClient
     * @throws \Exception
     */
    public static function build(string $api): IClient
    {
        $config = Config::load($api);

        // 当前项目的 app_id
        if (!isset($config['app_id']) && ($serverFlag = EsConfig::getInstance()->getConf('app_flag'))) {
            $config['app_id'] = EsConfig::getInstance()->getConf("server.$serverFlag")['app_id'];
        }

        if (!isset($config['app_id'])) {
            throw new \Exception("当前项目没有配置合法的app_id");
        }

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
        $serverParser = $requestAssembler = $responseParser = null;

        // request assembler
        $requestAssemblerClass = $config->requestAssembler;
        if (is_subclass_of($requestAssemblerClass, IHttpRequestAssembler::class)) {
            $requestAssembler = new $requestAssemblerClass($config);
        }

        // response parser
        $responseParserClass = $config->responseParser;
        if (is_subclass_of($responseParserClass, IResponseParser::class)) {
            $responseParser = new $responseParserClass($config);
        }

        // hooks
        $hooks = [];
        foreach ($config->hooks as $hook) {
            if (is_subclass_of($hook, IRequestDecorator::class)) {
                $hooks[] = Container::make($hook);
            }
        }

        return new HttpClient($config, $serverParser, $requestAssembler, $responseParser);
    }
}
