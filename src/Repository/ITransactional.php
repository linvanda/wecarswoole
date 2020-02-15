<?php

namespace WecarSwoole\Repository;

/**
 * 可提供事务的接口
 * Class ITransactional
 * @package WecarSwoole\Repository
 */
interface ITransactional
{
    /**
     * 获取事务主体上下文
     * @return mixed 返回的 context 需提供 commit、rollback 方法。此处采用鸭子类型模式，不做强类型限制（为了对接外部库）
     */
    public function getContext();

    /**设置事务主体上下文
     * @param mixed $context 事务上下文，需提供 commit、rollback 方法。
     * @return mixed
     */
    public function setContext($context);
}
