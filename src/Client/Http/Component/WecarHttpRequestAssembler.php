<?php

namespace WecarSwoole\Client\Http\Component;

use EasySwoole\Component\Context\ContextManager;
use EasySwoole\EasySwoole\Config;
use WecarSwoole\Exceptions\Exception;
use WecarSwoole\Signer\WecarSigner;

/**
 * wecar 内部的请求组装器
 * Class WecarHttpRequestAssembler
 * @package WecarSwoole\Client\Http\Component
 */
class WecarHttpRequestAssembler extends DefaultHttpRequestAssembler
{
    /**
     * @param array $flagParams
     * @param array $queryParams
     * @param array $body
     * @return array
     * @throws Exception
     */
    protected function reassemble(array $flagParams, array $queryParams, array $body): array
    {
        if (!isset($this->config->appId)) {
            throw new Exception("当前项目没有配置合法的app_id");
        }

        // 签名器
        $signer = new WecarSigner();
        $secret = $this->getAppSecret();

        if ($this->config->method === 'GET') {
            $queryParams = $this->combineWithSignature($signer, $secret, $queryParams);
        } else {
            $body = $this->combineWithSignature($signer, $secret, $body);
        }

        return ['flag_params' => $flagParams, 'query_params' => $queryParams, 'body' => $body];
    }

    protected function combineWithSignature(WecarSigner $signer, string $secret, array $params): array
    {
        $params = [
            'app_id' => $this->config->appId,
            'data' => json_encode($params)
        ];
        $params['token'] = $signer->signature($params, $secret);

        return $params;
    }

    /**
     * @return string
     * @throws Exception
     */
    protected function getAppSecret(): string
    {
        $currentAppId = Config::getInstance()->getConf('app_id');

        if (!$currentAppId) {
            throw new Exception("no config:app_id");
        }

        $appInfo = Config::getInstance()->getConf("server.app_ids.$currentAppId");
        if (is_string($appInfo)) {
            $appInfo = json_decode($appInfo, true);
        }

        return $appInfo['secret'] ?? '';
    }

    protected function parseHeaders(array $params): array
    {
        $headers = parent::parseHeaders($params);

        // 增加 request-id 头部(只适用于worker进程，task进程需要自行加入该头部)
        $requestIdKey = Config::getInstance()->getConf('request_id_key') ?: 'wcc-request-id';
        if (!array_key_exists($requestIdKey, $headers)) {
            $requestId = ContextManager::getInstance()->get('wcc-request-id');
            if ($requestId) {
                $headers[$requestIdKey] = $requestId;
            }
        }

        return $headers;
    }
}
