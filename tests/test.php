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

//class Address extends \WecarSwoole\Entity
//{
//    /**
//     * @field city
//     */
//    public $cityName;
//    public $area;
//    public $country;
//}
//
//class Td extends \WecarSwoole\Entity
//{
//    public $name;
//    /**
//     * @field gender
//     * @mapping 1=>女,2=>男
//     */
//    public $sex;
//    /**
//     * @var Address
//     */
//    public $address;
//    public $age;
//}
//
//$arr = [
//        new Td(['name'=>'李四', 'sex'=>1, 'address'=> ['city'=>'深圳','area'=>'落户']]),
//        new Td(['name'=>'王五', 'sex'=>'男', 'address'=>['city'=>'广州', 'area'=>"百余"]]),
//];
//
//$it = new \WecarSwoole\OTA\Collection($arr);

//var_export($it->toArray());

//$data = [
//    'name' => '张三',
//    'gender' => 2,
//    'city' => '广州',
//    'area' => '白云'
//];
//$dto = new Td();
//$dto->buildFromArray($data, false);
//
//var_export(json_encode($dto->toArray()));
//var_export($dto);
//if (is_string($k) && !ctype_lower($k)) {

//echo ctype_lower('dDddf');

//echo ctype_alpha('abAdfd');

//WecarSwoole\HealthCheck\HealthCheck::watch();

//$b = new \WecarSwoole\HealthCheck\Buckets(4);
//$b->push(3);
//$b->push(6);
//$b->push(4);
//$b->push(1);
//$b->push(8);
//$b->push(8);
//
//var_export($b);

