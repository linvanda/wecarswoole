<?php

namespace WecarSwoole\Client\Http\Component;

use WecarSwoole\Client\Config\HttpConfig;
use WecarSwoole\Client\Contract\IHttpServerParser;
use WecarSwoole\Util\Url;
use WecarSwoole\SubServer\Address;
use WecarSwoole\SubServer\Servers;

/**
 * 默认的 Http 服务器解析器
 * Class HttpServerParser
 * @package WecarSwoole\Client\Http\Component
 */
class HttpServerParser implements IHttpServerParser
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

        if (strpos($server, '://') !== false || strlen($server) > 8) {
            return $server;
        }

        // 从配置中解析 server
        if (!$serverObj = Servers::getInstance()->getByAlias($server)) {
            return $server;
        }

        return $serverObj->address(Address::PROTO_HTTP)->url();
    }
}
