<?php
/**
 *  Client 配置中心
 */
return [
    'app_id' => 1000090,
    'server' => [
        'dev' => 'develop.configserver.zhyz.cn:8080',
        'test' => 'test.configserver.zhyz.cn:8080',
        'preview' => 'preview.configserver.zhihuiyouzhan.cn:8080',
        'produce' => 'production.configserver.zhihuiyouzhan.cn:8080',
    ],
    // 需要监听的 namespace
    'namespaces' => [
        'application',
        'fw.appids',
        'fw.modules',
    ]
];
