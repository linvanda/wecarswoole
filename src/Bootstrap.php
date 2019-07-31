<?php

namespace WecarSwoole;

use DI\ContainerBuilder;
use EasySwoole\Component\Di;
use EasySwoole\EasySwoole\Config;
use Psr\Log\LoggerInterface;
use Swoole\Runtime;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use WecarSwoole\HealthCheck\HealthCheck;
use WecarSwoole\Util\File;
use WecarSwoole\Config\Config as WecarConfig;

/**
 * worker 进程启动脚本
 * 没当 work/task 进程启动时，执行此脚本的 boot 方法
 * 也可以在自定义进程中执行此方法，让自定义进程上下文和 Worker 进程相同
 * Class Bootstrap
 * @package WecarSwoole
 */
class Bootstrap
{
    /**
     * @throws \Throwable
     */
    public static function boot()
    {
        Runtime::enableCoroutine();

        // 加载配置
        static::loadConfig();

        // 注册 DI
        static::registerDI();

        // 注册事件订阅者
        static::registerSubscriber();

        // worker 进程健康监测
        HealthCheck::watch(Container::get(LoggerInterface::class));
    }

    protected static function loadConfig()
    {
        /**
         * 设置配置存储模式为内存数组
         * easyswoole 默认使用 swoole table 存储，而其设置的字段大小为 1024，加之其存储的实现方式，
         * 有可能会导致 value 超过长度而存储失败
         */
        $oldConfig = Config::getInstance()->getConf();
        Config::getInstance()->storageHandler(new WecarConfig())->load($oldConfig);

        //加载应用配置
        Config::getInstance()->loadFile(File::join(CONFIG_ROOT, 'config.php'), true);
    }

    /**
     * @throws \Exception
     */
    protected static function registerDI()
    {
        $builder = new ContainerBuilder();
        $builder->addDefinitions(File::join(CONFIG_ROOT, 'di/di.php'));
        Di::getInstance()->set('di-container', $builder->build());
    }

    /**
     * @throws \Throwable
     */
    protected static function registerSubscriber()
    {
        $dispatcher = Container::get('SymfonyEventDispatcher');
        foreach (Config::getInstance()->getConf('subscriber') ?? [] as $subscriber) {
            $subCls = new \ReflectionClass($subscriber);
            if ($subCls->isSubclassOf(EventSubscriberInterface::class)) {
                $dispatcher->addSubscriber($subCls->newInstance());
            }
        }
    }
}
