<?php

namespace WecarSwoole;

use WecarSwoole\ATO\ArrayToObject;
use WecarSwoole\ATO\IArrayBuildable;
use WecarSwoole\OTA\IExtractable;
use WecarSwoole\OTA\ObjectToArray;
use WecarSwoole\Util\AutoProperty;
use WecarSwoole\Util\GetterSetter;

/**
 * 实体基类
 * 虽然实体一般都需要存储，但设计原则是实体不要知晓存储的任何信息，实体存储相关的事情应全部交给仓储处理
 * Class Entity
 * @package WecarSwoole
 */
class Entity implements IExtractable, IArrayBuildable
{
    use ObjectToArray, ArrayToObject, AutoProperty, GetterSetter;
}
