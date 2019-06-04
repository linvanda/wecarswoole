<?php

namespace App\Http\Controllers;

use WecarSwoole\Http\Route;
use EasySwoole\Http\AbstractInterface\AbstractRouter;
use FastRoute\RouteCollector;
use EasySwoole\Http\Request;
use EasySwoole\Http\Response;
use WecarSwoole\Util\File;

/**
 * Http 路由器入口
 * 不要在此处添加具体路由规则，具体的路由在 Routes 目录下按模块定义
 * Class Router
 * @package App\Http\Controllers
 */
class Router extends AbstractRouter
{
    public function initialize(RouteCollector $routeCollector)
    {
        $this->setMethodNotAllowCallBack(function (Request $request,Response $response){
            $response->withStatus(404);
            $response->write('未找到处理方法');
            return false;
        });

        $this->setRouterNotFoundCallBack(function (Request $request,Response $response){
            $response->withStatus(404);
            $response->write('未找到路由匹配');
            return false;
        });

        // 加载具体路由
        $this->loadRoutes($routeCollector);
    }

    /**
     * 加载具体的路由
     * @param RouteCollector $route
     */
    protected function loadRoutes(RouteCollector $route)
    {
        $files = File::scanDirectory(File::join(EASYSWOOLE_ROOT, 'app/Http/Routes'));

        if (!$files || !($files = $files['files'])) {
            return;
        }

        foreach ($files as $routeFileName) {
            $this->loadRoutesFromFile($routeFileName, $route);
        }
    }

    protected function loadRoutesFromFile(string $fileName, RouteCollector $route)
    {
        $class = '\\App\\' . str_replace('/', '\\', str_replace('.php', '', explode('/app/', $fileName)[1]));
        if (basename($fileName) == 'Route.php' || !class_exists($class)) {
            return;
        }

        $instance = (new \ReflectionClass($class))->newInstance($route);
        if ($instance instanceof Route) {
            $instance->map();
        }
    }
}