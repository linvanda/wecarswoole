<?php

use WecarSwoole\CronTabUtil;
use WecarSwoole\Util\Concurrent;
use Swoole\Coroutine as Co;

include_once './base.php';

go(function () {
    $a = $b = $c = 5;
    echo "start:" . time()."\n";
    $r = Concurrent::simpleExec(
        function() use ($a, $b, $c) {
            Co::sleep(1);
            return "$a - $b - $c";
        },
        function () {
            Co::sleep(2);
            return "------";
        },
        function () {
            Co::sleep(1);
            throw new \Exception("我错了", 300);
            // return "h == i == j";
        }
    );
    echo "end:" . time() . "\n";
    Co::sleep(4);

    foreach ($r as $rt) {
        if ($rt instanceof \Throwable) {
            echo $rt->getMessage();
        } else {
            echo $rt;
        }
        echo "\n";
    }
});
