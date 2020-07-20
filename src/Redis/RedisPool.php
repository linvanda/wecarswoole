<?php

namespace WecarSwoole\Redis;

use EasySwoole\Component\Pool\AbstractPool;
use Psr\Log\LoggerInterface;
use Swoole\Coroutine;
use WecarSwoole\Container;

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

        $this->tryConnect($redis, $conf);

        if (isset($conf['password'])) {
            $redis->auth($conf['password']);
        }
        if (isset($conf['database'])) {
            $redis->select($conf['database']);
        }

        return $redis;
    }

    private function tryConnect(Redis $redis, array $conf)
    {
        // 最多尝试 3 次
        $tryCnt = 0;
        $errMsg = '';
        $errCode = 0;
        while ($tryCnt++ < 3) {
            try {
                $redis->connect(
                    $conf['host'],
                    $conf['port'],
                    $conf['timeout']
                );
                return;
            } catch (\RedisException $e) {
                $errMsg = $e->getMessage();
                $errCode = $e->getCode();
                $logConf = $conf;
                if ($conf['password']) {
                    $logConf['password'] = substr($conf['password'], 0, 4) . '******';
                }
                Container::get(LoggerInterface::class)->emergency("redis connect fail:code:$errCode, msg:$errMsg,retry $tryCnt", ['conf' => $logConf]);
                Coroutine::sleep(1);
            }
        }

        // 3 次不成功，抛出异常，由业务层决定如何处理
        throw new \RedisException($errMsg, $errCode);
    }
}
