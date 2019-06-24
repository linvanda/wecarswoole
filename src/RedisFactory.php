<?php

namespace WecarSwoole;

use WecarSwoole\Exceptions\ConfigNotFoundException;
use EasySwoole\EasySwoole\Config;

/**
 * Redis 工厂
 * Class RedisFactory
 * @package WecarSwoole
 */
class RedisFactory
{
    /**
     * @param string $redisAlias
     * @return \Redis
     * @throws ConfigNotFoundException
     */
    public static function build(string $redisAlias): \Redis
    {
        $redisConf = Config::getInstance()->getConf("redis.$redisAlias");
        if (!$redisConf) {
            throw new ConfigNotFoundException("redis.$redisAlias");
        }

        $redis = new \Redis();
        $redis->pconnect($redisConf['host'], $redisConf['port'], 3);
        if ($redisConf['auth']) {
            $redis->auth($redisConf['auth']);
        }

        return $redis;
    }
}
