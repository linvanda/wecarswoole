<?php

namespace WecarSwoole\Client\Contract;

use WecarSwoole\Client\Config\HttpConfig;

/**
 * Http 请求组装器
 * Interface IHttpRequestAssembler
 * @package WecarSwoole\Client\Contract
 */
interface IHttpRequestAssembler
{
    public function __construct(HttpConfig $config);
    public function assemble(array $params): IHttpRequestBean;
}