<?php

namespace WecarSwoole\Signer;

/**
 * 喂车内部签名器
 * Class WecarSigner
 * @package WecarSwoole\Signer
 */
class WecarSigner
{
    public function signature(array $params, \string $secret): \string
    {
        ksort($params);
        return md5(http_build_query($params) . $secret);
    }

    public function verify(array $params, \string $token, \string $secret): bool
    {
        if (!$params['app_id']) {
            return false;
        }

        if ($params['token']) {
            unset($params['token']);
        }

        return $token == $this->signature($params, $secret);
    }
}
