<?php

/**
 * 子系统配置
 * 建议仅喂车内部的系统作这些配置
 */
$commonInfo = require __DIR__ . '/server_common_info.php';

return [
    'PY' => [
        'name' => $commonInfo['PY']['name'],
        'app_id' => $commonInfo['PY']['app_id'],
        'servers'  => [
            ['url' => 'http://gateway.wcc.cn', 'weight' => 100],
        ],
    ],
    'OL' => [
        'name' => '油号',
        'app_id' => $commonInfo['OL'],
        'servers' => [
            ['url' => 'http://192.168.85.201:8081', 'weight' => 100],
        ],
    ],
    'YZ' => [
        'name' => '油站',
        'app_id' => $commonInfo['YZ'],
        'servers' => [
            ['url' => 'http://192.168.85.201:8082', 'weight' => 100],
        ],
    ],
    'DX' => [
        'name' => '短信模块',
        'app_id' => $commonInfo['DX'],
        'servers' => [
            ['url' => 'http://192.168.85.201:8086', 'weight' => 100],
        ],
    ],
    'TS' => [
        'name' => '推送分发',
        'app_id' => $commonInfo['TS'],
        'servers' => [
            ['url' => 'http://192.168.85.201:8085', 'weight' => 100],
        ],
    ],
    'YH' => [
        'name' => '用户',
        'app_id' => $commonInfo['YH'],
        'servers' => [
            ['url' => 'http://192.168.85.201:8087', 'weight' => 100],
        ],
    ],
    'JY' => [
        'name' => '交易',
        'app_id' => $commonInfo['JY'],
        'servers' => [
            ['url' => 'http://192.168.85.201:8083', 'weight' => 100]
        ],
    ],
    'JS' => [
        'name' => '结算',
        'app_id' => $commonInfo['JS'],
        'servers' => [
            ['url' => 'http://192.168.85.201:8089', 'weight' => 100],
        ],
    ],
    'JW' => [
        'name' => '结算业务',
        'app_id' => $commonInfo['JW'],
        'servers' => [
            ['url' => 'http://192.168.85.201:8091', 'weight' => 100],
        ],
    ],
    'YX' => [
        'name' => '营销',
        'app_id' => $commonInfo['YX'],
        'servers' => [
            ['url' => 'http://192.168.85.201:8088', 'weight' => 100],
        ],
    ],
    'DP' => [
        'name' => '异步分发',
        'app_id' => $commonInfo['DP'],
        'servers' => [
            ['url' => 'http://192.168.85.201:8092', 'weight' => 100],
        ],
    ],
    'CP' => [
        'name' => '抵用券',
        'app_id' => $commonInfo['CP'],
        'servers' => [
            ['url' => 'http://192.168.85.201:8093', 'weight' => 100],
        ],
    ],
    'BL' => [
        'name' => '逻辑业务',
        'app_id' => $commonInfo['BL'],
        'servers' => [
            ['url' => 'http://192.168.85.201:8094', 'weight' => 100],
        ],
    ],
    'TR' => [
        'name' => '时间规则',
        'app_id' => $commonInfo['TR'],
        'servers' => [
            ['url' => 'http://192.168.85.201:8095', 'weight' => 100],
        ],
    ],
    'TY' => [
        'name' => '推送分发(业务)',
        'app_id' => $commonInfo['TY'],
        'servers' => [
            ['url' => 'http://192.168.85.201:8085', 'weight' => 100],
        ],
    ],
    'DC' => [
        'name' => '数据中心',
        'app_id' => $commonInfo['DC'],
        'servers' => [
            ['url' => 'http://192.168.85.201:8097', 'weight' => 100],
        ],
    ],
    'AY' => [
        'name' => '统计分析',
        'app_id' => $commonInfo['AY'],
        'servers' => [
            ['url' => 'http://analy.weiche.cn', 'weight' => 100],
        ],
    ],
    'PS' => [
        'name' => '推送服务',
        'app_id' => $commonInfo['PS'],
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
        'name' => '商户端API',
        'app_id' => $commonInfo['MA'],
        'servers' => [
            ['url' => 'http://dev.mp.api.weicheche.cn', 'weight' => 100],
        ],
    ],
    'SS' => [
        'name' => '零管系统',
        'app_id' => $commonInfo['SS'],
        'servers' => [
            ['url' => 'http://weios.wecar.me:10842/webservice/ipsrv', 'weight' => 100],
        ],
    ],
    'HD' => [
        'name' => '华大油卡',
        'app_id' => $commonInfo['HD'],
        'servers' => [
            ['url' => '110.172.224.11:8111', 'weight' => 100],
        ],
    ],
    'RS' => [
        'name' => '推荐系统',
        'app_id' => $commonInfo['RS'],
        'servers' => [
            //['url' => 'http://120.25.203.147:8101', 'weight' => 100],
            ['url' => 'http://192.168.85.201:8101', 'weight' => 100],
        ],
    ],
    'WX' => [
        'name' => '微信端API',
        'app_id' => $commonInfo['WX'],
        'servers' => [
            ['url' => 'http://192.168.85.210:81', 'weight' => 100],
        ],
    ],
    'YY' => [
        'name' => '运营平台',
        'app_id' => $commonInfo['YY'],
        'servers' => [
            ['url' => 'http://192.168.85.202:8089', 'weight' => 100],
        ],
    ],
    'IC' => [
        'name' => '用户找回',
        'app_id' => $commonInfo['IC'],
        'servers' => [
            ['url' => 'http://192.168.85.201:8103', 'weight' => 100],
        ],
    ],
    'AU' => [
        'name' => '授权系统',
        'app_id' => $commonInfo['AU'],
        'servers' => [
            ['url' => 'http://auth.weicheche.cn', 'weight' => 100],
        ],
    ],
    'FP' => [
        'name' => '发票系统',
        'app_id' => $commonInfo['FP'],
        'servers' => [
            ['url' => 'http://192.168.85.119:8199', 'weight' => 100]
        ]
    ],
    'MP' => [
        'name' => '商户平台',
        'app_id' => $commonInfo['MP'],
        'servers' => [
            ['url' => 'http://dev2.mp.wcc.cn', 'weight' => 100],
        ],
    ],
    'NE' => [
        'name' => '通知接口',
        'app_id' => $commonInfo['NE'],
        'servers' => [
            ['url' => 'http://notice-api.weicheche.cn', 'weight' => 100],
        ],
    ],
];