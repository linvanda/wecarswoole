<?php

namespace WecarSwoole\Client;

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
     * @return Response
     * @throws \Exception
     */
    public static function invoke(string $api, array $params = []): Response
    {
        return ClientFactory::build($api)->call($params);
    }
}
