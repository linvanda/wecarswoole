<?php

namespace WecarSwoole\Client\Http\Component;

use WecarSwoole\Client\Config\Config;
use WecarSwoole\Client\Contract\IResponseParser;
use WecarSwoole\Client\Response;

/**
 * 默认的响应解析器
 * Class DefaultResponseParser
 * @package WecarSwoole\Client\Http\Component
 */
class DefaultResponseParser implements IResponseParser
{
    private $config;

    public function __construct(Config $config)
    {
        $this->config = $config;
    }

    /**
     * 默认实现：什么也不处理
     * @param Response $response
     * @return Response
     */
    public function parser(Response $response): Response
    {
        return $response;
    }
}
