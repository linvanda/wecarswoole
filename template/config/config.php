<?php

use function WecarSwoole\Config\apollo;

$baseConfig = [
    'app_name' => '应用名称',
    // 应用标识，必填
    'app_flag' => 'SY',
    // 应用id，必填
    'app_id' => '1000000',
    // 可用 server 列表，必填
    'server' => [
        'modules' => apollo('fw.modules'),
        'app_ids' => apollo('fw.appids'),
    ],
    // 邮件。可以配多个
    'mailer' => [
        'default' => [
            'host' => 'smtp.exmail.qq.com',
            'username' => 'robot@weicheche.cn',
            'password' => 'Chechewei123'
        ]
    ],
    'redis' => [
        'main' => [
            'host' => 'db.redis.wcc.cn',
            'port' => 6379,
            'auth' => 'XEXeh1l6nT3wHL0z',
            // 连接池配置
            '__pool' => [
                'max_object_num' => 10,
                'min_object_num' => 1,
                'max_idle_time' => 60,
            ]
        ],
    ],
    // 并发锁配置
    'concurrent_locker' => [
        'onoff' => 'off',
        "redis" => ''
    ],
    // 请求日志配置。默认是关闭的，如果项目需要开启，则自行修改为 on
    'request_log' => [
        'onoff' => 'off',
        // 记录哪些请求类型的日志
        'methods' => ['POST', 'GET', 'PUT', 'DELETE']
    ],
    'wcc_private_key' => '-----BEGIN PRIVATE KEY-----
MIIBVQIBADANBgkqhkiG9w0BAQEFAASCAT8wggE7AgEAAkEApFFwW79DIfUCw4t9
0sww59XroXRYbLCDyab8zdR0rBZ1pDIxx8ABuqs8no5+Y0mZGkBdwqlH5/wFVgwX
+zG+gQIDAQABAkEAlSG0sBAuhasxDviTAbaAzGjCqo5Fkp/BfEsqNkUUfvmO6L2Q
XG27qeUmAacVjbZBlhacdZhXhtBBt6fVIMvxYQIhANMkO7FAVpmIvYa416QVBYdX
brMSLWNKXJI83z7mIpDPAiEAxzqHfUimoFID9DCDNWQ3igv8URRz/HI0kjmabHKJ
b68CIQCEBcz5aWR8/l6b5eqYo7hgR1Bl0kDlK/M0UbG6L8Z/SwIgNdvPxwG98fda
FEiNIADwtsQYuP6TgHqLVcB2y7yHBQcCIG7g1jRCQcb6yNlu+dyb35Adf+6IIanU
qQqo/Ja8ohKN
-----END PRIVATE KEY-----
',
    'wcc_public_key' => '-----BEGIN PUBLIC KEY-----
MFwwDQYJKoZIhvcNAQEBBQADSwAwSAJBAKRRcFu/QyH1AsOLfdLMMOfV66F0WGyw
g8mm/M3UdKwWdaQyMcfAAbqrPJ6OfmNJmRpAXcKpR+f8BVYMF/sxvoECAwEAAQ==
-----END PUBLIC KEY-----
',
    'ss_public_key' => '-----BEGIN PUBLIC KEY-----
MIGfMA0GCSqGSIb3DQEBAQUAA4GNADCBiQKBgQDWP1vtrNLKoh0IqqtNRjkmC1vm
z/mVIZ58QnAjD/ZmYockGjkts8N1knvFRZuHenY20wLMmlFtdXKAix5QBUTUQpoA
EcKu/hieK53nHd9WTz5ht1Au1HM+DR359Wm43TNpSeYniSJGpoRG8t3QBebB3VYy
tIBhommmFXw6U9owvQIDAQAB
-----END PUBLIC KEY-----
',
];

return array_merge(
    $baseConfig,
    ['logger' => include_once __DIR__ . '/logger.php'],
    ['api' => require_once __DIR__ . '/api/api.php'],
    ['subscriber' => require_once __DIR__ . '/subscriber/subscriber.php'],
    require_once __DIR__ . '/env/' . ENVIRON . '.php'
);
