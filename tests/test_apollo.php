<?php

use EasySwoole\Component\Timer;
use EasySwoole\EasySwoole\Config as EsConfig;
use EasySwoole\ApolloConfig\Server as ApolloServer;
use EasySwoole\EasySwoole\Command\DefaultCommand\Reload;
use EasySwoole\EasySwoole\Core;
use \EasySwoole\EasySwoole\Logger as EasySwooleLogger;
use WecarSwoole\Config\Apollo\Apollo;
use WecarSwoole\Util\File;
use WecarSwoole\Config\Apollo\Client;
use WecarSwoole\Container;
use Psr\Log\LoggerInterface;
use function WecarSwoole\Config\apollo;

require_once './base.php';

go(function () {
    EsConfig::getInstance()->loadFile(File::join(CONFIG_ROOT, 'apollo.php'), false);
    $apolloConf = EsConfig::getInstance()->getConf('apollo');

//    Timer::getInstance()->loop(1000, function() {
//        echo "config value:";
//        var_export(\EasySwoole\EasySwoole\Config::getInstance()->getConf('test_apollo'));
//        echo "\n\n";
//    });

    (new Client(
        $apolloConf['server'][ENVIRON],
        $apolloConf['app_id'],
        $apolloConf['namespaces']
    ))->start(function () {
        echo "refresh conf\n\n";
    });

//    echo "getconf:\n";
//    var_export(apollo('fw.mysql.dw.ro', 'dw_read.host'));
//    var_export(EsConfig::getInstance()->getConf());
//    echo "\n---\n\n";
});
