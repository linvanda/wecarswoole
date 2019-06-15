<?php

namespace WecarSwoole\Util;

use \EasySwoole\EasySwoole\Config as EsConfig;

class Config
{
    public static function getServerInfoByAppId(int $appId): array
    {
        static $conf = [];

        if (isset($conf[$appId])) {
            return $conf[$appId];
        }

        foreach (EsConfig::getInstance()->getConf('server') as $alias => $servers) {
            if ($servers['app_id'] == $appId) {
                $conf[$appId] = $servers;
                return $servers;
            }
        }

        return [];
    }
}
