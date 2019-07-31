<?php

namespace Test;
use Swoole\Coroutine as Co;

require_once './base.php';

use Swoole\Runtime;
use WecarSwoole\RedisFactory;


function log($str)
{
    echo "co:".Co::getuid().":$str\n";
}

go(function () {
    Runtime::enableCoroutine();
    for ($i = 0; $i < 10; $i++) {
        go(function () {
            $redis1 = RedisFactory::build('main');

            $redis1->invoke(function (\Redis $redis) {
                $redis->set("hekey_".Co::getuid(), "redis:".Co::getuid());
                $redis->get("hekey_".Co::getuid());
            });
        });
    }
});
