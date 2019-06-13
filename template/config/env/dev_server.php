<?php

/**
 * 子系统配置
 * 建议仅喂车内部的系统作这些配置
 * 其它环境请使用配置中心的配置
 */
return [
    'PY' => [
        'name' => '支付网关',
        'app_id' => 10011,
        'servers'  => [
            ['url' => 'http://gateway.wcc.cn', 'weight' => 100],
        ],
    ],
    'OL' => [
        'name' => '油号',
        'app_id' => 10012,
        'servers' => [
            ['url' => 'http://192.168.85.201:8081', 'weight' => 100],
        ],
    ],
    'YZ' => [
        'name' => '油站',
        'app_id' => 10013,
        'servers' => [
            ['url' => 'http://192.168.85.201:8082', 'weight' => 100],
        ],
    ],
    'DX' => [
        'name' => '短信',
        'app_id' => 10016,
        'servers' => [
            ['url' => 'http://192.168.85.201:8086', 'weight' => 100],
        ],
    ],
    'TS' => [
        'name' => '推送分发',
        'app_id' => 10015,
        'servers' => [
            ['url' => 'http://192.168.85.201:8085', 'weight' => 100],
        ],
    ],
    'YH' => [
        'name' => '用户子系统',
        'app_id' => 10017,
        'servers' => [
            ['url' => 'http://192.168.85.201:8087', 'weight' => 100],
        ],
    ],
    'JY' => [
        'name' => '交易',
        'app_id' => 10014,
        'servers' => [
            ['url' => 'http://192.168.85.201:8083', 'weight' => 100]
        ],
    ],
    'JS' => [
        'name' => '结算',
        'app_id' => 10019,
        'servers' => [
            ['url' => 'http://192.168.85.201:8089', 'weight' => 100],
        ],
    ],
    'JW' => [
        'name' => '结算业务子系统',
        'app_id' => 10021,
        'servers' => [
            ['url' => 'http://192.168.85.201:8091', 'weight' => 100],
        ],
    ],
    'YX' => [
        'name' => '营销',
        'app_id' => 10018,
        'servers' => [
            ['url' => 'http://192.168.85.201:8088', 'weight' => 100],
        ],
    ],
    'DP' => [
        'name' => '异步分发',
        'app_id' => 10022,
        'servers' => [
            ['url' => 'http://192.168.85.201:8092', 'weight' => 100],
        ],
    ],
    'CP' => [
        'name' => '券',
        'app_id' => 10023,
        'servers' => [
            ['url' => 'http://192.168.85.201:8093', 'weight' => 100],
        ],
    ],
    'BL' => [
        'name' => '逻辑业务子系统',
        'app_id' => 10024,
        'servers' => [
            ['url' => 'http://192.168.85.201:8094', 'weight' => 100],
        ],
    ],
    'TR' => [
        'name' => '时间规则',
        'app_id' => 10024,
        'servers' => [
            ['url' => 'http://192.168.85.201:8095', 'weight' => 100],
        ],
    ],
    'TY' => [
        'name' => '推送分发子系统',
        'app_id' => 10026,
        'servers' => [
            ['url' => 'http://192.168.85.201:8085', 'weight' => 100],
        ],
    ],
    'DC' => [
        'name' => '数据中心',
        'app_id' => 10027,
        'servers' => [
            ['url' => 'http://192.168.85.201:8097', 'weight' => 100],
        ],
    ],
    'AY' => [
        'name' => '统计分析',
        'app_id' => 10028,
        'servers' => [
            ['url' => 'http://analy.weiche.cn', 'weight' => 100],
        ],
    ],
    'PS' => [
        'name' => '推送服务',
        'app_id' => 10030,
        'servers' => [
            ['url' => 'http://192.168.85.201:8099', 'weight' => 100],
        ],
        'tcp_servers' => [
            [
                'host' => '192.168.85.201',
                'port' => 9529,
                'weight' => 100,
            ],
        ],
    ],
    'MA' => [
        'name' => '商户端',
        'app_id' => 10112,
        'servers' => [
            ['url' => 'http://dev.mp.api.weicheche.cn', 'weight' => 100],
        ],
    ],
    'SS' => [
        'name' => 'OS',
        'app_id' => 10141,
        'servers' => [
            ['url' => 'http://weios.wecar.me:10842/webservice/ipsrv', 'weight' => 100],
        ],
    ],
    'HD' => [
        'name' => '华大油卡',
        'app_id' => 20000,
        'servers' => [
            ['url' => '110.172.224.11:8111', 'weight' => 100],
        ],
    ],
    'RS' => [
        'name' => '推荐系统',
        'app_id' => 20001,
        'servers' => [
            ['url' => 'http://192.168.85.201:8101', 'weight' => 100],
        ],
    ],
    'WX' => [
        'name' => '微信端',
        'app_id' => 10172,
        'servers' => [
            ['url' => 'http://192.168.85.210:81', 'weight' => 100],
        ],
    ],
    'YY' => [
        'name' => '运营平台',
        'app_id' => 10131,
        'servers' => [
            ['url' => 'http://192.168.85.202:8089', 'weight' => 100],
        ],
    ],
    'IC' => [
        'name' => '用户召回',
        'app_id' => 20002,
        'servers' => [
            ['url' => 'http://192.168.85.201:8103', 'weight' => 100],
        ],
    ],
    'AU' => [
        'name' => '授权系统',
        'app_id' => 20003,
        'servers' => [
            ['url' => 'http://auth.weicheche.cn', 'weight' => 100],
        ],
    ],
    'FP' => [
        'name' => '发票',
        'app_id' => 10155,
        'servers' => [
            ['url' => 'http://192.168.85.119:8199', 'weight' => 100]
        ]
    ],
    'MP' => [
        'name' => '商户平台',
        'app_id' => 10113,
        'secret' => '2iiigbbXfM0VbgpwSCAUpjYbbEZAokLl',
        'servers' => [
            ['url' => 'http://dev2.mp.wcc.cn', 'weight' => 100],
        ],
    ],
    'NE' => [
        'name' => '通知包',
        'app_id' => 20004,
        'secret' => '2iiigbbXfM0VbgpwSCAUpjYbbEZAokLl',
        'servers' => [
            ['url' => 'http://notice-api.weicheche.cn', 'weight' => 100],
        ],
    ]
];