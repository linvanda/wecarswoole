<?php

namespace WecarSwoole\Client;

use WecarSwoole\Client\Config\HttpConfig;
use WecarSwoole\Client\Contract\IClient;
use WecarSwoole\Client\Contract\IHttpRequestAssembler;
use WecarSwoole\Client\Contract\IResponseParser;
use WecarSwoole\Client\Contract\IHttpServerParser;
use WecarSwoole\Client\Http\HttpClient;
use WecarSwoole\Client\Config\Config;
use WecarSwoole\Exceptions\ConfigNotFoundException;

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
        $serverParser = $requestAssembler = $responseParser = null;
        $config = new HttpConfig($conf);

        // server parser
        $serverParserClass = $config->serverParser;
        if (is_subclass_of($serverParserClass, IHttpServerParser::class)) {
            $serverParser = new $serverParserClass($config);
        }

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

        return new HttpClient($config, $serverParser, $requestAssembler, $responseParser);
    }
}