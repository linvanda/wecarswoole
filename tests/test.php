<?php

include_once './base.php';

class A
{
    public $name;

    public function __construct($name)
    {
        $this->name = $name;
    }
}

$a1 = new A('a');
$a2 = new A('b');
$a3 = new A('a');
$a4 = new A('c');
$a5 = new A('a');
$a6 = new A('b');

$map1 = new \WecarSwoole\Collection\Map(['a' => $a1, 'c' => $a2]);
$map2 = new \WecarSwoole\Collection\Map(['a' => $a3, 'b' => $a6]);

$r = $map1->intersect($map2);

echo "count:".count($r)."\n";
foreach ($r as $k => $v) {
    echo "k:$k -- v:{$v->name}\n";
}