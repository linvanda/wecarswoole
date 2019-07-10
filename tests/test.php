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

echo in_array(0, [null, '', []], true);