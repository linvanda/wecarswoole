<?php

namespace WecarSwoole\Client\Config;

use WecarSwoole\Exceptions\ConfigNotFoundException;
use EasySwoole\EasySwoole\Config as EsConfig;

class Config
{
    public $apiName;
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
        $this->apiName = $apiConf['api_name'] ?? '';
        $this->requestAssembler = $apiConf['request_assembler'];
        $this->responseParser = $apiConf['response_parser'];
        $this->config = $apiConf;
    }

    /**
     * @param string $api
     * @return array
     * @throws ConfigNotFoundException
     * @throws \Exception
     */
    public static function load(string $api): array
    {
        static $confCache = [];
        $apiCacheKey = md5($api);

        if (isset($confCache[$apiCacheKey])) {
            return $confCache[$apiCacheKey];
        }

        list($groupName, $apiName) = self::parseApi($api);
        $conf = EsConfig::getInstance()->getConf('api');

        if (!$conf) {
            throw new ConfigNotFoundException("api");
        }

        // 全局配置
        $globalConf = $conf['config'] ?? [];
        // 组配置
        $groupConf = isset($conf[$groupName]) && isset($conf[$groupName]['config']) ?
            $conf[$groupName]['config'] : [];
        // api 配置
        $apiConf = isset($conf[$groupName]) && isset($conf[$groupName]['api']) ? $conf[$groupName]['api'] : [];
        $apiConf = $apiConf[$apiName] ?? $apiConf['/' . $apiName] ?? $apiConf;

        $protocol = $apiConf['protocol'] ?? $groupConf['protocol'] ?? $globalConf['protocol'] ?? 'http';
        // 协议配置
        $protocolConf = array_merge(
            $globalConf[$protocol] ?? [],
            $groupConf[$protocol] ?? [],
            $apiConf[$protocol] ?? []
        );

        unset($globalConf[$protocol], $groupConf[$protocol], $apiConf[$protocol]);

        $config = array_merge(['api_name' => $api], $globalConf, $groupConf, $protocolConf, $apiConf);
        $config['app_id'] = EsConfig::getInstance()->getConf('app_id');

        $confCache[$apiCacheKey] = $config;

        return $config;
    }

    /**
     * @param string $api
     * @return array ['group' => '...', 'api' => '...']
     * @throws \Exception
     */
    private static function parseApi(string $api): array
    {
        $arr = explode(':', $api, 2);

        if (count($arr) < 2) {
            throw new \Exception("api 格式错误：{$api}");
        }

        return [$arr[0], $arr[1]];
    }
}
