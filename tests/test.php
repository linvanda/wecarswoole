<?php

include_once './base.php';

class A
{
    use \WecarSwoole\Util\SetWhenNull;
    use \WecarSwoole\Util\GetterSetter;

    private $name;
    private $age;
    private $loverMan;

    public function __construct($name = '')
    {
        $this->name = $name;
    }
}

$a = new A(null);
$a->setWhenNull([
   'name' => '张三',
   'age' => 34,
   'loverMan' => '松林'
]);

echo \WecarSwoole\Util\Random::str(100);