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

class DDD extends \WecarSwoole\Entity
{
    protected $serverName = 'server test';
}

class MyDTO extends \WecarSwoole\Entity
{
    public $name;
    public $age;
    public $sex;
    protected $loveMan;
    protected $teste;
    protected $ddd;

    public function __construct()
    {
        $this->name = '三';
        $this->age = 543;
        $this->sex = true;
        $this->teste = [
                'ageSay' => 33
        ];
        $this->ddd = new DDD();
    }
}

$bean = new MyDTO(['name' => '三', 'love_man' => '三字', 'sex' => 1, 'teste' => ['ageSay' => 33]]);
var_export($bean->toArray(true, false));
