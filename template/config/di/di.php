<?php

use Psr\SimpleCache\CacheInterface;
use Psr\Log\LoggerInterface;
use Psr\EventDispatcher\EventDispatcherInterface;
use Psr\Container\ContainerInterface;
use Symfony\Component\EventDispatcher\EventDispatcher;
use EasySwoole\Component\Di;
use WecarSwoole\CacheFactory;
use WecarSwoole\ID\IIDGenerator;
use WecarSwoole\ID\UUIDGenerator;
use WecarSwoole\Logger;

use function DI\{autowire, get};

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
    'SymfonyEventDispatcher' =>  get(EventDispatcherInterface::class),
    // DI 容器
    ContainerInterface::class => function () {
        return Di::getInstance()->get('di-container');
    },
    // ID 生成器
    IIDGenerator::class => function () {
        return new UUIDGenerator();
    }
];
