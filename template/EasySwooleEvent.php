<?php

namespace EasySwoole\EasySwoole;

use WecarSwoole\CronTabUtil;
use WecarSwoole\Util\File;
use EasySwoole\EasySwoole\Swoole\EventRegister;
use EasySwoole\EasySwoole\AbstractInterface\Event;
use EasySwoole\Http\Request;
use EasySwoole\Http\Response;
use EasySwoole\Component\Di;
use WecarSwoole\Process\HotReload;
use DI\ContainerBuilder;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class EasySwooleEvent implements Event
{
    public static function initialize()
    {
        date_default_timezone_set('Asia/Shanghai');

        // HTTP 控制器命名空间
        Di::getInstance()->set(SysConst::HTTP_CONTROLLER_NAMESPACE, 'App\\Http\\Controllers\\');
        // 错误处理器
//        Di::getInstance()->set(SysConst::ERROR_HANDLER,function ($errorCode, $description, $file = null, $line = null){
//            echo "error: $description \n";
//        });

        //加载应用配置
        Config::getInstance()->loadFile(File::join(EASYSWOOLE_ROOT, 'config/config.php'), true);

        // 设置 PHP-DI 容器
        $builder = new ContainerBuilder();
        $builder->addDefinitions(File::join(EASYSWOOLE_ROOT, 'config/di/di.php'));
        if (in_array(ENVIRON, ['preview', 'produce'])) {
            $builder->enableCompilation(File::join(EASYSWOOLE_ROOT, 'storage/di'));
            $builder->writeProxiesToFile(true, File::join(EASYSWOOLE_ROOT, 'storage/di/proxies'));
        }
        $container = $builder->build();
        Di::getInstance()->set('di-container', $container);

        // 事件订阅
        $dispatcher = $container->get('SymfonyEventDispatcher');
        if ($subscribers = Config::getInstance()->getConf('subscriber')) {
            foreach ($subscribers as $subscriber) {
                $subCls = new \ReflectionClass($subscriber);
                if ($subCls->isSubclassOf(EventSubscriberInterface::class)) {
                    $dispatcher->addSubscriber($subCls->newInstance());
                }
            }
        }
    }

    /**
     * @param EventRegister $register
     * @throws \WecarSwoole\Exceptions\ConfigNotFoundException
     */
    public static function mainServerCreate(EventRegister $register)
    {
        // 热重启(仅用在非生产环境)
        if (Core::getInstance()->isDev()) {
            ServerManager::getInstance()->getSwooleServer()->addProcess((new HotReload('HotReload', ['disableInotify' => true]))->getProcess());
        }

        // 加载定时任务
        CronTabUtil::register();
    }

    public static function onRequest(Request $request, Response $response): bool
    {
        return true;
    }

    public static function afterRequest(Request $request, Response $response): void
    {
        // TODO: Implement afterAction() method.
    }
}