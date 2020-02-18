<?php

namespace WecarSwoole\Client;

use WecarSwoole\Util\Url;

/**
 * 远程调用客户端入口类 facade
 * 远程调用 api 统一在 config/api.php 中定义
 * Class API
 * @package WecarSwoole\Client
 */
class API
{
    /**
     * @param string $api
     * @param array $params
     * @param array $config
     * @return Response
     * @throws \Throwable
     * @throws \WecarSwoole\Exceptions\ConfigNotFoundException
     */
    public static function invoke(string $api, array $params = [], array $config = []): Response
    {
        return ClientFactory::build($api, $config)->call($params);
    }

    /**
     * 一个精简的调用接口，支持直接指定绝对 url
     * @param string $url
     * @param string $method
     * @param array $params
     * @param string $group
     * @param array $config
     * @return Response
     * @throws \Throwable
     * @throws \WecarSwoole\Exceptions\ConfigNotFoundException
     */
    public static function simpleInvoke(string $url, string $method = 'GET', array $params = [], string $group = '_', array $config = []): Response
    {
        if (!$url) {
            $url = '_';
        }

        if (!filter_var($url, FILTER_VALIDATE_URL)) {
            $api = strpos($url, ':') === false ? "$group:$url" : $url;
        } else {
            $api = "$group:_";
            $config['path'] = $url;
        }

        $config['method'] = $method;

        return self::invoke($api, $params, $config);
    }
}
