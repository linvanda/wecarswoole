<?php

namespace WecarSwoole;

use WecarSwoole\Repository\ITransactional;

/**
 * 事务管理器
 * Class Transaction
 * @package WecarSwoole
 */
class Transaction
{
    private const STATUS_OPEN = 1;
    private const STATUS_CLOSED = 2;

    private $status;
    private $context;

    protected function __construct()
    {
        $this->status = self::STATUS_OPEN;
    }

    public function __destruct()
    {
        if ($this->status != self::STATUS_CLOSED) {
            $this->context->rollback();
        }
    }

    /**
     * 开启事务
     * @param array ...$repositories
     * @return Transaction
     */
    public static function begin(...$repositories): Transaction
    {
        $trans = new static();
        $trans->add(...$repositories);

        return $trans;
    }

    public function commit()
    {
        $result = $this->context->commit();
        $this->status = self::STATUS_CLOSED;

        return $result;
    }

    public function rollback()
    {
        $result = $this->context->rollback();
        $this->status = self::STATUS_CLOSED;

        return $result;
    }

    /**
     * 添加仓储到事务中
     * @param array ...$repositories
     */
    public function add(...$repositories): void
    {
        foreach ($repositories as $repository) {
            if (!$repository instanceof ITransactional) {
                continue;
            }

            // 先试图提交之前的事务
            $repository->getContext()->commit();

            // 取第一个仓储的 dbContext
            if (!$this->context) {
                $this->context = $repository->getContext();
                $this->context->begin();
                continue;
            }

            $repository->setContext($this->context);
        }
    }
}
