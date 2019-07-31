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

defined('STORAGE_ROOT') or define('STORAGE_ROOT', \WecarSwoole\Util\File::join(EASYSWOOLE_ROOT, 'template/storage'));
defined('CONFIG_ROOT') or define('CONFIG_ROOT', \WecarSwoole\Util\File::join(EASYSWOOLE_ROOT, 'template/config'));

$config = Config::getInstance()->getConf();
Config::getInstance()->storageHandler(new \WecarSwoole\Config\Config())->load($config);

Config::getInstance()->loadFile(File::join(CONFIG_ROOT, 'config.php'), true);
