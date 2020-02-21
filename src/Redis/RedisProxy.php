<?php

namespace WecarSwoole\Redis;

use EasySwoole\Component\Pool\PoolManager;

/**
 * 对 redis 扩展的代理类
 * 由于很多外部扩展（如一些 cache 类库）依赖于 redis 扩展，而在 swoole 常驻进程中，这些单例对象（如 cache 实例）会一直持有同一个
 * Redis 连接实例，在长时间不连接的情况下 Redis 服务器会断开连接，从而造成后续操作失败。
 * 该扩展拦截 Redis 的所有操作，在操作前先从连接池获取连接对象，如果连接对象已经和服务器断开连接，则重连，然后执行操作，最后归还
 * 连接池中。
 * 禁止直接使用该类，请用 RedisFactory 获取
 * Class RedisProxy
 * @package WecarSwoole
 * @internal can not new this class directly
 */
class RedisProxy extends \Redis
{
    private static $poolMap = [];
    private $connInfo = [];
    private $poolConf;
    private $pool;

    /**
     * RedisProxy constructor.
     * @param $host
     * @param int $port
     * @param float $timeout
     * @param string $password
     * @param array $poolConf
     * @param int $database
     * @throws \EasySwoole\Component\Pool\Exception\PoolException
     * @throws \EasySwoole\Component\Pool\Exception\PoolObjectNumError
     */
    public function __construct(
        $host,
        $port = 6379,
        $timeout = 0.0,
        $password = '',
        $poolConf = [],
        $database = 0
    ) {
        $this->setConnect($host, $port, $timeout, null, 0);
        $this->setAuth($password);
        $this->poolConf = $poolConf;

        if ($database) {
            $this->setSelect($database);
        }

        // 创建连接池
        $this->pool = $this->getPool();
    }

    /**
     * @param string $host
     * @param int $port
     * @param float $timeout
     * @param null $reserved
     * @param int $retryInterval
     * @return bool|void
     */
    public function setConnect($host, $port = 6379, $timeout = 0.0, $reserved = null, $retryInterval = 0)
    {
        $this->connInfo = [
            'host' => $host,
            'port' => $port,
            'timeout' => $timeout,
            'reserved' => $reserved,
            'retry_interval' => $retryInterval
        ];
    }

    public function setAuth($password)
    {
        $this->connInfo['password'] = $password;
    }

    public function setSelect($database)
    {
        $this->connInfo['database'] = $database;
    }

    /**
     * 执行多条指令，一般用于 redis 事务（watch）
     * @param callable $call
     * @throws \Throwable
     */
    public function invoke(callable $call)
    {
        $redis = $this->pool->getObj();

        try {
            $redis->ping();
        } catch (\RedisException $exception) {
            self::connectRedis($redis, $this->connInfo);
        }

        $call($redis);

        // 归还
        $this->pool->recycleObj($redis);
    }

    /**
     * @param string $method
     * @param array ...$args
     * @return mixed
     * @throws \Throwable
     */
    protected function callMethod(string $method, ...$args)
    {
        $redis = $this->pool->getObj();
        try {
            $result = $redis->{$method}(...$args);
        } catch (\RedisException $e) {
            // 重新连接 Redis
            self::connectRedis($redis, $this->connInfo);
            $result = $redis->{$method}(...$args);
        } finally {
            // 归还
            $this->pool->recycleObj($redis);
        }

        return $result;
    }

    /**
     * @return \EasySwoole\Component\Pool\AbstractPool|null
     * @throws \EasySwoole\Component\Pool\Exception\PoolObjectNumError
     * @throws \EasySwoole\Component\Pool\Exception\PoolException
     */
    private function getPool()
    {
        $key = $this->getPoolKey();
        if (isset(self::$poolMap[$key])) {
            return PoolManager::getInstance()->getPool($this->keyToClassName($key));
        }

        $this->createPool($key);

        return PoolManager::getInstance()->getPool($this->keyToClassName($key));
    }

    /**
     * @param \Redis $redis
     * @param array $connInfo
     */
    private static function connectRedis($redis, array $connInfo)
    {
        $redis->connect(
            $connInfo['host'],
            $connInfo['port'],
            $connInfo['timeout']
        );
        
        if ($connInfo['password']) {
            $redis->auth($connInfo['password']);
        }

        if ($connInfo['database']) {
            $redis->select($connInfo['database']);
        }
    }

    private function getPoolKey(): string
    {
        $arr = $this->connInfo;
        ksort($arr);
        return 'redispool_' . md5(implode('-', array_values($arr)));
    }

    private function keyToClassName(string $key): string
    {
        return "\WecarSwoole\Redis\C$key";
    }

    /**
     * @param string $key
     * @throws \EasySwoole\Component\Pool\Exception\PoolException
     * @throws \EasySwoole\Component\Pool\Exception\PoolObjectNumError
     */
    private function createPool(string $key)
    {
        if (isset(self::$poolMap[$key])) {
            return;
        }

        $className = $this->keyToClassName($key);
        if (!class_exists($className)) {
            $tmpArr = array_filter(explode('\\', $className));
            $shortName = array_pop($tmpArr);
            $namespace = implode('\\', $tmpArr);
            eval("namespace $namespace {class $shortName extends \WecarSwoole\Redis\RedisPool {}}");
        }

        // 创建连接池对象
        $poolConf = PoolManager::getInstance()->register($className);
        $poolConf->setExtraConf($this->connInfo);
        $poolConf->setMinObjectNum($this->poolConf['min_object_num'] ?? 1);
        $poolConf->setMaxObjectNum($this->poolConf['max_object_num'] ?? 10);
        $poolConf->setMaxIdleTime($this->poolConf['max_idle_time'] ?? 60);

        self::$poolMap[$key] = true;
    }
}
