<?php

namespace WecarSwoole;

use WecarSwoole\Repository\IRepository;

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
    private $dbContext;

    protected function __construct()
    {
        $this->status = self::STATUS_OPEN;
    }

    public function __destruct()
    {
        if ($this->status != self::STATUS_CLOSED) {
            $this->dbContext->rollback();
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
        $result = $this->dbContext->commit();
        $this->status = self::STATUS_CLOSED;

        return $result;
    }

    public function rollback()
    {
        $result = $this->dbContext->rollback();
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
            if (!$repository instanceof IRepository) {
                continue;
            }

            // 先试图提交之前的事务
            $repository->getDBContext()->commit();

            // 取第一个仓储的 dbContext
            if (!$this->dbContext) {
                $this->dbContext = $repository->getDBContext();
                $this->dbContext->begin();
                continue;
            }

            $repository->setDBContext($this->dbContext);
        }
    }
}