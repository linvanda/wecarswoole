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
     * 接口调用
     * @param string $api 需要在配置文件中定义。格式：$group:$api，如 weicheche:user.add
     * @param array $params 请求参数，支持简单格式和复杂格式，见 README 文件说明
     * @param array $config 调用级别配置
     * @return Response
     * @throws \Throwable
     * @throws \WecarSwoole\Exceptions\ConfigNotFoundException
     */
    public static function invoke(string $api, array $params = [], array $config = []): Response
    {
        if (
            isset($config['retry_num'])
            && intval($config['retry_num']) >= 1 
            && (!isset($config['retry_func']) || !is_callable(isset($config['retry_func'])))
        ) {
            $config['retry_func'] = function ($currentNum) {
                // $currentNum：尝试次数
                return pow($currentNum, 2) * 3;
            };
        }

        return ClientFactory::build($api, $config)->call($params);
    }

    /**
     * 支持失败重试的便捷方法
     * 默认重试 3 次，使用默认的时间间隔机制
     */
    public static function retryInvoke(string $api, array $params = [], array $config = []): Response
    {
        if (!isset($config['retry_num'])) {
            // 这里取一个很大的数，内部将改成使用默认的重试次数
            $config['retry_num'] = 1000;
        }

        return self::invoke($api, $params, $config);
    }

    /**
     * 一个精简的接口调用方，支持直接指定绝对 url
     * @param string $url 可以是绝对 url，也可以是 url 别名（即在配置文件中定义的，不过此情况不常用，此情况建议直接用 invoke 方法）
     * @param string $method
     * @param array $params
     * @param string $group 接口分组。一般可以用此参数指定要使用的组级别配置（如请求组装器、响应解析器等）。如果设置为 _ 则使用默认配置
     * @param array $config 调用级别配置
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

    /**
     * 支持失败重试版本的 simpleInvoke
     */
    public static function retrySimpleInvoke(string $url, string $method = 'GET', array $params = [], string $group = '_', array $config = []): Response
    {
        if (!isset($config['retry_num'])) {
            // 这里取一个很大的数，内部将改成使用默认的重试次数
            $config['retry_num'] = 1000;
        }

        return self::simpleInvoke($url, $method, $params, $group, $config);
    }
}
