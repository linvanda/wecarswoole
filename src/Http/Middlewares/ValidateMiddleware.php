<?php

namespace WecarSwoole\Http\Middlewares;

use EasySwoole\Http\Request;
use EasySwoole\Http\Response;
use EasySwoole\Validate\Validate;
use WecarSwoole\ErrCode;
use WecarSwoole\Exceptions\ValidateException;
use WecarSwoole\Http\Controller;
use WecarSwoole\Middleware\Middleware;
use WecarSwoole\Middleware\Next;

/**
 * 验证器中间件
 * Class ValidateMiddleware
 * @package WecarSwoole\Http\Middlewares
 */
class ValidateMiddleware extends Middleware implements IControllerMiddleware
{
    public function __construct(Controller $controller)
    {
        parent::__construct($controller);
    }

    /**
     * @param Request $request
     * @param Response $response
     * @return bool|mixed
     * @throws ValidateException
     */
    public function before(Next $next, Request $request, Response $response)
    {
        if (!($rules = $this->proxy->validateRules())) {
            goto last;
        }

        $action = basename(explode('?', $request->getRequestTarget())[0]);
        if (!array_key_exists($action, $rules)) {
            goto last;
        }

        $validate = new Validate();
        foreach ($this->formatRules($rules[$action]) as $paramName => $paramRules) {
            $ruleObj = $validate->addColumn($paramName);
            // 添加该字段的规则
            foreach ($paramRules as $ruleName => $ruleOpts) {
                if (method_exists($ruleObj, $ruleName)) {
                    $ruleObj->{$ruleName}(...$ruleOpts);
                }
            }
        }

        // 执行验证
        if (!$this->proxy->validate($validate)) {
            throw new ValidateException($validate->getError()->getErrorRuleMsg(), ErrCode::PARAM_VALIDATE_FAIL);
        }

        last:
        return $next($request, $response);
    }

    public function after(Next $next, Request $request, Response $response)
    {
        return $next($request, $response);
    }

    public function gc(Next $next)
    {
        return $next();
    }

    private function formatRules(array $rules): array
    {
        $formattedRules = [];
        foreach ($rules as $paramName => $paramRules) {
            $formattedRules[$paramName] = $this->formatParamRules($paramRules);
        }

        return $formattedRules;
    }

    private function formatParamRules(array $paramRules): array
    {
        $pRules = [];
        foreach ($paramRules as $k => $v) {
            if (!is_array($v)) {
                if (is_int($k)) {
                    $pRules[$v] = [null, null];
                } else {
                    $pRules[$k] = [$v];
                }
                continue;
            }


            $pRules[$k] = $this->formatRuleOptions($k, $v);
        }

        return $pRules;
    }

    private function formatRuleOptions(string $ruleName, array $ruleOpts): array
    {
        if (in_array($ruleName, ['inArray', 'notInArray'])) {
            // 这两个要特殊处理
            return $this->formatInArrayOpt($ruleOpts);
        }

        if (!$ruleOpts['arg'] && !$ruleOpts['msg']) {
            return is_array($ruleOpts) ? $ruleOpts : [$ruleOpts];
        }

        $rtn = isset($ruleOpts['arg']) ? [$ruleOpts['arg']] : [];
        if ($ruleOpts['msg']) {
            $rtn[] = $ruleOpts['msg'];
        }

        return $rtn;
    }

    private function formatInArrayOpt(array $ruleOpt): array
    {
        $arg = $ruleOpt['arg'] ?? (isset($ruleOpt['msg']) ? null : $ruleOpt);
        $msg = $ruleOpt['msg'] ?? null;
        $rtn = [];

        if ($arg) {
            if (count($arg) == 2 && is_array($arg[0]) && is_bool($arg[1])) {
                $rtn = $arg;
            } else {
                $rtn[] = $arg;
            }
        }

        if ($msg) {
            $rtn[] = $msg;
        }

        return $rtn;
    }
}
