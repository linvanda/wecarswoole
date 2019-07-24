<?php

namespace WecarSwoole\Config;

use EasySwoole\Config\SplArrayConfig;
use WecarSwoole\Util\File;
use EasySwoole\EasySwoole\Config as EsConfig;

/**
 * 配置 handle，增加对 apollo 的支持
 * Class Config
 * @package WecarSwoole\Config
 */
class Config extends SplArrayConfig
{
    public function __construct(bool $isDev = true)
    {
        parent::__construct($isDev);
    }

    public function load(array $array): bool
    {
        parent::load($array);

        // 加载 apollo 本地配置
        $this->loadApolloLocalConfig();

        return true;
    }

    public static function getKeyByNamespace(string $namespace): string
    {
        return "__apollo__$namespace";
    }

    public static function getKeyForReleaseByNamespace(string $namespace): string
    {
        return "__releasekey__$namespace";
    }

    public static function getApolloLocalCacheDir()
    {
        return File::join(STORAGE_ROOT, 'apollo');
    }

    public static function getApolloCacheFileName(string $namespace): string
    {
        return File::join(self::getApolloLocalCacheDir(), '__apollo__' . $namespace . ".php");
    }

    protected function loadApolloLocalConfig()
    {
        $files = File::scanDirectory(self::getApolloLocalCacheDir())['files'];

        if (!$files) {
            return;
        }

        $confs = [];
        foreach ($files as $namespaceFile) {
            if (strpos(basename($namespaceFile), '__apollo__') !== 0) {
                continue;
            }

            $nsArr = include($namespaceFile);
            if (!is_array($nsArr) || !isset($nsArr['configurations']) || !isset($nsArr['releaseKey'])) {
                continue;
            }

            $confs[self::getKeyByNamespace($nsArr['namespaceName'])] = $nsArr['configurations'];
            $confs[self::getKeyForReleaseByNamespace($nsArr['namespaceName'])] = $nsArr['releaseKey'];
        }

        if (!$confs) {
            return;
        }

        foreach ($confs as $key => $value) {
            $this->setConf($key, $value);
        }
    }
}

/**
 * 助手方法：获取 apollo 配置中心的配置信息
 */
if (!function_exists('\WecarSwoole\Config')) {
    function apollo($namespace, $key = null)
    {
        $nsConf = EsConfig::getInstance()->getConf(Config::getKeyByNamespace($namespace));

        if (!$nsConf) {
            return null;
        }

        if (!$key) {
            return $nsConf;
        }

        return $nsConf[$key] ?? null;
    }
}
