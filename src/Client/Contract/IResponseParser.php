<?php

namespace WecarSwoole\Client\Contract;

use WecarSwoole\Client\Config\Config;
use WecarSwoole\Client\Response;

/**
 * 响应结果解析器，传入原始响应对象，该解析器解析成业务需要的格式
 * Interface IResponseParser
 * @package WecarSwoole\Client\Contract
 */
interface IResponseParser
{
    public function __construct(Config $config);

    public function parser(Response $response): Response;
}
