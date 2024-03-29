<?php

namespace WecarSwoole\Client\Http;

use Psr\Http\Message\ResponseInterface;
use Psr\Log\LoggerInterface;
use Swlib\Http\Cookies;
use Swlib\Http\Exception\HttpExceptionMask;
use WecarSwoole\Client\Config\HttpConfig;
use WecarSwoole\Client\Contract\IClient;
use WecarSwoole\Client\Contract\IHttpRequestAssembler;
use WecarSwoole\Client\Contract\IHttpRequestBean;
use WecarSwoole\Client\Contract\IResponseParser;
use WecarSwoole\Client\Response;
use WecarSwoole\Exceptions\APIInvokeException;
use WecarSwoole\Middleware\MiddlewareHelper;
use WecarSwoole\Util\Url;
use Swlib\Http\Uri;
use Swlib\Saber;
use Swlib\Saber\Request;
use Swlib\Saber\Response as SaberResponse;
use Swlib\Http\BufferStream;
use Swoole\Coroutine;
use WecarSwoole\Container;

/**
 * Http 客户端
 * Class HttpClient
 * @package WecarSwoole\Client\Http
 */
class HttpClient implements IClient
{
    use MiddlewareHelper;

    protected $config;
    protected $requestAssembler;
    protected $responseParser;

    public function __construct(
        HttpConfig $config,
        IHttpRequestAssembler $requestAssembler,
        IResponseParser $responseParser
    ) {
        $this->config = $config;
        $this->requestAssembler = $requestAssembler;
        $this->responseParser = $responseParser;
    }

    /**
     * @param array $params
     * @return Response
     * @throws \WecarSwoole\Exceptions\ParamsCannotBeNullException
     * @throws \WecarSwoole\Exceptions\Exception
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

        // 重试机制
        if ($this->config->retryNum >= 1 && is_callable($this->config->retryFunc)) {
            $saberConf['retry_time'] = $this->config->retryNum;
            $saberConf['retry'] = function (Request $request, SaberResponse $response) {
                $retriedTime = $request->getRetriedTime();
                $sleepSec = call_user_func($this->config->retryFunc, $retriedTime);
                if ($sleepSec) {
                    Coroutine::sleep($sleepSec);
                }

                // 记录日志
                Container::get(LoggerInterface::class)->info("第{$retriedTime}次重试：" . $request->getUri()->__toString());

                return true;
            };
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

        if ($headers = $this->headers($requestBean->headers() ?? [])) {
            $saber->withHeaders($headers);
        }

        if ($requestBean->cookies()) {
            $saber->withHeader('Cookie', (new Cookies($requestBean->cookies()))->toRequestString());
        }

        if ($body = $requestBean->getBody() ?? '') {
            if (!is_string($body)) {
                $body = isset($headers['Content-Type']) && $headers['Content-Type'] == 'application/json'
                    ? json_encode($body) : http_build_query($body);
            }
        } else {
            $body = '';
        }

        $buffer = new BufferStream();
        $buffer->write($body);
        $saber->withBody($buffer);

        $response = $this->execMiddlewares('before', $this->config, $saber);

        $fromRealRequest = false;
        if (!$response instanceof ResponseInterface) {
            $response = $saber->exec()->recv();
            $fromRealRequest = true;
        }

        // 重新设置 body 供后面使用
        $buffer = new BufferStream();
        $buffer->write($body);
        $saber->withBody($buffer);

        $this->execMiddlewares('after', $this->config, $saber, $response);

        $this->dealBadResponse($response, $requestBean);

        // 解析响应数据
        return $this->responseParser->parser(
            new Response(
                $response->getBody()->read($response->getBody()->getSize()),
                $response->getStatusCode(),
                $response->getReasonPhrase(),
                $fromRealRequest
            )
        );
    }

    /**
     * @param ResponseInterface $response
     * @throws \WecarSwoole\Exceptions\Exception
     */
    private function dealBadResponse(ResponseInterface $response, IHttpRequestBean $requestBean)
    {
        // 非 20X 是否需要抛异常
        if ($this->config->throwException && $response->getStatusCode() >= 300) {
            $exception = (
                new APIInvokeException(
                    "接口{$this->config->apiName}调用错误：" . $response->getReasonPhrase(),
                    $response->getStatusCode()
                )
            )->withContext(
                [
                    'uri' => $requestBean->baseUri(),
                    'params' => $requestBean->getParams()
                ]
            );

            if ($response->getStatusCode() === 504) {
                $exception->shouldRetry();
            }

            throw $exception;
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
        $k = array_map(function ($item) {
            return ucfirst($item);
        }, explode('-', $key));

        return implode('-', $k);
    }
}
