<?php

namespace WecarSwoole\ID;

use EasySwoole\Utility\Random;

/**
 * 基于 uuid 的随机数生成器
 */
class UUIDGenerator implements IIDGenerator
{
    public function id(): string
    {
        return Random::makeUUIDV4();
    }
}
