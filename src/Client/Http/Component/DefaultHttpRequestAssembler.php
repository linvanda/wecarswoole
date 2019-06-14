<?php

namespace WecarSwoole\Client\Http\Component;

use WecarSwoole\Client\Config\HttpConfig;
use WecarSwoole\Client\Contract\IHttpRequestAssembler;
use WecarSwoole\Client\Contract\IHttpRequestBean;
use WecarSwoole\Signer\WecarSigner;
use WecarSwoole\Util\Config as UtilConfig;

/**
 * 默认请求组装器
 * 可以通过重写此类的相关方法改写其行为
 * Class DefaultHttpRequestAssembler
 * @package WecarSwoole\Client\Http\Component
 */
class DefaultHttpRequestAssembler implements IHttpRequestAssembler
{
    protected $config;

    public function __construct(HttpConfig $config)
    {
        $this->config = $config;
    }

    public function assemble(array $params): IHttpRequestBean
    {
        $queryParams = $this->parseQueryParams($params);
        $flagParams = $this->parseFlagParams($params);
        $body = $this->parseBody($params);

        $data = $this->reassemble($flagParams, $queryParams, $body);

        return new HttpRequestBean(
            $data['body'],
            $data['flag_params'],
            $data['query_params'],
            $this->parseHeaders($params),
            $this->parseCookies($params)
        );
    }

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

    protected function parseFlagParams(array $params): array
    {
        $params = self::isSimpleStructure($params) ? $params : ($params['flag_params'] ?? $params['query_params'] ?? $params['body']);
        preg_match_all('/{\??([^}]+)}/', $this->config->path, $flags);

        return array_intersect_key($params, array_flip($flags[1]));
    }

    protected function parseBody(array $params): array
    {
        // 如果是 GET 请求，则返回 []
        if ($this->config->method == 'GET') {
            return [];
        }

        $p = self::isSimpleStructure($params) ? $params : ($params['body'] ?? []);

        if (!$p) {
            return [];
        }

        // 剔除 flag params
        return array_diff_key($p, $this->parseFlagParams($params));
    }

    protected function parseQueryParams(array $params): array
    {
        if (self::isSimpleStructure($params)) {
            if ($this->config->method !== 'GET') {
                return [];
            }

            $p = $params;
        } else {
            if ($this->config->method == 'GET') {
                // 合并 query_params 和 body
                $p = $params['query_params'] + $params['body'];
            } else {
                $p = $params['query_params'] ?? [];
            }
        }

        return array_diff_key($p, $this->parseFlagParams($params));
    }

    protected function parseHeaders(array $params): array
    {
        return $params['headers'] ?? [];
    }

    protected function parseCookies(array $params): array
    {
        return $params['cookies'] ?? [];
    }

    protected static function isSimpleStructure(array $params): bool
    {
        foreach ($params as $k => $v) {
            if (in_array($k, ['headers', 'cookies', 'query_params', 'body', 'flag_params']) && is_array($v)) {
                return false;
            }
        }

        return true;
    }
}