#!/usr/bin/env php
<?php

use EasySwoole\EasySwoole\Config;
use WecarSwoole\Util\File;

defined('ENVIRON') or define('ENVIRON', 'dev');
defined('IN_PHAR') or define('IN_PHAR', boolval(\Phar::running(false)));
defined('RUNNING_ROOT') or define('RUNNING_ROOT', realpath(getcwd()));
defined('EASYSWOOLE_ROOT') or define('EASYSWOOLE_ROOT', IN_PHAR ? \Phar::running() : realpath(getcwd()) . '/..');

$file = EASYSWOOLE_ROOT.'/vendor/autoload.php';
if (file_exists($file)) {
    require $file;
}else{
    die("include composer autoload.php fail\n");
}

Config::getInstance()->loadFile(File::join(EASYSWOOLE_ROOT, 'config/config.php'), true);

class Address extends \WecarSwoole\DTO
{
    /**
     * @field city
     */
    public $cityName;
    public $area;
}

class Td extends \WecarSwoole\DTO
{
    public $name;
    /**
     * @field gender
     * @mapping 1=>女,2=>男
     */
    public $sex;
    /**
     * @var Address
     */
    public $address;
}
//
//$arr = [
//        new Td(['name'=>'李四', 'sex'=>1, 'address'=> ['city'=>'深圳','area'=>'落户']]),
//        new Td(['name'=>'王五', 'sex'=>'男', 'address'=>['city'=>'广州', 'area'=>"百余"]]),
//];
//
//$it = new \WecarSwoole\OTA\Collection($arr);

//var_export($it->toArray());

$data = [
    'name' => '张三',
    'gender' => 2,
    'address' => [
            'city' => '广州',
            'area' => '白云'
    ]
];
$dto = new Td($data);

var_export($dto->address->cityName);
var_export($dto->toArray());