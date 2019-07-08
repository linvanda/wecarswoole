<?php

namespace WecarSwoole\Client\Http\Middleware;

use Psr\Http\Message\ResponseInterface;
use Swlib\Http\Response;
use WecarSwoole\Client\Config\HttpConfig;
use WecarSwoole\Client\Contract\IHttpRequestBean;
use WecarSwoole\Middleware\Next;
use WecarSwoole\Util\File;

/**
 * 模拟请求
 * Class MockRequestMiddleware
 * @package WecarSwoole\Client\Http\Middleware
 */
class MockRequestMiddleware implements IRequestMiddleware
{
    private static $config;

    public function before(Next $next, HttpConfig $config, IHttpRequestBean $request)
    {
        // 仅 dev 环境才 mock
        if (ENVIRON === 'dev' && ($mockData = $this->getMockData($config, $request)) && $mockData['activate']) {
            // 返回前先执行 next
            $next($config, $request);

            return new Response($mockData['http_code'], $mockData['headers'], $mockData['body']);
        }

        return $next($config, $request);
    }

    public function after(Next $next, HttpConfig $config, IHttpRequestBean $request, ResponseInterface $response)
    {
        // nothing
        return $next($config, $request, $response);
    }

    private function getMockData(HttpConfig $config, IHttpRequestBean $request)
    {
        $mockConf = $this->loadConfig();

        if (!isset($mockConf[$config->apiName])) {
            return null;
        }

        return $this->resolveMockInfo($mockConf[$config->apiName], $config, $request);
    }

    private function resolveMockInfo($mockConf, HttpConfig $config, IHttpRequestBean $request)
    {
        if (is_callable($mockConf)) {
            $mockConf = $mockConf($config, $request);
        }

        if (!$mockConf) {
            return null;
        }

        return $this->formatMockResult($mockConf);
    }

    private function formatMockResult($mockResult)
    {
        if (!is_array($mockResult) || !isset($mockResult['http_code']) || !isset($mockResult['body'])) {
            $mockResult = [
                'http_code' => 200,
                'body' => $mockResult,
            ];
        }

        $mockResult['activate'] = $mockResult['activate'] ?? 1;
        $mockResult['headers'] = $mockResult['headers'] ?? [];

        if (is_array($mockResult['body'])) {
            $mockResult['body'] = json_encode($mockResult['body']);
        } elseif (!is_string($mockResult['body']) &&
            !is_scalar($mockResult['body']) &&
            method_exists($mockResult['body'], '__toString')) {
            $mockResult['body'] = (string)$mockResult['body'];
        }

        return $mockResult;
    }

    private function loadConfig()
    {
        if (self::$config) {
            return self::$config;
        }

        $files = File::scanDirectory(File::join(EASYSWOOLE_ROOT, 'mock/http'));

        if (!$files || !($files = $files['files'])) {
            return [];
        }

        $cfg = [];
        foreach ($files as $mockFilename) {
            $cfg = array_merge($cfg, include_once($mockFilename) ?? []);
        }

        self::$config = $cfg;
        return $cfg;
    }
}
