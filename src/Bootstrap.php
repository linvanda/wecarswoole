<?php

namespace WecarSwoole;

use DI\ContainerBuilder;
use EasySwoole\Component\Di;
use EasySwoole\EasySwoole\Config;
use \Symfony\Component\EventDispatcher\EventDispatcher;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use WecarSwoole\Util\File;

/**
 * 启动脚本
 * 没当 work/task 进程启动时，执行此脚本的 boot 方法
 * Class Bootstrap
 * @package WecarSwoole
 */
class Bootstrap
{
    public static function boot()
    {
        // 加载配置
        static::loadConfig();

        // 注册 DI
        static::registerDI();

        // 注册事件订阅者
        static::registerSubscriber();
    }

    protected static function loadConfig()
    {
        Config::getInstance()->loadFile(File::join(EASYSWOOLE_ROOT, 'config/api/api.php'), false);
        Config::getInstance()->loadFile(File::join(EASYSWOOLE_ROOT, 'config/subscriber/subscriber.php'), false);
    }

    protected static function registerDI()
    {
        $builder = new ContainerBuilder();
        $builder->addDefinitions(File::join(EASYSWOOLE_ROOT, 'config/di/di.php'));
        Di::getInstance()->set('di-container', $builder->build());
    }

    protected static function registerSubscriber()
    {
        $dispatcher = Container::get(EventDispatcher::class);
        foreach (Config::getInstance()->getConf('subscriber') ?? [] as $subscriber) {
            $subCls = new \ReflectionClass($subscriber);
            if ($subCls->isSubclassOf(EventSubscriberInterface::class)) {
                $dispatcher->addSubscriber($subCls->newInstance());
            }
        }
    }
}
