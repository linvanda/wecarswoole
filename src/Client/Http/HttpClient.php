<?php

namespace WecarSwoole\Client\Http;

use Swlib\Http\Cookies;
use WecarSwoole\Client\Config\HttpConfig;
use WecarSwoole\Client\Contract\IClient;
use WecarSwoole\Client\Contract\IHttpRequestAssembler;
use WecarSwoole\Client\Contract\IResponseParser;
use WecarSwoole\Client\Contract\IHttpServerParser;
use WecarSwoole\Client\Response;
use WecarSwoole\Util\Url;
use Swlib\Http\Uri;
use Swlib\Saber;
use Swlib\Http\BufferStream;

/**
 * Http 客户端
 * Class HttpClient
 * @package WecarSwoole\Client\Http
 */
class HttpClient implements IClient
{
    protected $config;
    protected $serverParser;
    protected $requestAssembler;
    protected $responseParser;

    public function __construct(
        HttpConfig $config,
        IHttpServerParser $serverParser,
        IHttpRequestAssembler $requestAssembler,
        IResponseParser $responseParser
    ) {
        $this->config = $config;
        $this->serverParser = $serverParser;
        $this->requestAssembler = $requestAssembler;
        $this->responseParser = $responseParser;
    }

    /**
     * @param array $params
     * @return Response
     */
    public function call(array $params): Response
    {
        $saberConf = [
            'base_uri' => $this->serverParser->parse(),
            'use_pool' => true,
            'timeout' => $this->config->timeout ?: 3,
        ];

        if (strpos($saberConf['base_uri'], 'http') !== 0) {
            $saberConf['base_uri'] = 'http://' . $saberConf['base_uri'];
        }

        // ssl
        if (strpos($saberConf['base_uri'], 'https') === 0) {
            if ($this->config->CAFile) {
                $saberConf['cafile'] = $this->config->CAFile;
            }
            $saberConf['ssl_verify_peer'] = $this->config->sslVerifyPeer ?? false;
            $saberConf['ssl_allow_self_signed'] = $this->config->sslAllowSelfSigned ?? true;
        }

        $saber = Saber::create($saberConf)->psr();
        $saber->withMethod($this->config->method);

        // 解析请求信息
        $requestBean = $this->requestAssembler->assemble($params);

        // 设置 uri
        $saber->withUri(
            new Uri(
                Url::assemble(
                    $this->config->path,
                    $saberConf['base_uri'],
                    $requestBean->getQueryParams(),
                    $requestBean->getFlagParams()
                )
            )
        );

        if ($headers = self::headers($requestBean->headers() ?? [])) {
            $saber->withHeaders($headers);
        }

        if ($requestBean->cookies()) {
            $saber->withHeader('Cookie', (new Cookies($requestBean->cookies()))->toRequestString());
        }

        if ($body = $requestBean->getBody()) {
            if (!is_string($body)) {
                $body = isset($headers['Content-Type']) && $headers['Content-Type'] == 'application/json'
                    ? json_encode($body) : http_build_query($body);
            }
            $saber->withBody(new BufferStream($body));
        }

        // 执行请求
        $result = $saber->exec()->recv();

        $response = new Response(
            $result->getBody()->read($result->getBody()->getSize()),
            $result->getStatusCode(),
            $result->getReasonPhrase()
        );

        // 解析响应数据
        return $this->responseParser->parser($response);
    }

    private function headers(array $headers): array
    {
        $headers = self::formatHeaders($headers);

        if (!isset($headers['Content-Type']) && $this->config->contentType) {
            $headers['Content-Type'] = $this->config->contentType;
        }

        return $headers;
    }

    private static function formatHeaders(array $headers): array
    {
        $data = [];
        foreach ($headers as $key => $header) {
            $data[self::formatHeaderKey($key)] = $header;
        }

        return $data;
    }

    private static function formatHeaderKey(string $key): string
    {
        $k = explode('-', $key);
        $k = array_map(function ($item) {
            return ucfirst($item);
        }, $k);

        return implode('-', $k);
    }
}
