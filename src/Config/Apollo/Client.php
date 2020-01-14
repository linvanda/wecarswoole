<?php

namespace WecarSwoole\Config\Apollo;

use Swlib\Http\Exception\HttpExceptionMask;
use Swlib\Saber;
use WecarSwoole\Config\Config;

/**
 * Apollo 客户端
 * 注意：此处用的 curl，如果不是在单独的进程调用，则必须将curl 协程化，否则会造成堵塞
 * Class Client
 * @package WecarSwoole\Config\Apollo
 */
class Client
{
    protected $configServer; //apollo服务端地址
    protected $appId; //apollo配置项目的appid
    protected $cluster = 'default';
    protected $clientIp; //绑定IP做灰度发布用
    protected $notifications = [];
    protected $pullTimeout = 5; //获取某个namespace配置的请求超时时间
    protected $intervalTimeout = 70; //每次请求获取apollo配置变更时的超时时间

    /**
     * ApolloClient constructor.
     * @param string $server apollo服务端地址
     * @param string $appId apollo配置项目的appid
     * @param array $namespaces apollo配置项目的namespace
     */
    public function __construct(string $server, $appId, array $namespaces)
    {
        $this->configServer = $server;
        $this->appId = $appId;

        foreach ($namespaces as $namespace) {
            $this->notifications[$namespace] = ['namespaceName' => $namespace, 'notificationId' => -1];
        }
    }

    public function setCluster($cluster)
    {
        $this->cluster = $cluster;
    }

    public function setClientIp($ip)
    {
        $this->clientIp = $ip;
    }

    public function setPullTimeout($pullTimeout)
    {
        $pullTimeout = intval($pullTimeout);
        if ($pullTimeout < 1 || $pullTimeout > 300) {
            return;
        }

        $this->pullTimeout = $pullTimeout;
    }

    public function setIntervalTimeout($intervalTimeout)
    {
        $intervalTimeout = intval($intervalTimeout);
        if ($intervalTimeout < 1 || $intervalTimeout > 300) {
            return;
        }

        $this->intervalTimeout = $intervalTimeout;
    }

    /**
     * 启动配置循环监听
     * @param \Closure $callback 有配置更新时的回调
     */
    public function start(\Closure $callback = null)
    {
        do {
            $notifyResults = $this->get($this->getNotifyUrl(), $this->intervalTimeout);

            if ($notifyResults['http_code'] != 200) {
                continue;
            }

            $notifyResults = $notifyResults['response'];
            $changeList = [];
            foreach ($notifyResults as $r) {
                if ($r['notificationId'] != $this->notifications[$r['namespaceName']]['notificationId']) {
                    $changeList[$r['namespaceName']] = $r['notificationId'];
                }
            }

            if (!$changeList) {
                continue;
            }

            $pullRst = $this->pullConfigBatch(array_keys($changeList));

            if ($pullRst['reloaded']) {
                // 有配置变动，需要调用回调函数
                $callback && $callback();
            }

            foreach ($pullRst['list'] as $namespaceName => $result) {
                $result && ($this->notifications[$namespaceName]['notificationId'] = $changeList[$namespaceName]);
            }
        } while (true);
    }

    /**
     * 获取多个namespace的配置-无缓存的方式
     * @param array $namespaces
     * @return array
     */
    private function pullConfigBatch(array $namespaces): array
    {
        if (!$namespaces) {
            return ['list' => [], 'reloaded' => false];
        }

        $responseList = [];
        $reloaded = false;

        $responses = $this->requestAll($this->getPullUrls($namespaces), $this->pullTimeout);

        foreach ($namespaces as $namespace) {
            $responseList[$namespace] = true;
        }

        foreach ($responses as $response) {
            if (!$response['response']) {
                continue;
            }

            $namespace = $response['response']['namespaceName'];
            if ($response['http_code'] == 200) {
                $result = $response['response'];

                if (!is_array($result) || !isset($result['configurations'])) {
                    continue;
                }

                $content = '<?php return ' . var_export($result, true) . ';';
                $this->saveToFile(Config::getApolloCacheFileName($namespace), $content);
                $reloaded = true;
            } elseif ($response['http_code'] != 304) {
                $responseList[$namespace] = false;
            }
        }

        return ['list' => $responseList, 'reloaded' => $reloaded];
    }

    private function saveToFile(string $file, string $content)
    {
        $dir = dirname($file);
        if (!file_exists($dir)) {
            mkdir($dir, 0755, true);
        }
        file_put_contents($file, $content);
    }

    private function getReleaseKey($configFile)
    {
        $releaseKey = '';
        if (file_exists($configFile)) {
            $lastConfig = require_once($configFile);
            is_array($lastConfig) && isset($lastConfig['releaseKey']) && $releaseKey = $lastConfig['releaseKey'];
        }
        return $releaseKey;
    }

    private function getPullUrl(string $namespace): string
    {
        $baseApi = rtrim($this->configServer, '/') . '/configs/' . $this->appId . '/' . $this->cluster . '/';
        $api = $baseApi . $namespace;
        $args = [
            'releaseKey' => $this->getReleaseKey(Config::getApolloCacheFileName($namespace)),
        ];

        if ($this->clientIp) {
            $args['ip'] = $this->clientIp;
        }

        $api .= '?' . http_build_query($args);

        return $api;
    }

    private function getPullUrls(array $namespaces): array
    {
        return array_map(function ($namespace) {
            return $this->getPullUrl($namespace);
        }, $namespaces);
    }

    private function getNotifyUrl(): string
    {
        $params = [
            'appId' => $this->appId,
            'cluster' => $this->cluster,
            'notifications' => json_encode(array_values($this->notifications)),
        ];

        return rtrim($this->configServer, '/') . '/notifications/v2?' . http_build_query($params);
    }

    /**
     * @param string $url
     * @return array
     */
    private function get(string $url, int $timeout): array
    {
        $saber = Saber::create([
            'timeout' => $timeout,
            'exception_report' => HttpExceptionMask::E_NONE
        ]);

        $response = $saber->get($url);
        return [
            'http_code' => $response->getStatusCode(),
            'response' => json_decode(strval($response->getBody()), true)
        ];
    }

    private function requestAll(array $urls, int $timeout): array
    {
        if (!$urls) {
            return [];
        }

        $saber = Saber::create([
            'timeout' => $timeout,
            'exception_report' => HttpExceptionMask::E_NONE
        ]);

        $responseMap = $saber->requests(
            array_map(
                function ($url) {
                    return ['get', $url];
                },
                $urls
            )
        );

        if (!$responseMap) {
            return [];
        }

        return array_map(
            function ($response) {
                return [
                    'http_code' => $response->getStatusCode(),
                    'response' => json_decode(strval($response->getBody()), true)
                ];
            },
            $responseMap->getArrayCopy()
        );
    }
}
