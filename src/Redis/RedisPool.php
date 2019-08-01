<?php

namespace WecarSwoole\Redis;

use EasySwoole\Component\Pool\AbstractPool;

/**
 * 不要在外面直接用这个连接池
 * Class RedisPool
 * @package WecarSwoole\Redis
 * @internal can not new this class directly
 */
class RedisPool extends AbstractPool
{
    protected function createObject()
    {
        $conf = $this->getConfig()->getExtraConf();
        try {
            $redis = new \Redis();
            $redis->pconnect(
                $conf['host'],
                $conf['port'],
                $conf['timeout']
            );
            if ($conf['password']) {
                $redis->auth($conf['password']);
            }
        } catch (\RedisException $exception) {
            // do nothing
        }

        return $redis;
    }
}
