<?php

use WecarSwoole\CronTabUtil;
use WecarSwoole\Util\Concurrent;
use Swoole\Coroutine as Co;

include_once './base.php';

go(function () {
    echo "start:" . time()."\n";
    $r = Concurrent::instance()
    ->addParams([1, 3, 5], [], ['a', 'b', 'c'])
    ->addTasks(
        function($a, $b, $c) {
            Co::sleep(1);
            return "$a - $b - $c";
        },
        function () {
            Co::sleep(2);
            return "------";
        },
        function ($a, $b, $c) {
            Co::sleep(1);
            throw new \Exception("我错了", 300);
        }
    )->exec();
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
