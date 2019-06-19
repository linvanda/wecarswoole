<?php

namespace App\Foundation\Client\SSCardRequest;

use WecarSwoole\Client\Http\Component\DefaultHttpRequestAssembler;

/**
 * 储值卡请求组装器
 */
class SSCardHttpRequestAssembler extends DefaultHttpRequestAssembler
{
    protected function reassemble(array $flagParams, array $queryParams, array $body): array
    {
        $signer = new SSCardSigner();

        $key_info = $signer->getLastKeyInfo();
        if (!$key_info) {
            \Think\Log::record("key_info:" . print_r($key_info, true), \Think\Log::DEBUG);
            return false;
        }
        $rsa = new \Rsa("", $key_info['ss_public_key']);
        $data = $params;
        if ($encryt == 1) {
            $data = $rsa->encryptByPublicKey($params);
        }
        $response_data = [
            'app_id' => $key_info['id'],
            'time' =>  date("Y-m-d H:i:s", time()),
            'type' => 0,
            'data' => json_encode($data),
        ];
        $token = "app_id=" . $response_data['app_id'] . "&time=" . $response_data['time'] . "&type=" . $response_data['type'] . "&data=" . $response_data['data'] . $key_info['token'];
        $response_data['token'] = md5($token);








        // 签名器
        $signer = new WecarSigner();
        $currentServerInfo = UtilConfig::getServerInfoByAppId($this->config->appId);
        $secret = $currentServerInfo['secret'] ?? '';

        if ($this->config->method === 'GET') {
            $queryParams = [
                'app_id' => $this->config->appId,
                'data' => json_encode($queryParams)
            ];
            $queryParams['token'] = $signer->signature($queryParams, $secret);
        } else {
            $body = [
                'app_id' => $this->config->appId,
                'data' => json_encode($body)
            ];
            $body['token'] = $signer->signature($body, $secret);
        }

        return ['flag_params' => $flagParams, 'query_params' => $queryParams, 'body' => $body];
    }
}
