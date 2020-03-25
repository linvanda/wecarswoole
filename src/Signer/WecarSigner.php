<?php

namespace WecarSwoole\Signer;

/**
 * 喂车内部签名器
 * Class WecarSigner
 * @package WecarSwoole\Signer
 */
class WecarSigner
{
    protected $secret;

    public function __construct(string $secret)
    {
        $this->secret = $secret;
    }

    /**
     * 签名
     * @return string
     */
    public function signature(array $params): string
    {
        ksort($params);
        return md5(http_build_query($params) . $this->secret);
    }

    /**
     * 校验
     * @return bool
     */
    public function verify(array $params, string $token): bool
    {
        if (!isset($params['app_id'])) {
            return false;
        }

        if (isset($params['token'])) {
            unset($params['token']);
        }

        return $token == $this->signature($params);
    }
}
