<?php

namespace WecarSwoole\Client\Contract;

use WecarSwoole\Client\Config\HttpConfig;

/**
 * 服务器解析器接口，返回包括协议在内的服务器信息，如 https://www.baidu.com
 * Interface IHttpServerParser
 * @package WecarSwoole\Client\Contract
 */
interface IHttpServerParser
{
    public function __construct(HttpConfig $config);

    public function parse(): string;
}