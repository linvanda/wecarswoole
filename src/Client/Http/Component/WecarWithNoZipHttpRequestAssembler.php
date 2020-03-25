<?php

namespace WecarSwoole\Client\Http\Component;

use WecarSwoole\Signer\WecarSigner;

/**
 * 内部使用的组装器，没有 data 字段
 * Class WecarWithNoZipHttpRequestAssembler
 * @package WecarSwoole\Client\Http\Component
 */
class WecarWithNoZipHttpRequestAssembler extends WecarHttpRequestAssembler
{
    protected function combineWithSignature(WecarSigner $signer, array $params): array
    {
        $params['app_id'] = $this->config->appId;
        $params['token'] = $signer->signature($params);

        return $params;
    }
}
