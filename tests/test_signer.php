<?php

namespace Test;

use WecarSwoole\Signer\WecarSigner;

include_once './base.php';

$params = [
    'name' => 'linvanda',
    'sex' => 'male',
    'flag' => 0,
    'app_id' => '1234',
];
$secret = "123456789";

$signer = new WecarSigner();

$token = $signer->signature($params, $secret);
$mtext = $signer->verify($params, $token, $secret);

echo "token:$token,suc:$mtext\n";
