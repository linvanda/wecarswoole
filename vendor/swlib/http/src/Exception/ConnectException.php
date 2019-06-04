<?php
/**
 * Copyright: Swlib
 * Author: Twosee <twose@qq.com>
 * Date: 2018/4/1 上午12:53
 */

namespace Swlib\Http\Exception;

class ConnectException extends RequestException
{

    public function __construct(
        \Swlib\Http\Request $request,
        int $code = 0,
        string $message = '',
        \Exception $previous = null
    ) {
        parent::__construct($request, null, $code, $message, $previous);
    }

}