<?php
/**
 *  Client 配置中心
 */
return [
    'app_id' => 1000090,
    'server' => [
        'dev' => 'http://192.168.85.203:8080',
        'test' => '192.168.85.201:8080',
        'preview' => '119.23.146.197:8080',
        'produce' => '120.78.9.114:8080',
    ],
    // 需要监听的 namespace
    'namespaces' => [
        'application',
        'fw.mysql.weicheche.rw',
        'fw.mysql.fw_coupon.ro',
        'fw.mysql.invoice_db.rw',
        'fw.mysql.dw.ro',
        'fw.redis.01'
    ]
];
