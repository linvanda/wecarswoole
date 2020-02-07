<?php

namespace WecarSwoole;

use WecarSwoole\Exceptions\ConfigNotFoundException;
use EasySwoole\EasySwoole\Config;
use WecarSwoole\Redis\RedisProxy;
use WecarSwoole\Redis\TmpRedisProxyCreator;

/**
 * Redis 工厂
 * Class RedisFactory
 * @package WecarSwoole
 */
class RedisFactory
{
    /**
     * 创建临时 RedisProxy 子类实例
     * @param string $redisAlias
     * @return RedisProxy
     * @throws ConfigNotFoundException
     */
    public static function build(string $redisAlias): RedisProxy
    {
        $redisConf = Config::getInstance()->getConf("redis.$redisAlias");
        if (!$redisConf) {
            throw new ConfigNotFoundException("redis.$redisAlias");
        }

        $redisClassName = '\WecarSwoole\Redis\TmpAb837dfdeh2nqoiqmgnRedisProxy';
        if (!class_exists($redisClassName)) {
            TmpRedisProxyCreator::create($redisClassName);
        }

        $redis = new $redisClassName(
            $redisConf['host'],
            $redisConf['port'],
            3,
            $redisConf['auth'],
            $redisConf['__pool'] ?? [],
            $redisConf['database'] ?? 0
        );

        return $redis;
    }
}
