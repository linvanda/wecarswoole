<?php

use WecarSwoole\CacheFactory;
use WecarSwoole\Logger;
use Psr\SimpleCache\CacheInterface;
use Psr\Log\LoggerInterface;
use Psr\EventDispatcher\EventDispatcherInterface;

return [
    // 仓储
    'App\Domain\*\I*Repository' => \DI\create('\App\Foundation\Repository\*\MySQL*Repository'),
    // 缓存
    CacheInterface::class => \DI\factory([CacheFactory::class, 'build']),
    // 日志
    LoggerInterface::class => \DI\factory(function () {
        return Logger::getInstance();
    }),
    // 事件
    EventDispatcherInterface::class => \DI\create(\Symfony\Component\EventDispatcher\EventDispatcher::class),
];
