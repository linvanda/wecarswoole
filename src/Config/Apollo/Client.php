<?php

namespace WecarSwoole\Config\Apollo;

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
            $notifyResults = $this->curlGet($this->getNotifyUrl(), $this->intervalTimeout);

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
        // 实际中 namespaces 不是很多，此处依次调用 curl get 获取数据
        foreach ($namespaces as $namespace) {
            $response = $this->curlGet($this->getPullUrl($namespace), $this->pullTimeout);
            $responseList[$namespace] = true;

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
     * 不使用 swoole 的协程 Client，swoole 的协程 Client 在服务器返回 304 时会一直等待直到超时
     * @param string $url
     * @return array
     */
    private function curlGet(string $url, int $timeout): array
    {
        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_TIMEOUT, $timeout);
        curl_setopt($ch, CURLOPT_HEADER, false);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $errorNo = curl_errno($ch);

        if (!$errorNo) {
            $response = json_decode($response, true);
        }

        curl_close($ch);

        return ['error_no' => $errorNo, 'http_code' => $httpCode, 'response' => $response];
    }
}
