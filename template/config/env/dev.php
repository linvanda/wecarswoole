<?php

use function WecarSwoole\Config\apollo;

return [
    'test_apollo' => apollo('cache_type'),
    'server' => require __DIR__ . '/dev_server.php'
];
