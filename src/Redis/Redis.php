<?php

namespace WecarSwoole\Redis;

use EasySwoole\Component\Pool\PoolObjectInterface;

class Redis extends \Redis implements PoolObjectInterface
{

    function gc()
    {
        $this->close();
    }

    function objectRestore()
    {
        // TODO: nothing
    }

    function beforeUse(): bool
    {
        return true;
    }
}
