<?php

namespace WecarSwoole\Client\Http\Component;

use WecarSwoole\Exceptions\Exception;
use WecarSwoole\Signer\WecarSigner;
use WecarSwoole\Util\Config as UtilConfig;

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
        $currentServerInfo = UtilConfig::getServerInfoByAppId($this->config->appId);
        $secret = $currentServerInfo['secret'] ?? '';

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
}
