<?php

namespace WecarSwoole\ID;

/**
 * ID 生成器
 * 生成一个长度不超过 36 位的字符串 id
 */
interface IIDGenerator
{
    public function id(): string;
}
