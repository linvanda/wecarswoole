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

$sign = new \WecarSwoole\Signer\WecarSigner();
$appId = 34342;
$secret = "asfasd2938dfj3j";
$params = [
        'name' => '里斯',
        'age' => 12,
        'app_id' => $appId,
        'lover' => '小资',
];

//$token = $sign->signature($params, $secret);
//echo $token."\n";
//echo $sign->verify($token, $params, $secret);

\WecarSwoole\Util\Config::getServerInfoByAppId(10012);
\WecarSwoole\Util\Config::getServerInfoByAppId(10012);
\WecarSwoole\Util\Config::getServerInfoByAppId(10012);