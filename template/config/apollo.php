<?php

/**
 *  Client 配置中心
 */
return [
    // 实际项目需重写 app_id（由运维创建 apollo 项目并提供 app_id）
    'app_id' => 0,
    // server 部分一般不要动
    'server' => [
        'dev' => 'http://develop.configserver.zhyz.cn:8080',
        'test' => 'http://test.configserver.zhyz.cn:8080',
        'preview' => 'http://preview.configserver.zhihuiyouzhan.cn:8080',
        'produce' => 'http://production.configserver.zhihuiyouzhan.cn:8080',
    ],
    // 需要监听的 namespace
    'namespaces' => [
        'application',
        'fw.appids',
        'fw.modules',
        'fw.mail',
    ]
];
