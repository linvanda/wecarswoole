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

//class Address extends \WecarSwoole\DTO
//{
//    /**
//     * @field city
//     */
//    public $cityName;
//    public $area;
//    public $country;
//}
//
//class Td extends \WecarSwoole\DTO
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
//        ['name'=>'李四', 'sex'=>1, 'address'=> ['city'=>'深圳','area'=>'落户']],
//        ['name'=>'王五', 'sex'=>'男', 'address'=>['city'=>'广州', 'area'=>"百余"]],
//];
//
//$it = new \WecarSwoole\OTA\DTOCollection(Td::class, $arr);
//
//var_export($it[0]->address->cityName);

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

//set_exception_handler(function ($e) {
//    echo "hahah---";
//});
//
//$dirIterator = new \RecursiveDirectoryIterator("/Users/linvanda");
//$iterator = new \RecursiveIteratorIterator($dirIterator);
//
//foreach ($iterator as $file) {
//    echo $file->getBaseName();
//    echo "\n";
//}

class EmptyObject
{
    private $newThis;
    private $func;

    public function __construct()
    {
        $this->func = function ($name, ...$arguments) {
            if (method_exists($this, $name)) {
                return $this->$name(...$arguments);
            } elseif (property_exists($this, $name)) {
                return $this->{$name};
            }
        };
    }

    public function __call($name, $arguments)
    {
        return $this->func->call($this->newThis, $name, $arguments);
    }

    public function __get($name)
    {
        return $this->func->call($this->newThis, $name);
    }

    public function setObject($obj)
    {
        $this->newThis = $obj;
    }
}

class Middleware
{
    private $proxy;

    public function __construct()
    {
        $this->proxy = new EmptyObject();
    }

    public function __invoke($method, $newThis = null, ...$args)
    {
        $this->proxy->setObject($newThis);
        return $this->$method(...$args);
    }

    public function before()
    {
        return $this->name;
    }

    public function __call($name, $args)
    {
        return $this->proxy->{$name}($args);
    }

    public function __get($name)
    {
        return $this->proxy->{$name};
    }
}


class B
{
    private $name = "BBB";
    private $age = 34;
    private $cars = ['2323423', 'asdfsa'];

    public function getVars($vars)
    {
        return $vars;
    }
}

$b = new B();
$proxy = new \WecarSwoole\Proxy(new B);
//var_export($proxy->getVars([90,89]));