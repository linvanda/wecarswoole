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
        $redis = new Redis();
        $redis->connect(
            $conf['host'],
            $conf['port'],
            $conf['timeout']
        );
        if ($conf['password']) {
            $redis->auth($conf['password']);
        }
        if ($conf['database']) {
            $redis->select($conf['database']);
        }

        return $redis;
    }
}
