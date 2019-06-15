<?php

namespace WecarSwoole\Util;

use EasySwoole\Utility\File as EsFile;

class File extends EsFile
{
    /**
     * 拼接目录/文件
     * @param array ...$paths
     * @return string
     */
    public static function join(...$paths): string
    {
        return (strtoupper(substr(PHP_OS, 0, 3)) === 'WIN' ? '' : '/')
            . implode('/', array_map(function ($path) {
                return trim($path, '/');
            }, $paths));
    }
}
