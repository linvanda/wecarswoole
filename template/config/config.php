<?php

use \WecarSwoole\Util\File;

$baseConfig = [
    'app_name' => '应用名称',
    'app_flag' => 'SY', // 应用标识
    // 日志配置，可配置：file（后面对应目录），mailer（后面对应邮件配置）
    'logger' => include_once __DIR__ . '/logger.php',
    // 邮件。可以配多个
    'mailer' => [
        'default' => [
            'host' => 'smtp.exmail.qq.com',
            'username' => 'robot@weicheche.cn',
            'password' => 'Chechewei123'
        ]
    ],
    'concurrent_locker' => [
        'onoff' => 'on',
        'redis' => 'main'
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
    ['cron_config' => require_once __DIR__ . '/cron.php'],
    require_once __DIR__ . '/env/' . ENVIRON . '.php'
);
