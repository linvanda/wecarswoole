<?php

namespace WecarSwoole\Http\Middlewares;

use App\ErrCode;
use EasySwoole\EasySwoole\Config;
use EasySwoole\Http\Request;
use EasySwoole\Http\Response;
use WecarSwoole\Exceptions\AuthException;
use WecarSwoole\Middleware\Next;
use WecarSwoole\Signer\WecarSigner;
use WecarSwoole\SubServer\Servers;

/**
 * api 鉴权中间件
 * 此处采用喂车内部通用鉴权方案：
 * 1. 请求参数中必须有 app_id、token
 * 2. 通过 app_id 从配置中心获取其对应的 secret
 * 3. 对请求参数中除了 token 以外的参数使用 ksort 排序
 * 4. 对第 3 步得到的数据通过 http_build_query 转换成字符串
 * 5. 将第 4 步获得的字符串和 secret 拼接
 * 6. 取上一步字符串的 md5 值作为 token
 * Class ApiAuthMiddleware
 */
class ApiAuthMiddleware implements IRouteMiddleware
{
    /**
     * @param Request $request
     * @param Response $response
     * @return mixed
     * @throws \Exception
     */
    public function handle(Next $next, Request $request, Response $response)
    {
        // 可通过配置跳过校验（一般用来做临时测试用）
        $auth = Config::getInstance()->getConf("auth_request");
        if ($auth === 0 || $auth === '0') {
            return $next($request, $response);
        }

        $appId = $request->getRequestParam('app_id');
        $token = $request->getRequestParam('token');

        if (!$appId || !$token) {
            throw new AuthException("invalid invoke:no app_id or token.", ErrCode::PARAM_VALIDATE_FAIL);
        }

        if (!$server = Servers::getInstance()->getByAppId($appId)) {
            throw new AuthException("invalid invoke:invalid app_id.", ErrCode::PARAM_VALIDATE_FAIL);
        }
        
        $signer = new WecarSigner($server->secret());
        if (!$signer->verify($request->getRequestParam(), $token)) {
            throw new AuthException("invalid invoke:verify token faild.", ErrCode::AUTH_FAIL);
        }

        return $next($request, $response);
    }
}
