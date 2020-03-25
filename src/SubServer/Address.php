<?php

namespace WecarSwoole\SubServer;

use WecarSwoole\Util\Url;

/**
 * 服务地址
 */
class Address
{
    public const PROTO_HTTP = 'http';
    public const PROTO_TCP = 'tcp';
    public const PROTO_UDP = 'udp';
    public const PROTO_WEBSOCKET = 'ws';
    public const PROTO_RPC = 'rpc';

    // 地址（包括端口号）
    protected $url;
    // 端口
    protected $port;
    // 请求协议：https、http、ws 等，如果没有解析出来，默认 tcp
    protected $protocol;
    // 权重，0 - 100
    protected $weight;
    // 允许的请求协议列表
    protected $protocols = ['http', 'https', 'tcp', 'udp', 'ws', 'rpc'];

    public function __construct(string $url, int $weight = 100, int $port = null)
    {
        $this->url = $url;
        $this->weight = $weight < 0 ? 0 : ($weight > 100 ? 100 : $weight);

        $urlInfo = Url::parse($url);

        if (isset($port)) {
            $this->port = $port;
        } else {
            $hostInfo = explode(':', $urlInfo['host'] ?? '');
            if (count($hostInfo) === 2) {
                $this->port = $hostInfo[1];
            } elseif (isset($urlInfo['schema']) && $urlInfo['schema'] === 'https') {
                $this->port = 443;
            } else {
                // 其他情况默认是 80
                $this->port = 80;
            }
        }

        $protocol = isset($urlInfo['schema']) ? strtolower($urlInfo['schema']) : self::PROTO_TCP;
        $this->protocol = $protocol && in_array($protocol, $this->protocols) ? $protocol : self::PROTO_TCP;
    }

    public function url(): string
    {
        return $this->url;
    }

    public function port(): int
    {
        return $this->port;
    }

    public function protocol(): string
    {
        return $this->protocol;
    }

    public function weight(): int
    {
        return $this->weight;
    }
}
