<?php

namespace WecarSwoole;

use DI\ContainerBuilder;
use EasySwoole\ApolloConfig\Apollo;
use EasySwoole\Component\Di;
use EasySwoole\Component\Timer;
use EasySwoole\EasySwoole\Config;
use EasySwoole\ApolloConfig\Server as ApolloServer;
use Psr\Log\LoggerInterface;
use Swoole\Server;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use WecarSwoole\HealthCheck\HealthCheck;
use WecarSwoole\Util\File;

/**
 * 启动脚本
 * 没当 work/task 进程启动时，执行此脚本的 boot 方法
 * Class Bootstrap
 * @package WecarSwoole
 */
class Bootstrap
{
    /**
     * @throws \Throwable
     */
    public static function boot(Server $server = null, $workerId = null)
    {
        // 注册 apollo 配置中心客户端
        static::registerApolloClient($workerId);

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
        $dispatcher = Container::get('SymfonyEventDispatcher');
        foreach (Config::getInstance()->getConf('subscriber') ?? [] as $subscriber) {
            $subCls = new \ReflectionClass($subscriber);
            if ($subCls->isSubclassOf(EventSubscriberInterface::class)) {
                $dispatcher->addSubscriber($subCls->newInstance());
            }
        }
    }

    protected static function registerApolloClient($workerId)
    {
        if ($workerId == 0) {
            Timer::getInstance()->loop(10000, function () {
                    $server = new Server([
                        'server' => 'http://106.12.25.204:8080',
                        'appId'  => 'easyswoole',
                    ]);
                    $config = new Apollo();
                    $config->setNameSpace([
                        'mysql'
                    ]);
                    $config->setServer($server);
                    $releaseKey = Config::getInstance()->getConf('releaseKey_mysql');
                    $config->sync();
                    //获得原先的config配置项,加载到新的配置项中
                    Config::getInstance()->merge($config->getConf());
                    $oldConfig = Config::getInstance()->getConf();
                    Config::getInstance()->storageHandler($config)->load($oldConfig);
                    //配置版本号不同,则重启框架
                    if ($releaseKey!=null&&$releaseKey!=$config->getReleaseKey('mysql')){
                        (new Reload())->exec(['all',Core::getInstance()->isDev()?'dev':'produce']);
                        Logger::getInstance()->console('重启成功');
                    }
                });
        }
    }
}
