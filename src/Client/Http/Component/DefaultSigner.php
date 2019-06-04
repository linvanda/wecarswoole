<?php

namespace WecarSwoole\Client\Http\Component;

use EasySwoole\EasySwoole\Config;

/**
 * 默认 http 签名器
 * 注意签名器和组装器配合使用，通过构造函数传参给组装器
 * Class DefaultSigner
 * @package WecarSwoole\Client\Http\Component
 */
class DefaultSigner
{
    /**
     * @param array $params 用于签名的参数
     * @return string
     */
    public static function sign($appId, string $server, array $params): string
    {
        $secret = Config::getInstance()->getConf("server.$server.secret", '');
        $params['app_id'] = $appId;
        ksort($params);
        $query_string = http_build_query($params);

        return md5($query_string . $secret);
    }

    /**
     * 验证相应签名的正确性
     * @return bool
     */
    public static function validate(): bool
    {
        return true;
    }
}