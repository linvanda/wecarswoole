<?php

namespace WecarSwoole\Util;

use WecarSwoole\Exceptions\ParamsCannotBeNullException;
use EasySwoole\EasySwoole\Config as ESConfig;

class Url
{
    /**
     * @param string $path
     * @param array $queryParams
     * @param array $flagParams
     * @return string
     * @throws ParamsCannotBeNullException
     */
    public static function realUrl(string $path, array $queryParams = [], array $flagParams = [])
    {
        return self::assemble($path, self::isCompleteUrl($path) ? '' : self::baseUrl(), $queryParams, $flagParams);
    }

    /**
     * 组装 url
     * @param string $uri uri 的 path 部分或者整个 uri，可以使用占位符如 {uid}，{?group_id}（?表示可选参数）
     * @param string $base baseurl
     * @param array $queryParams 查询字符串
     * @param array $flagParams 用于替换 $uri 中的占位符
     * @return string
     * @throws ParamsCannotBeNullException
     */
    public static function assemble(
        string $uri,
        string $base = '',
        array $queryParams = [],
        array $flagParams = []
    ): string {
        $uri = self::replaceFlag($uri, $flagParams);

        if (!self::isCompleteUrl($uri) && $base) {
            $uri = implode('/', [rtrim($base, '/'), ltrim($uri, '/')]);
        }

        if ($queryParams) {
            $queryStr = http_build_query($queryParams);
            $uri = strpos(rtrim($uri, '?'), '?') === false ? $uri . '?' . $queryStr : '&' . $queryStr;
        }

        return $uri;
    }

    /**
     * 解析出 schema,host,path,query_string
     * @param string $url 支持仅传递 path 部分（此时 host 和 schema 为空），支持占位符，如：
     *                    http://www.wcc.cn/user/{uid}?phone=12909090987
     *                    /coupon/{?cid}?type=1
     * @return array
     */
    public static function parse(string $url): array
    {
        $tmpUrl = $url;
        $data = ['schema' => '', 'host' => '', 'path' => '', 'query_string' => ''];

        $arr = explode('://', $tmpUrl);

        if (count($arr) == 2) {
            $data['schema'] = $arr[0];
            $arr = explode('/', $arr[1], 2);
            $data['host'] = $arr[0];
            $tmpUrl = $arr[1];
        }

        // 解析 path 和 query
        $arr = explode('?', str_replace('{?', '--==--', $tmpUrl));
        if (count($arr) == 2) {
            $data['path'] = str_replace('--==--', '{?', $arr[0]);
            $data['query_string'] = str_replace('--==--', '{?', $arr[1]);
        } else {
            $data['path'] = $tmpUrl;
        }

        return $data;
    }

    /**
     * @param string $uri
     * @param array $params
     * @return mixed|string
     * @throws ParamsCannotBeNullException
     */
    private static function replaceFlag(string $uri, array $params = [])
    {
        if (!$params) {
            return $uri;
        }

        if (strpos($uri, '{') === false) {
            return $uri;
        }

        preg_match_all('/{([^}]+)}/', $uri, $flags);

        foreach ($flags[1] as $flag) {
            $must = strpos($flag, '?') !== 0;
            $tflag = $flag;
            if (!$must) {
                $tflag = ltrim($flag, '?');
            } elseif (!array_key_exists($flag, $params)) {
                throw new ParamsCannotBeNullException($tflag);
            }

            $uri = str_replace('{' . $flag . '}', $params[$tflag], $uri);
        }

        return implode('/', array_filter(explode('/', $uri)));
    }

    private static function baseUrl(): string
    {
        return ESConfig::getInstance()->getConf('base_url') ?? '';
    }

    private static function isCompleteUrl(string $uri): bool
    {
        return strpos($uri, 'https:') === 0 || strpos($uri, 'http:') === 0;
    }
}
