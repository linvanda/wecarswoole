<?php

namespace WecarSwoole\Client\Http\Component;

use WecarSwoole\Client\Contract\IHttpRequestBean;

class HttpRequestBean implements IHttpRequestBean
{
    private $baseUri;
    private $flagParams;
    private $queryParams;
    private $body;
    private $headers;
    private $cookies;

    public function __construct(
        array $body = [],
        array $flagParams = [],
        array $queryParams = [],
        array $headers = [],
        array $cookies = [],
        string $baseUri = ''
    ) {
        $this->flagParams = $flagParams;
        $this->queryParams = $queryParams;
        $this->body = $body;
        $this->headers = $headers;
        $this->cookies = $cookies;
        $this->baseUri = $baseUri;
    }

    /**
     * url 占位符参数
     * @return array
     */
    public function getFlagParams(): array
    {
        return $this->flagParams;
    }

    public function setFlagParams(array $params)
    {
        $this->flagParams = $params;
    }

    /**
     * url 查询字符串数组
     * @return array
     */
    public function getQueryParams(): array
    {
        return $this->queryParams;
    }

    public function setQueryParams(array $params)
    {
        $this->queryParams = $params;
    }

    /**
     * 请求体
     * @return array
     */
    public function getBody(): array
    {
        return $this->body;
    }

    public function setBody(array $body)
    {
        $this->body = $body;
    }

    /**
     * 请求头数组
     * @return array
     */
    public function headers(): array
    {
        return $this->headers;
    }

    public function setHeaders(array $headers)
    {
        $this->headers = $headers;
    }

    /**
     * cookie 数组
     * @return array
     */
    public function cookies(): array
    {
        return $this->cookies;
    }

    public function setCookies(array $cookies)
    {
        $this->cookies = $cookies;
    }

    public function baseUri(): string
    {
        return $this->baseUri;
    }

    public function setBaseUri(string $uri)
    {
        $this->baseUri = $uri;
    }

    public function __toString()
    {
        return json_encode(
            [
                'base_uri' => $this->baseUri(),
                'body' => $this->getBody(),
                'flag_params' => $this->getFlagParams(),
                'query_params' => $this->getQueryParams(),
            ],
            JSON_UNESCAPED_UNICODE
        );
    }
}
