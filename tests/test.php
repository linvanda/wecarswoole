<?php

use WecarSwoole\CronTabUtil;

include_once './base.php';

go(function () {
    $conf = [
        'ip' => '172.16.0.31'
    ];

    echo CronTabUtil::willRunCrontab($conf);
});
