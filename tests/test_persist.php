#!/usr/bin/env php
<?php

namespace Test;

use WecarSwoole\AbstractInterface\IPersistable;
use WecarSwoole\Util\Str;

error_reporting(~E_NOTICE);

defined('IN_PHAR') or define('IN_PHAR', boolval(\Phar::running(false)));
defined('RUNNING_ROOT') or define('RUNNING_ROOT', realpath(getcwd()));
defined('EASYSWOOLE_ROOT') or define('EASYSWOOLE_ROOT', IN_PHAR ? \Phar::running() : realpath(getcwd()) . '/..');

$file = EASYSWOOLE_ROOT.'/vendor/autoload.php';
if (file_exists($file)) {
    require $file;
}else{
    die("include composer autoload.php fail\n");
}

$entity = new TestEntity("里斯", '女', '1989-01-09');

var_export($entity->named);