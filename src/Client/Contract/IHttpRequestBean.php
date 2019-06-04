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
     * 请求头数组
     * @return array
     */
    public function headers(): array;

    /**
     * cookie 数组
     * @return array
     */
    public function cookies(): array;
}