<?php

namespace WecarSwoole;

use EasySwoole\EasySwoole\Config;
use EasySwoole\Http\Request;
use EasySwoole\Utility\Random;

/**
 * 请求标识
 * 只能在协程环境使用
 * Class RequestId
 * @package WecarSwoole
 */
class RequestId
{
    private $flag;

    public function __construct(Request $request)
    {
        $this->generate($request);
    }

    public function get()
    {
        return $this->flag;
    }

    public function __toString()
    {
        return $this->flag;
    }

    public static function key()
    {
        return Config::getInstance()->getConf('request_id_key') ?: 'wcc-request-id';
    }

    /**
     * request_id 格式：服务标识1-服务标识2-服务标识3:id1-id2-id3
     * @param Request $request
     */
    private function generate(Request $request)
    {
        // 从 header 中获取
        $pre = $request->getHeader(self::key()) ?? [];
        if (is_array($pre)) {
            $pre = $pre[0] ?? '';
        }
        $preArr = explode(':', $pre);

        $svrFlag = Config::getInstance()->getConf('app_flag') ?: 'NONE';
        if (isset($preArr[0]) && $preArr[0]) {
            $svrFlag = $preArr[0] . '-' . $svrFlag;
        }

        $uniqId = $this->newId();
        if (isset($preArr[1]) && $preArr[1]) {
            $uniqId = $preArr[1] . '-' . $uniqId;
        }

        $this->flag = $svrFlag . ':' . $uniqId;
    }

    private function newId()
    {
        return Random::character(8);
    }
}
