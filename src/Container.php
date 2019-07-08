<?php

namespace WecarSwoole;

use EasySwoole\Component\Di;

/**
 * Container facade
 * Class Container
 * @package WecarSwoole
 */
class Container
{
    /**
     * 从容器获取对象。单例模式，只会实例化对象一次
     * @param $name
     * @return mixed
     * @throws \Throwable
     */
    public static function get($name)
    {
        return Di::getInstance()->get("di-container")->get($name);
    }

    /**
     * 同 get，不过 make 每次会重新实例化对象
     * @param $name
     * @param array $parameters
     * @return mixed
     * @throws \Throwable
     */
    public static function make($name, array $parameters = [])
    {
        return Di::getInstance()->get("di-container")->make($name, $parameters);
    }

    /**
     * 设置注入内容
     * @param $name
     * @param $value
     * @return mixed
     * @throws \Throwable
     */
    public static function set($name, $value)
    {
        return Di::getInstance()->get("di-container")->set($name, $value);
    }

    /**
     * @param $name
     * @return mixed
     * @throws \Throwable
     */
    public static function has($name)
    {
        return Di::getInstance()->get("di-container")->has($name);
    }
}
