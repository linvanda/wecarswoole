<?php

namespace WecarSwoole\LogHandler;

use Monolog\Handler\AbstractProcessingHandler;
use Monolog\Logger;
use WecarSwoole\SMS;

class SmSHandler extends AbstractProcessingHandler
{
    protected $mobiles;

    public function __construct(array $mobiles, $level = Logger::DEBUG, $bubble = true)
    {
        $this->mobiles = $mobiles;
        parent::__construct($level, $bubble);
    }

    protected function write(array $record)
    {
        foreach ($this->mobiles as $mobile) {
            SMS::getInstance()->send($mobile, mb_substr($record['message'], 0, 50));
        }
    }
}
