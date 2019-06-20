<?php

namespace WecarSwoole\Client\Http\Component;

use WecarSwoole\Signer\WecarSigner;
use WecarSwoole\Util\Config as UtilConfig;

/**
 * wecar 内部的请求组装器
 * Class WecarHttpRequestAssembler
 * @package WecarSwoole\Client\Http\Component
 */
class WecarHttpRequestAssembler extends DefaultHttpRequestAssembler
{
    protected function reassemble(array $flagParams, array $queryParams, array $body): array
    {
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
