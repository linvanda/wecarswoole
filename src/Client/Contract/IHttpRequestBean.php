<?php

namespace WecarSwoole\Client\Contract;

/**
 * Http 请求组装器组装结果
 * Interface IHttpRequestBean
 * @package WecarSwoole\Client\Contract
 */
interface IHttpRequestBean
{
    /**
     * url 占位符参数
     * @return array
     */
    public function getFlagParams(): array;

    /**
     * url 查询字符串数组
     * @return array
     */
    public function getQueryParams(): array;

    /**
     * 请求体
     * @return array
     */
    public function getBody(): array;

    /**
     * 获取请求参数信息，包括了 flag_params、query_params 和 body
     * @return array
     */
    public function getParams(): array;

    public function headers(): array;

    public function cookies(): array;

    public function setFlagParams(array $params);

    public function setQueryParams(array $params);

    public function setBody(array $body);

    public function setHeaders(array $headers);

    public function setCookies(array $cookies);

    public function baseUri(): string;

    public function setBaseUri(string $uri);

    public function __toString();
}
