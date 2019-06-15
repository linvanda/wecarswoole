<?php

namespace WecarSwoole\Client\Http\Component;

use WecarSwoole\Client\Config\HttpConfig;
use WecarSwoole\Client\Contract\IHttpServerParser;
use WecarSwoole\Util\Url;
use EasySwoole\EasySwoole\Config as EsConfig;

/**
 * 默认的 Http 服务器解析器
 * Class DefaultHttpServerParser
 * @package WecarSwoole\Client\Http\Component
 */
class DefaultHttpServerParser implements IHttpServerParser
{
    protected $config;

    public function __construct(HttpConfig $config)
    {
        $this->config = $config;
    }

    public function parse(): string
    {
        $server = $this->config->server;

        // 没有单独配 server，则试图从 path 中解析 server
        if (!$server) {
            if ($this->config->path) {
                $uriArr = Url::parse($this->config->path);
                if ($uriArr['schema'] && $uriArr['host']) {
                    return implode('://', [$uriArr['schema'], $uriArr['host']]);
                } else {
                    return '';
                }
            } else {
                return '';
            }
        }

        if (strpos($server, '://') !== false) {
            return $server;
        }

        // 从配置中解析 server
        return $this->parseFromConfig($server);
    }

    protected function parseFromConfig(string $server): string
    {
        if (!($serverConf = EsConfig::getInstance()->getConf("server.$server"))) {
            return $server;
        }

        if (!($serverConf = $serverConf['servers'])) {
            return $server;
        }

        return $this->chooseServer($serverConf);
    }

    /**
     * 目前不考虑权重
     * @param array $servers
     * @return string
     */
    protected function chooseServer(array $servers): string
    {
        return $servers[mt_rand(0, count($servers) - 1)]['url'];
    }
}
