<?php

use WecarSwoole\Util\Mock;
use WecarSwoole\Client\Config\HttpConfig;
use Psr\Http\Message\RequestInterface;

$mock = new Mock();

return [
    /**
     * 支持返回完整格式(完整格式必须至少同时有 http_code 和 body)：
     *      [
     *          'http_code' => 200, // http code
     *          'body' => ... // http body，数组或者字符串，或者其他实现了 __toString() 的对象
     *          'headers' => [], // http 响应头
     *          'activate' => 1, // 激活，0表示不再使用该 mock 数据，将请求真实数据
     *      ]
     */
    'weicar:user.info' => [
        'uid' => $mock->number('100-10000'),
        'name' => $mock->cnName()
    ],
    /**
     * 返回闭包
     * 闭包中可以做复杂的处理，比如模拟慢请求，返回 http 错误码等
     * 返回格式同上面
     */
    'weicar:coupon.info' => function (HttpConfig $config, RequestInterface $request) use ($mock) {
        return [
            'cid' => $mock->number('1000, 1000000')
        ];
    }
];
