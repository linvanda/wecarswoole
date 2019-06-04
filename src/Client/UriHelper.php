<?php

namespace WecarSwoole\Client;

use WecarSwoole\Exceptions\ParamsCannotBeNullException;

class UriHelper
{
    /**
     * @param string $uri
     * @param string $base
     * @param array $queryParams
     * @param array $flagParams
     * @return mixed|string
     * @throws ParamsCannotBeNullException
     */
    public static function assemble(string $uri, string $base = '', array $queryParams = [], array $flagParams = [])
    {
        $uri = self::replaceFlag($uri, $flagParams);

        if (strpos($uri, 'https:') !== 0 && strpos($uri, 'http:') !== 0 && $base) {
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
     * @param string $url
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
}