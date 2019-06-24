<?php

namespace App\Foundation\Client\SSCardRequest;

use EasySwoole\EasySwoole\Config;
use WecarSwoole\Client\API;
use WecarSwoole\Container;
use WecarSwoole\Util\RSA;

/**
 * SS 储值卡 api 签名
 */
class SSCardSigner
{
    protected $autoCheckFields = false;
    private $expired_time = 7200;

    /**
     * 协商密钥逻辑
     * @throws \Exception
     */
    public function consultKey()
    {
        $rsa = new RSA();
        $new_key_pairs = $rsa->createKeyPairs([
            'digest_alg' => "sha512",
            'private_key_bits' => 512,
            'private_key_type' => OPENSSL_KEYTYPE_RSA,
        ]);

        if (!$new_key_pairs) {
            throw new \Exception("创建密钥失败");
        }

        // 通过基础公钥对密钥对进行加密
        $base_rsa = new RSA(
            Config::getInstance()->getConf('wcc_private_key'),
            Config::getInstance()->getConf('wcc_public_key')
        );
        // 存储密钥信息
        $private_key = $base_rsa->encryptByPublicKey($new_key_pairs['private_key']);
        $public_key = $base_rsa->encryptByPublicKey($new_key_pairs['public_key']);
        $add_key_id = Container::make(MySQLSSCardRepository::class)->addKey(
            new Key(['wcc_private_key' => $private_key, 'wcc_public_key' => $public_key])
        );

        //请求ss更换密钥
        $ss_rsa = new RSA("", Config::getInstance()->getConf('ss_public_key'));
        //根据环境变量获取用户密码及协商地址
        $name = "SS";
        if (isset(self::$_modules[ENVIRON][$name])) {
            $module = self::$_modules[ENVIRON][$name];
            $rand_key = \WPLib\Helper::getRandByWeight($module['servers'], 'weight');
            $url = $module['servers'][$rand_key]['url'];
            $user_name = $module["user_name"];
            $user_pass = $module["user_pass"];
        } else {
            return false;
        }
        $request_data = [
            'app_id' => $add_key_id,
            'UserName' => $user_name,
            'UserPass' => $ss_rsa->encryptByPublicKey($user_pass),
            'KeyType' => '2',
            "Expired" => $this->expired_time,
            "KeyStr" => $ss_rsa->encryptByPublicKey($new_key_pairs['public_key']),
        ];
        $request_data = $this->encryptData($request_data, $add_key_id);

        $res = API::call('sscard:key.sync', $request_data);


        if ($res['status'] != 200) {
            return false;
        } else {
            //ss更换成功，将ss的公钥更新进数据库
            $res = $rsa->decryptByPrivateKey($res['data']);
            $data = [
                'id' => $add_key_id,
                'ss_public_key' => $res['KeyStr'],
                'token' => $res['AccessToken'],
                'expired_time' => $this->expired_time,
            ];
            $update = D("Keys")->save($data);
            if (!$update) {
                return false;
            }
        }
    }

    /**
     * 获取最近一次生成的公钥
     * @return bool|mixed
     */
    public function getLastPublicKey()
    {
        $key_info = $this->getLastKeyInfo();
        if (!$key_info) {
            return false;
        } else {
            $rsa = new \Rsa(C("WCC_PRIVATE_KEY"), C("WCC_PUBLIC_KEY"));
            $wcc_public_key = $rsa->decryptByPrivateKey($key_info['wcc_public_key']);
            if (!$wcc_public_key) {
                return false;
            } else {
                return $wcc_public_key;
            }
        }
    }

    /**
     * 获取最后一次生成的私钥
     * @return bool|mixed
     */
    public function getLastPrivateKey()
    {
        $key_info = $this->getLastKeyInfo();
        if (!$key_info) {
            return false;
        } else {
            $rsa = new \Rsa(C("WCC_PRIVATE_KEY"), C("WCC_PUBLIC_KEY"));
            $wcc_private_key = $rsa->decryptByPrivateKey($key_info['wcc_private_key']);
            if (!$wcc_private_key) {
                return false;
            } else {
                return $wcc_private_key;
            }
        }
    }

    /**
     * 获取最后一次SS的公钥
     * @return bool
     */
    public function getLastSSPublicKey()
    {
        $key_info = $this->getLastKeyInfo();
        if (!$key_info) {
            return false;
        } else {
            $ss_public_key = $key_info['ss_public_key'];
            if (!$ss_public_key) {
                return false;
            } else {
                return $ss_public_key;
            }
        }
    }

    /**
     * 获取最后一次生成的密钥
     * @return mixed
     * @throws \Exception
     */
    public function getLastKeyInfo()
    {
        $where = [
            'type' => 1,
            'is_delete' => 0,
            'expired_time' => array("gt" , 0),
            'token' => array("neq" , ''),
        ];
        $key_info = D("Keys")->get($where);
        if ((time() - $key_info['create_time']) > ($key_info['expired_time'] - 180)) {
            //如果密钥快过期或者已经过期
            $consult = $this->consultKey();
            if ($consult === false) {
                return false;
            } else {
                $key_info = $this->getLastKeyInfo();
            }
        }
        return $key_info;
    }

    /**
     * 脚本定时更新密钥
     * @param boolean $is_force 是否强制更新
     * @return mixed
     */
    public function updateKeyInfo($is_force = false)
    {
        $where = [
            'type'         => 1,
            'is_delete'    => 0,
            'expired_time' => array("gt" , 0),
            'token'        => array("neq" , ''),
        ];
        $key_info = D("Keys")->get($where);
        //如果key为空或小于40分钟有效期，则更新key
        if (
            $is_force === true
            || empty($key_info)
            || (!empty($key_info)
                && ($key_info['create_time'] + $key_info['expired_time']) - time() < 2400 )
        ) {
            $consult = $this->consultKey();
            if ($consult === false) {
                return false;
            } else {
                $key_info = $this->getLastKeyInfo();
            }
        }

        if ($key_info) {
            return true;
        }

        return false;
    }

    /**
     * 根据id获取密钥信息
     * @param $id
     * @return mixed
     */
    public function getKeyInfoById($id)
    {
        $where = [
            'id' => $id,
        ];
        $key_info = D("Keys")->get($where);
        return $key_info;
    }

    /**
     * 协商密钥需要的基本方法
     * @param $data
     * @param $key_id
     * @return array
     */
    private function encryptData($data, $key_id)
    {
        $rsa = new RSA("", Config::getInstance()->getConf('ss_public_key'));
        $response_data = [
            'app_id' => $key_id,
            'time' => date("Y-m-d H:i:s", time()),
            'type' => '1',
            'data' => $rsa->encryptByPublicKey($data),
        ];
        $token = "app_id=" . $response_data['app_id'] . "&time=" . $response_data['time']
            . "&type=" . $response_data['type'] . "&data=" . $response_data['data']
            . "2iiigbbXfM0VbgpwSCAUpjYbbEZAokLl";
        $response_data['token'] = md5($token);

        return $response_data;
    }

    /**
     * 给ss发送数据
     * @param $url
     * @param array $params
     * @param string $type
     * @param int $type 如果是加密的数据，type=1,否则=0，如果是压缩数据，type=2,否则=0，如果加密+压缩，type=3
     * @param array $options
     * @param int $timeout
     * @param int $connection_timeout
     * @param int $retry_time
     * @return bool|mixed
     */
    public function send($url, $params = [], $type = 'POST', $encryt = 0, $options = [], $timeout = 5, $connection_timeout = 2, $retry_time = 3)
    {
        $name = substr($url, 1, 2);
        //处理url
        if (isset(self::$_modules[ENVIRON][$name])) {
            $url = substr($url, 3);
            $module = self::$_modules[ENVIRON][$name];
            $rand_key = \WPLib\Helper::getRandByWeight($module['servers'], 'weight');
            $base_url = $module['servers'][$rand_key]['url'];
            $url = $base_url . $url;
        }
        $key_info = $this->getLastKeyInfo();
        //\Think\Log::record("key_info:".print_r($key_info, true), \Think\Log::INFO);
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
        $res = \WPLib\WPApi::send($url, $response_data, $type, $options, $timeout, $connection_timeout, $retry_time);
        $res = json_decode($res, true);
        if ($res["status"] == 1) {
            //授权失败时重新调用一次授权协商
            \Think\Log::record("re_consult_key" . json_encode($res), \Think\Log::WARN);
            $this->consultKey();
            return $res;
        }
        if (is_array($res) && $encryt == 1) {
            $local_rsa = new \Rsa($key_info['wcc_private_key']);
            $res['data'] = $local_rsa->decryptByPrivateKey($res['data']);
        }
        return $res;
    }

    /**
     * 加密ss单独字段
     * @param $string
     * @return string
     */
    public function encryptSingleString($string)
    {
        $ss_public_key = $this->getLastSSPublicKey();
        $rsa = new \Rsa("", $ss_public_key);
        return $rsa->encryptByPublicKey($string);
    }

    /**
     * 使用SS公钥加密ss单独字段
     * @param $string
     * @return string
     */
    public function encryptSingleStringWithSSKey($string)
    {
        $rsa = new \Rsa("", C("SS_PUBLIC_KEY"));
        return $rsa->encryptByPublicKey($string);
    }
}
