<?php

include_once './base.php';

use Swlib\SaberGM;

//go(function () {
//    $res = SaberGM::get("http://www.weicheche.cn");
//    var_export($res->getStatusCode());
//});

$file = "/tmp/test/wel/test.log";
$dir = dirname($file);
if (!file_exists($dir)) {
    mkdir($dir, 0755, true);
}
file_put_contents($file, "test hello");
