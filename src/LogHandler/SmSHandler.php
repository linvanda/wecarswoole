<?php

namespace WecarSwoole\LogHandler;

use Monolog\Handler\AbstractProcessingHandler;
use Monolog\Logger;
use WecarSwoole\Container;
use WecarSwoole\Sms;

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
            Container::get(Sms::class)->send($mobile, $record['formatted']);
        }
    }
}
