<?php

namespace WecarSwoole\Client\Http\Component;

use WecarSwoole\Client\Config\HttpConfig;
use WecarSwoole\Client\Contract\IHttpRequestAssembler;
use WecarSwoole\Client\Contract\IHttpRequestBean;

/**
 * 默认请求组装器
 * 可以通过重写此类的相关方法改写其行为
 * Class DefaultHttpRequestAssembler
 * @package WecarSwoole\Client\Http\Component
 */
class DefaultHttpRequestAssembler implements IHttpRequestAssembler
{
    protected $config;
    protected $serverParser;

    public function __construct(HttpConfig $config)
    {
        $this->config = $config;
        $this->serverParser = new HttpServerParser($config);
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
            $this->parseCookies($params),
            $this->serverParser->parse()
        );
    }

    /**
     * 默认实现：直接返回
     * 子类可重写该方法以增加自定义的组装规则
     * @param array $flagParams
     * @param array $queryParams
     * @param array $body
     * @return array
     */
    protected function reassemble(array $flagParams, array $queryParams, array $body): array
    {
        return ['flag_params' => $flagParams, 'query_params' => $queryParams, 'body' => $body];
    }

    protected function parseFlagParams(array $params): array
    {
        $params = self::isSimpleStructure($params)
            ? $params : ($params['flag_params'] ?? $params['query_params'] ?? $params['body']);
        preg_match_all('/{\??([^}]+)}/', $this->config->path, $flags);

        return array_intersect_key($params, array_flip($flags[1]));
    }

    protected function parseBody(array $params): array
    {
        // 如果是 GET 请求，则返回 []
        if ($this->config->method == 'GET') {
            return [];
        }

        $param = self::isSimpleStructure($params) ? $params : ($params['body'] ?? []);

        if (!$param) {
            return [];
        }

        // 剔除 flag params
        return array_diff_key($param, $this->parseFlagParams($params));
    }

    protected function parseQueryParams(array $params): array
    {
        if (self::isSimpleStructure($params)) {
            if ($this->config->method !== 'GET') {
                return [];
            }

            $param = $params;
        } else {
            if ($this->config->method == 'GET') {
                // 合并 query_params 和 body
                $param = $params['query_params'] + $params['body'];
            } else {
                $param = $params['query_params'] ?? [];
            }
        }

        return array_diff_key($param, $this->parseFlagParams($params));
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
