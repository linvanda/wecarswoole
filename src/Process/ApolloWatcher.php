<?php

namespace WecarSwoole\Process;

use EasySwoole\Component\Process\AbstractProcess;
use EasySwoole\EasySwoole\Command\DefaultCommand\Reload;
use EasySwoole\EasySwoole\Core;
use WecarSwoole\Config\Apollo\Client;
use EasySwoole\EasySwoole\Config as EsConfig;
use WecarSwoole\Util\File;

/**
 * apollo 配置变更监控程序
 * Class ApolloWatcher
 * @package WecarSwoole\Process
 */
class ApolloWatcher extends AbstractProcess
{
    /**
     * @param $arg
     * @throws \Throwable
     */
    public function run($arg)
    {
        EsConfig::getInstance()->loadFile(File::join(CONFIG_ROOT, 'apollo.php'), false);
        $apolloConf = EsConfig::getInstance()->getConf('apollo');

        (new Client(
            $apolloConf['server'][ENVIRON],
            $apolloConf['app_id'],
            $apolloConf['namespaces']
        ))->start(function () {
            // 重启服务
            (new Reload())->exec(['all', Core::getInstance()->isDev() ? 'dev' : 'produce']);
        });
    }

    public function onShutDown()
    {
    }

    public function onReceive(string $str)
    {
    }
}
