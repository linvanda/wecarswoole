<?php

include_once './base.php';

interface IM
{
    public function run(\WecarSwoole\Middleware\Next $next, $name, $age);
}

class M1 implements IM
{
    public function run(\WecarSwoole\Middleware\Next $next, $name, $age)
    {
        echo "M:M1。name:$name, age:$age\n";
        $name .= ".M1";
        return $next($name, $age);
    }
}

class M2 implements IM
{
    public function run(\WecarSwoole\Middleware\Next $next, $name, $age)
    {
        echo "M:M2。name:$name, age:$age\n";
        $name .= ".M2";
        return true;
//        return $next($name, $age);
    }
}

class M3 implements IM
{
    public function run(\WecarSwoole\Middleware\Next $next, $name, $age)
    {
        echo "M:M3。name:$name, age:$age\n";
        $name .= ".M3";
        return $next($name, $age);
    }
}

class A
{
    use \WecarSwoole\Middleware\MiddlewareHelper;

    public function __construct()
    {
        $this->appendMiddlewares(
            [
                new M1(),
                new M2(),
                new M3(),
            ]
        );
    }

    public function run()
    {
        $result = $this->execMiddlewares('run', '张三', '男');
        echo "result:\n";
        var_export($result);
    }
}

(new A())->run();