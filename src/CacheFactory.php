<?php

namespace WecarSwoole;

use EasySwoole\EasySwoole\Config;
use Psr\SimpleCache\CacheInterface;
use Symfony\Component\Cache\Simple\ArrayCache;
use Symfony\Component\Cache\Simple\FilesystemCache;
use Symfony\Component\Cache\Simple\NullCache;
use Symfony\Component\Cache\Simple\RedisCache;

class CacheFactory
{
    private const ALLOW_DRIVERS = ['array', 'file', 'redis', 'null'];

    /**
     * @return CacheInterface
     * @throws \WecarSwoole\Exceptions\ConfigNotFoundException
     */
    public static function build(): CacheInterface
    {
        $cacheConf = Config::getInstance()->getConf('cache');

        if (!$cacheConf) {
            $cacheConf = [];
        }

        if (!array_key_exists('driver', $cacheConf) || !in_array(strtolower($cacheConf['driver']), self::ALLOW_DRIVERS)) {
            $cacheConf['driver'] = 'file';
        }

        $expire = $cacheConf['expire'] ?? 3600;
        $namespace = $cacheConf['namespace'] ?? '';

        switch (strtolower($cacheConf['driver'])) {
            case 'array':
                return new ArrayCache($expire);
            case 'file':
                return new FilesystemCache($namespace, $expire, $cacheConf['dir'] ?? '');
            case 'redis':
                return new RedisCache(RedisFactory::build($cacheConf['redis']), $namespace, $expire);
            default:
                return new NullCache();
        }
    }
}