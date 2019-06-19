<?php

namespace WecarSwoole\Util;

/**
 * RSA 加密，解密，密钥生成等方法
 */
class RSA
{
    /**
     * 私钥
     * @var string
     */
    private $private_key = "";

    /**
     * 公钥
     * @var string
     */
    private $public_key = "";

    /**
     * 公钥资源
     * @var string
     */
    private $public_resource = "";

    /**
     * 私钥资源
     * @var string
     */
    private $private_resource = "";
    /**
     * key大小 bits
     * @var int
     */
    private $key_size = 0;

    /**
     * 默认配置
     * @var array
     */
    private $config = [
        'digest_alg' => "sha512",
        'private_key_bits' => 512,
        'private_key_type' => OPENSSL_KEYTYPE_RSA,
    ];

    /**
     * @param string $private_key
     * @param string $public_key
     */
    public function __construct(string $private_key = "", string $public_key = "")
    {
        if (!empty($private_key)) {
            $this->private_key = $private_key;
            $this->getKeySizeByPrivateKey($private_key);
        }

        if (!empty($public_key)) {
            $this->public_key = $public_key;
            $this->getKeySizeByPublicKey($public_key);
        }
    }

    /**
     * 生成新的密钥对
     * @param array $config
     * @return array|bool
     */
    public function createKeyPairs($config = [])
    {
        if (!$config) {
            $config = $this->config;
        }
        $resource = openssl_pkey_new($config);
        if (!$resource) {
            return false;
        }

        openssl_pkey_export($resource, $this->private_key);
        $key_info = openssl_pkey_get_details($resource);
        $this->public_key = $key_info['key'];
        $this->key_size = $config['private_key_bits'] / 8;
        return [
            'public_key' => $this->public_key,
            'private_key' => $this->private_key,
        ];
    }

    /**
     * 公钥加密
     * @param $input
     * @return string
     */
    public function encryptByPublicKey($input)
    {
        if ($this->key_size == 0) {
            return false;
        }
        if (is_array($input)) {
            $plaintext = json_encode($input);
        } else {
            $plaintext = $input;
        }
        $ciphertext = "";
        while ($plaintext) {
            $tmp = substr($plaintext, 0, $this->key_size - 11);
            $plaintext = substr($plaintext, $this->key_size - 11);
            openssl_public_encrypt($tmp, $output, $this->public_key);
            $ciphertext .= $output;
        }
        return base64_encode($ciphertext);
    }

    /**
     * 私钥解密
     * @param $input
     * @return mixed
     */
    public function decryptByPrivateKey($input)
    {
        if ($this->key_size == 0) {
            return false;
        }
        $ciphertext = base64_decode($input);
        $plaintext = "";
        while ($ciphertext) {
            $tmp = substr($ciphertext, 0, $this->key_size);
            $ciphertext = substr($ciphertext, $this->key_size);
            openssl_private_decrypt($tmp, $output, $this->private_key);
            $plaintext .= $output;
        }

        $res_arr = json_decode(trim($plaintext), true);
        if (!$res_arr) {
            return $plaintext;
        } else {
            return $res_arr;
        }
    }

    /**
     * 私钥加密
     * @param $input
     * @return string
     */
    public function encryptByPrivateKey($input)
    {
        if ($this->key_size == 0) {
            return false;
        }
        if (is_array($input)) {
            $plaintext = json_encode($input);
        } else {
            $plaintext = $input;
        }

        $ciphertext = "";
        while ($plaintext) {
            $tmp = substr($plaintext, 0, $this->key_size - 11);
            $plaintext = substr($plaintext, $this->key_size - 11);
            openssl_private_encrypt($tmp, $output, $this->private_key);
            $ciphertext .= $output;
        }
        return base64_encode($ciphertext);
    }

    /**
     * 公钥解密
     * @param $input
     * @return mixed
     */
    public function decryptByPublicKey($input)
    {
        if ($this->key_size == 0) {
            return false;
        }
        $ciphertext = base64_decode($input);
        $plaintext = "";
        while ($ciphertext) {
            $tmp = substr($ciphertext, 0, $this->key_size);
            $ciphertext = substr($ciphertext, $this->key_size);
            openssl_public_decrypt($tmp, $output, $this->public_key);
            $plaintext .= $output;
        }

        $res_arr = json_decode(trim($plaintext), true);
        if (!$res_arr) {
            return $plaintext;
        } else {
            return $res_arr;
        }
    }

    /**
     * 通过公钥获取key大小
     * @param $public_key
     */
    private function getKeySizeByPublicKey($public_key)
    {
        $this->public_resource = openssl_pkey_get_public($public_key);
        if (!$this->public_resource) {
            return;
        }
        $key_detail = openssl_pkey_get_details($this->public_resource);
        if (!$key_detail) {
            return;
        }
        $this->key_size = $key_detail['bits'] / 8;
    }

    /**
     * 通过私钥获取key大小
     * @param $private_key
     */
    private function getKeySizeByPrivateKey($private_key)
    {
        $this->private_resource = openssl_pkey_get_private($private_key);
        if (!$this->private_resource) {
            return;
        }
        $key_detail = openssl_pkey_get_details($this->private_resource);
        if (!$key_detail) {
            return;
        }
        $this->key_size = $key_detail['bits'] / 8;
    }
}
