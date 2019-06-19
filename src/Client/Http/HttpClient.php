<?php

namespace WecarSwoole\Client\Http;

use Psr\Http\Message\ResponseInterface;
use Swlib\Http\Cookies;
use Swlib\Http\Exception\HttpExceptionMask;
use WecarSwoole\Client\Config\HttpConfig;
use WecarSwoole\Client\Contract\IClient;
use WecarSwoole\Client\Contract\IHttpRequestAssembler;
use WecarSwoole\Client\Contract\IHttpRequestBean;
use WecarSwoole\Client\Contract\IResponseParser;
use WecarSwoole\Client\Http\Hook\IRequestDecorator;
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
    protected $requestAssembler;
    protected $responseParser;
    protected $hooks;

    public function __construct(
        HttpConfig $config,
        IHttpRequestAssembler $requestAssembler,
        IResponseParser $responseParser,
        array $hooks = []
    ) {
        $this->config = $config;
        $this->requestAssembler = $requestAssembler;
        $this->responseParser = $responseParser;
        $this->hooks = $hooks;
    }

    /**
     * @param array $params
     * @return Response
     */
    public function call(array $params): Response
    {
        // 解析请求信息
        $requestBean = $this->requestAssembler->assemble($params);

        $saberConf = [
            'base_uri' => $requestBean->baseUri(),
            'use_pool' => true,
            'timeout' => $this->config->timeout ?? 3,
        ];

        // ssl
        if (strpos($saberConf['base_uri'], 'https') === 0) {
            if ($this->config->CAFile) {
                $saberConf['cafile'] = $this->config->CAFile;
            }
            $saberConf['ssl_verify_peer'] = $this->config->sslVerifyPeer ?? false;
            $saberConf['ssl_allow_self_signed'] = $this->config->sslAllowSelfSigned ?? true;
        }

        $saber = Saber::create($saberConf)->psr();
        $saber->setExceptionReport(HttpExceptionMask::E_NONE);

        $saber->withMethod($this->config->method);

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
        if (!$this->onBefore($requestBean)) {
            return new Response();
        }

        $response = $saber->exec()->recv();

        $this->onAfter($requestBean, $response);

        // 解析响应数据
        return $this->responseParser->parser(
            new Response(
                $response->getBody()->read($response->getBody()->getSize()),
                $response->getStatusCode(),
                $response->getReasonPhrase()
            )
        );
    }

    private function onBefore(IHttpRequestBean $request): bool
    {
        foreach ($this->hooks as $hook) {
            if (!$hook instanceof IRequestDecorator) {
                continue;
            }

            if ($hook->before($this->config, $request) === false) {
                return false;
            }
        }

        return true;
    }

    private function onAfter(IHttpRequestBean $request, ResponseInterface $response)
    {
        foreach ($this->hooks as $hook) {
            if (!$hook instanceof IRequestDecorator) {
                continue;
            }

            $hook->after($this->config, $request, $response);
        }
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
