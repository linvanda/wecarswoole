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
            $queryParams = [
                'app_id' => $this->config->appId,
                'data' => json_encode($queryParams)
            ];
            $queryParams['token'] = $signer->signature($queryParams, $secret);
        } else {
            $body = [
                'app_id' => $this->config->appId,
                'data' => json_encode($body)
            ];
            $body['token'] = $signer->signature($body, $secret);
        }

        return ['flag_params' => $flagParams, 'query_params' => $queryParams, 'body' => $body];
    }
}
