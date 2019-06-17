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

class MyDTO extends \EasySwoole\Spl\SplBean
{
    public $name;
    public $age;
    /**
     * @field gender
     * @map 1 => 男, 2 => 女
     */
    public $sex;
    public $loveMan;
}

$bean = new MyDTO(['name' => '三', 'love_man' => '三字', 'gender' => 1]);
var_export($bean->toArray());
