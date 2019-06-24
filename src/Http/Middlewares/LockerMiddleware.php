<?php

namespace WecarSwoole\Http\Middlewares;

use Dev\Locker\Locker;
use EasySwoole\EasySwoole\Config;
use EasySwoole\Http\Request;
use EasySwoole\Http\Response;
use WecarSwoole\Http\Controller;
use WecarSwoole\RedisFactory;

/**
 * 请求并发锁，基于 Redis。用于控制同一个客户端同时打入多条同样的请求，造成数据错误
 * 需要配置是否开启此锁，如果开启，必须指定 redis
 * Class LockerMiddleware
 * @package WecarSwoole\Http\Middlewares
 */
class LockerMiddleware implements IControllerMiddleware
{
    private static $on;
    private static $redisBuildErrNum = 0;
    /** @var Locker*/
    private $locker;
    private $timeout;

    public function __construct(int $timeout = 5)
    {
        $this->timeout = $timeout;
    }

    /**
     * @param Request $request
     * @param Response $response
     * @return bool
     * @throws \Exception
     */
    public function before(Controller $controller, Request $request, Response $response)
    {
        if (self::$on === false) {
            return true;
        }

        $conf = Config::getInstance()->getConf('concurrent_locker');
        if (!$conf || strtolower($conf['onoff']) == 'off' || !isset($conf['redis'])) {
            self::$on = false;
            return true;
        }

        if (!Config::getInstance()->getConf("redis." . $conf['redis'])) {
            self::$on = false;
            return true;
        }

        try {
            $redis = RedisFactory::build($conf['redis']);
        } catch (\Exception $e) {
            self::$redisBuildErrNum++;

            if (self::$redisBuildErrNum > 10) {
                self::$on = false;
            }

            return true;
        }

        self::$on = true;

        if (!($key = $this->key($controller, $request))) {
            return true;
        }

        $this->locker = new Locker($redis, $key, $this->timeout);

        if (!$this->locker->lock()) {
            throw new \Exception("获取并发锁失败，请不要频繁请求");
        }

        return true;
    }

    public function after(Controller $controller, Request $request, Response $response)
    {
        if (is_bool(self::$on) && !self::$on) {
            return;
        }

        $this->locker && $this->locker->unlock();
    }

    public function gc()
    {
        if (is_bool(self::$on) && !self::$on) {
            return;
        }

        $this->locker = null;
    }

    /**
     * 根据请求信息（请求url、参数、客户端ip）计算key
     * @return string
     */
    protected function key(Controller $controller, Request $request)
    {
        $lockerMap = $controller->lockers();
        $action = basename($request->getRequestTarget());

        if (!$lockerMap || (!isset($lockerMap[$action]) && !isset($lockerMap['__default']))) {
            return '';
        }

        if (isset($lockerMap[$action])) {
            if ($lockerMap[$action] === 'none') {
                return '';
            }

            return $this->generateKeyFromParams($lockerMap[$action], $request->getRequestParam());
        } else {
            // 走 default
            if ($lockerMap['__default'] === 'default') {
                return $this->generateKeyFromRequest($request);
            }
        }

        return '';
    }

    private function generateKeyFromParams(array $fields, array $params): string
    {
        $p = [];
        foreach ($fields as $field) {
            $p[] = is_array($params[$field]) ? json_encode($params[$field]) : ($params[$field] ?? '');
        }
        sort($p);

        return md5(implode('-', $p));
    }

    private function generateKeyFromRequest(Request $request): string
    {
        $serverInfo = $request->getServerParams();
        $requestInfo = [
            $serverInfo['remote_addr'],
            $serverInfo['path_info'],
            $serverInfo['query_string'],
            json_encode($request->getParsedBody())
        ];
        sort($requestInfo);

        return md5(implode('-', $requestInfo));
    }
}
