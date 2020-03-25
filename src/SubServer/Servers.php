<?php

namespace WecarSwoole\SubServer;

use EasySwoole\Component\Singleton;
use EasySwoole\EasySwoole\Config;

/**
 * 子系统服务
 */
class Servers
{
    use Singleton;
    /**
     * @var array 服务列表，结构：[appid => $server,]
     */
    protected $servers = [];
    /**
     * @var array 服务别名到 appid 的映射，结构：[alias => appid]
     */
    public $aliasMap = [];
    /**
     * @var array 和 aliasMap 反过来：[appid => alias]
     */
    public $appIdMap = [];

    protected function __construct()
    {
        // 解析出所有的服务别名和 appid 映射关系
        $modulesConf = Config::getInstance()->getConf('server.modules');
        $modulesConf = is_string($modulesConf) ? json_decode($modulesConf, true) : $modulesConf;
        foreach ($modulesConf as $alias => $conf) {
            if (!isset($conf['app_id'])) {
                continue;
            }
            $this->aliasMap[$alias] = $conf['app_id'];
        }

        $this->appIdMap = array_flip($this->aliasMap);
    }

    /**
     * 通过服务别名获取服务
     */
    public function getByAlias(string $alias): ?Server
    {
        if (!$appId = $this->aliasMap[$alias]) {
            return null;
        }

        return $this->getByAppId($appId);
    }

    /**
     * 通过 appId 获取服务
     */
    public function getByAppId(int $appId): ?Server
    {
        if (isset($this->servers[$appId])) {
            return $this->servers[$appId];
        }

        $this->servers[$appId] = self::createServerFromConfig($appId);

        return $this->servers[$appId];
    }

    /**
     * 工厂方法：从配置文件创建 Server 对象
     */
    protected static function createServerFromConfig(int $appId): ?Server
    {
        if (isset($this->servers[$appId])) {
            return null;
        }

        // 从配置文件中获取 app_id 和 module 相关配置
        $appConf = Config::getInstance()->getConf('server.app_ids')[$appId] ?? [];
        $moduleConf = Config::getInstance()->getConf('server.modules')[$this->appIdMap[$appId]] ?? [];
        $appConf = is_string($appConf) ? json_decode($appConf, true) : $appConf;
        $moduleConf = is_string($moduleConf) ? json_decode($moduleConf) : $moduleConf;

        if (!$appConf || !$moduleConf) {
            return null;
        }

        $addressList = [];
        if (isset($moduleConf['servers'])) {
            // http 服务
            if (!isset($addressList[Address::PROTO_HTTP])) {
                $addressList[Address::PROTO_HTTP] = [];
            }

            foreach ($moduleConf['servers'] as $addrConf) {
                // 处理 url
                if (!isset($addrConf['url'])) {
                    continue;
                }

                if (strpos($addrConf['url'], 'http') !== 0) {
                    $addrConf['url'] = 'http://' . $addrConf['url'];
                }

                $addressList[Address::PROTO_HTTP][] = new Address($addrConf['url'], $addrConf['weight'] ?? 100);
            }
        }

        if (isset($moduleConf['tcp_servers'])) {
            // tcp 服务
            if (!isset($addressList[Address::PROTO_TCP])) {
                $addressList[Address::PROTO_TCP] = [];
            }

            foreach ($moduleConf['tcp_servers'] as $addrConf) {
                $addressList[Address::PROTO_TCP][] = new Address($addrConf['host'], $addrConf['weight'] ?? 100, $addrConf['port']);
            }
        }

        return new Server($appId, $moduleConf['name'], $appConf['secret'], $addressList);
    }
}
