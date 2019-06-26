<?php

use Psr\SimpleCache\CacheInterface;
use Psr\Log\LoggerInterface;
use Psr\EventDispatcher\EventDispatcherInterface;
use Psr\Container\ContainerInterface;
use Symfony\Component\EventDispatcher\EventDispatcher;
use EasySwoole\Component\Di;
use WecarSwoole\CacheFactory;
use WecarSwoole\Logger;

use function DI\{autowire};

return [
    // 仓储
    'App\Domain\*\I*Repository' => autowire('\App\Foundation\Repository\*\MySQL*Repository'),
    // 缓存
    CacheInterface::class => function () {
        return CacheFactory::build();
    },
    // 日志
    LoggerInterface::class => function () {
        return Logger::getInstance();
    },
    // 事件
    EventDispatcherInterface::class => function () {
        return new EventDispatcher();
    },
    // DI 容器
    ContainerInterface::class => function () {
        return Di::getInstance()->get('di-container');
    }
];
