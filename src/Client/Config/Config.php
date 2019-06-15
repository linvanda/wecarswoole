<?php

namespace WecarSwoole\Client\Config;

use WecarSwoole\Exceptions\ConfigNotFoundException;
use EasySwoole\EasySwoole\Config as EsConfig;

class Config
{
    /**
     * 请求协议
     * @var string
     */
    public $protocol;

    /**
     * 当前项目的app_id
     * @var int
     */
    public $appId;

    /**
     * 服务器地址解析器
     * @var string|null
     */
    public $serverParser;

    /**
     * @var string uri 组装器
     */
    public $uriAssembler;

    /**
     * 请求参数组装器
     * @var string|null
     */
    public $requestAssembler;

    /**
     * 响应参数解析器
     * @var string|null
     */
    public $responseParser;

    /**
     * 原始数组
     * @var array
     */
    public $config;

    public function __construct(array $apiConf)
    {
        $this->protocol = $apiConf['protocol'] ?: 'http';
        $this->appId = $apiConf['app_id'];
        $this->requestAssembler = $apiConf['request_assembler'];
        $this->responseParser = $apiConf['response_parser'];
        $this->serverParser = $apiConf['server_parser'];

        $this->config = $apiConf;
    }

    /**
     * @param string $api
     * @return array
     * @throws ConfigNotFoundException
     * @throws \Exception
     */
    public static function load(\string $api): array
    {
        $apiInfo = self::parseApi($api);
        $conf = EsConfig::getInstance()->getConf('api_config');

        if (!$conf) {
            throw new ConfigNotFoundException("api_config");
        }

        $globalConf = $conf['config'] ?? [];
        $groupConf = isset($conf[$apiInfo['group']]) && isset($conf[$apiInfo['group']]['config']) ? $conf[$apiInfo['group']]['config'] : [];
        $apiConf = $conf[$apiInfo['group']]['api'];
        $apiConf = $apiConf[$apiInfo['api']] ?? $apiConf['/' . $apiInfo['api']];

        if (!$apiConf) {
            throw new ConfigNotFoundException("api_config.{$api}");
        }

        $protocol = $apiConf['protocol'] ?? $groupConf['protocol'] ?? $globalConf['protocol'] ?? 'http';
        // 协议配置
        $protocolConf = array_merge($globalConf[$protocol] ?? [], $groupConf[$protocol] ?? [], $apiConf[$protocol] ?? []);

        unset($globalConf[$protocol], $groupConf[$protocol], $apiConf[$protocol]);

        $config = array_merge(['api_name' => $api], $globalConf, $groupConf, $protocolConf, $apiConf);

        return $config;
    }

    /**
     * @param string $api
     * @return array ['group' => '...', 'api' => '...']
     * @throws \Exception
     */
    private static function parseApi(\string $api): array
    {
        $arr = explode(':', $api);

        if (count($arr) !== 2) {
            throw new \Exception("api 格式错误：{$api}");
        }

        return [
            'group' => $arr[0],
            'api' => $arr[1]
        ];
    }
}