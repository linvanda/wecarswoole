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

$a = new A('三');
$b = clone $a;
$b->name = '四';
echo $a->name;