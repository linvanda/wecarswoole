<?php

namespace Test;

use WecarSwoole\Signer\WecarSigner;

include_once './base.php';

$params = [
    'name' => 'linvanda',
    'sex' => 'male',
    'flag' => 0,
    'app_id' => '1234',
    'back_url' => 'http://wx.weicheche.cn'
];
$secret = "123456789";

$signer = new WecarSigner($secret);

$token = $signer->signature($params);
$mtext = $signer->verify($params, $token);

echo "token:$token,suc:$mtext\n";
