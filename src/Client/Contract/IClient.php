<?php

namespace WecarSwoole\Client\Contract;

use WecarSwoole\Client\Response;

/**
 * 远程客户端接口
 * Interface IClient
 * @package WecarSwoole\Client\Contract
 */
interface IClient
{
    public function call(array $params): Response;
}
