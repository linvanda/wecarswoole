<?php

namespace WecarSwoole\Client\Config;

class HttpConfig extends Config
{
    public $server;
    public $path;
    public $method;
    public $contentType;
    public $timeout;
    public $CAFile;
    public $sslVerifyPeer;
    public $sslAllowSelfSigned;
    public $middlewares;
    public $throwException;

    /**
     * HttpConfig constructor.
     * @param array $apiConf
     * @throws \Exception
     */
    public function __construct(array $apiConf)
    {
        parent::__construct($apiConf);

        if (!$apiConf['path']) {
            throw new \Exception("配置错误：http api 未提供 path 信息。api name:{$apiConf['api_name']}");
        }

        $this->server = $apiConf['server'];
        $this->path = $apiConf['path'];
        $this->method = $apiConf['method'] ? strtoupper($apiConf['method']) : 'POST';
        $this->middlewares = $apiConf['middlewares'] ?? [];
        $this->throwException = $apiConf['throw_exception'] ?? true;

        if (isset($apiConf['content_type'])) {
            $this->contentType = $apiConf['content_type'];
        }

        if (isset($apiConf['timeout'])) {
            $this->timeout = $apiConf['timeout'];
        }
    }
}
