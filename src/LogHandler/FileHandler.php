<?php

namespace WecarSwoole\LogHandler;

use Monolog\Handler\StreamHandler;

class FileHandler extends StreamHandler
{
    public function getUrl()
    {
        $dt = getdate();
        return str_replace(
            ['%Y', '%m', '%d', '%H', '%i', '%s'],
            [$dt['year'], $dt['mon'], $dt['mday'], $dt['hours'], $dt['minutes'], $dt['seconds']],
            $this->url
        );
    }
}
