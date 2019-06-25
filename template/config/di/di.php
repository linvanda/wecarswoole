<?php

use WecarSwoole\CacheFactory;
use WecarSwoole\Logger;
use Psr\SimpleCache\CacheInterface;
use Psr\Log\LoggerInterface;
use Psr\EventDispatcher\EventDispatcherInterface;

use function DI\{create, factory};

return [
    // 仓储
    'App\Domain\*\I*Repository' => create('\App\Foundation\Repository\*\MySQL*Repository'),
    // 缓存
    CacheInterface::class => factory([CacheFactory::class, 'build']),
    // 日志
    LoggerInterface::class => factory(function () {
        return Logger::getInstance();
    }),
    // 事件
    EventDispatcherInterface::class => create(\Symfony\Component\EventDispatcher\EventDispatcher::class),
];
