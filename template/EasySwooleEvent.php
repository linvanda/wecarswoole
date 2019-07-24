<?php

namespace EasySwoole\EasySwoole;

use App\Bootstrap;
use Swoole\Server;
use WecarSwoole\CronTabUtil;
use EasySwoole\EasySwoole\Swoole\EventRegister;
use EasySwoole\EasySwoole\AbstractInterface\Event;
use EasySwoole\Http\Request;
use EasySwoole\Http\Response;
use EasySwoole\Component\Di;
use WecarSwoole\Process\ApolloWatcher;
use WecarSwoole\Process\HotReload;
use WecarSwoole\Config\Config as WecarConfig;


class EasySwooleEvent implements Event
{
    public static function initialize()
    {
        date_default_timezone_set('Asia/Shanghai');

        // HTTP 控制器命名空间
        Di::getInstance()->set(SysConst::HTTP_CONTROLLER_NAMESPACE, 'App\\Http\\Controllers\\');

        /**
         * 设置配置存储模式为内存数组
         * easyswoole 默认使用 swoole table 存储，而其设置的字段大小为 1024，加之其存储的实现方式，
         * 有可能会导致 value 超过长度而存储失败
         */
        $oldConfig = Config::getInstance()->getConf();
        Config::getInstance()->storageHandler(new WecarConfig())->load($oldConfig);
    }

    /**
     * @param EventRegister $register
     * @throws \WecarSwoole\Exceptions\ConfigNotFoundException
     */
    public static function mainServerCreate(EventRegister $register)
    {
        // 热重启(仅用在非生产环境)
        if (Core::getInstance()->isDev()) {
            ServerManager::getInstance()->getSwooleServer()->addProcess(
                (new HotReload(
                    'HotReload',
                    ['disableInotify' => true, 'monitorDirs' => [EASYSWOOLE_ROOT . '/app', EASYSWOOLE_ROOT . '/mock']]
                ))->getProcess()
            );
        }

        // worker 进程启动脚本
        $register->add(EventRegister::onWorkerStart, function () {
            Bootstrap::boot();
        });

        // 定时任务
        CronTabUtil::register();

        // Apollo 配置变更监听程序
        ServerManager::getInstance()->getSwooleServer()->addProcess(new ApolloWatcher());
    }

    public static function onRequest(Request $request, Response $response): bool
    {
        return true;
    }

    public static function afterRequest(Request $request, Response $response): void
    {
        //
    }
}
