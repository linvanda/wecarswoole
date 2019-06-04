<?php

namespace WecarSwoole\Client\Http\Component;

use WecarSwoole\Client\Contract\IHttpRequestBean;

class HttpRequestBean implements IHttpRequestBean
{
    private $flagParams;
    private $queryParams;
    private $body;
    private $headers;
    private $cookies;

    public function __construct(array $body, array $flagParams = [], array $queryParams = [], array $headers = [], array $cookies = [])
    {
        $this->flagParams = $flagParams;
        $this->queryParams = $queryParams;
        $this->body = $body;
        $this->headers = $headers;
        $this->cookies = $cookies;
    }

    /**
     * url 占位符参数
     * @return array
     */
    public function getFlagParams(): array
    {
        return $this->flagParams;
    }

    /**
     * url 查询字符串数组
     * @return array
     */
    public function getQueryParams(): array
    {
        return $this->queryParams;
    }

    /**
     * 请求体
     * @return array
     */
    public function getBody(): array
    {
        return $this->body;
    }

    /**
     * 请求头数组
     * @return array
     */
    public function headers(): array
    {
        return $this->headers;
    }

    /**
     * cookie 数组
     * @return array
     */
    public function cookies(): array
    {
        return $this->cookies;
    }
}