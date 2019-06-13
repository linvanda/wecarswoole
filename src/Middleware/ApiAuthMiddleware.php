<?php

namespace WecarSwoole\Middleware;

use EasySwoole\Http\Request;
use EasySwoole\Http\Response;
use WecarSwoole\Exceptions\AuthException;
use WecarSwoole\Signer\WecarSigner;
use WecarSwoole\Util\Config;

/**
 * api 鉴权中间件
 * Class ApiAuthMiddleware
 * @package WecarSwoole\Middleware
 */
class ApiAuthMiddleware implements IRouteMiddleware
{
    /**
     * @param Request $request
     * @param Response $response
     * @throws \Exception
     */
    public function handle(Request $request, Response $response)
    {
        $params = $request->getRequestParam();
        $appId = $params['app_id'];
        $token = $params['token'];

        if (!isset($params['data']) || !$appId || !$token) {
            throw new \Exception("请求参数格式不合法：" . print_r($params, true));
        }

        $serverInfo = Config::getServerInfoByAppId($appId);
        if (!$serverInfo) {
            throw new \Exception("app_id 找不到对应的服务配置");
        }

        // 如果没有配 secret 则认为不需要鉴权
        if (!$serverInfo['secret']) {
            return;
        }

        if (!(new WecarSigner())->verify($params, $token, $serverInfo['secret'])) {
            throw new AuthException("token鉴权失败");
        }
    }
}