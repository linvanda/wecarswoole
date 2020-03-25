<?php

namespace WecarSwoole\SubServer;

/**
 * 子服务信息
 * 一个子服务可以对外提供多种服务，如同时提供 http 和 webSocket 服务，通过协议决定使用哪个具体的服务类型
 */
class Server
{
    protected $appId;
    protected $name;
    protected $secret;
    protected $addressList;

    /**
     * $addressList 结构：
     * [
     *      'http' => [$address1, $address2,...], // 协议 => 地址列表（Address 类型）
     * ]
     */
    public function __construct(string $appId, string $name, string $secret, array $addressList)
    {
        $this->appId = $appId;
        $this->name = $name;
        $this->secret = $secret;
        $this->addressList = $addressList;
    }

    public function appid(): int
    {
        return $this->appId;
    }

    public function name(): string
    {
        return $this->name;
    }

    public function secret(): string
    {
        return $this->secret;
    }

    /**
     * 获取子服务对应协议的一个地址
     * http 和 https 协议可以互通
     */
    public function address(string $protocol = Address::PROTO_HTTP): ?Address
    {
        if (!$this->addressList) {
            return null;
        }

        $protocol = strtolower($protocol);
        if ($protocol === 'http' && !isset($this->addressList[$protocol])) {
            $protocol = 'https';
        }

        if (!isset($this->addressList[$protocol])) {
            return null;
        }

        if (count($this->addressList[$protocol]) === 1) {
            return $this->addressList[$protocol][0];
        }

        // 从多个中选择一个
        return $this->randomOne($this->addressList[$protocol]);
    }

    protected function randomOne(array $addressList): Address
    {
        // 目前仅取第一个（大部分配的都是负载均衡的 url）
        return $addressList[0];
    }
}
