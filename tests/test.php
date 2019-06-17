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

class Id extends \WecarSwoole\Entity
{
    protected $id;
    protected $type;

    public function __construct(string $id, string $type)
    {
        $this->id = $id;
        $this->type = $type;
    }
}

class User extends \WecarSwoole\Entity
{
    protected $name;
    protected $age;
    protected $sex;
    protected $id;

    public function __construct($name, $age, $sex, $idArr)
    {
        $this->name = $name;
        $this->age = $age;
        $this->sex = $sex;
        $this->id = new Id($idArr['id'], $idArr['type']);
    }
}

$userDTO = new User("张三", 12, '男', ['id' => '2342112213', 'type' => '身份证']);
var_export($userDTO->toArray(true, true, true));


