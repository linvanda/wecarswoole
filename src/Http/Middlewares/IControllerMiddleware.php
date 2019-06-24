<?php

namespace WecarSwoole\Http\Middlewares;

use EasySwoole\Http\Request;
use EasySwoole\Http\Response;
use WecarSwoole\Http\Controller;

/**
 * Http 控制器中间件
 * Interface IControllerMiddleware
 */
interface IControllerMiddleware
{
    /**
     * 请求前
     * 返回 false 则停止后续执行（包括控制器 action）
     * @param Controller $controller
     * @param Request $request
     * @param Response $response
     * @return mixed
     */
    public function before(Controller $controller, Request $request, Response $response);

    /**
     * 请求后
     * @param Controller $controller
     * @param Request $request
     * @param Response $response
     * @return mixed
     */
    public function after(Controller $controller, Request $request, Response $response);

    /**
     * 请求结束前的回收。因为控制器使用的是对象池，因而控制器中间件会被多次请求共用，
     * 如果在中间件中保存了请求相关信息，需要在该方法中清理，否则会影响后续请求
     * @return mixed
     */
    public function gc();
}
