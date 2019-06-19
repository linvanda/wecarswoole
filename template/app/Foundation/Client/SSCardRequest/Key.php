<?php

namespace App\Foundation\Client\SSCardRequest;

use WecarSwoole\DTO;

class Key extends DTO
{
    public $WCCPublicKey;
    public $WCCPrivateKey;
    public $SSPublicKey;
}
