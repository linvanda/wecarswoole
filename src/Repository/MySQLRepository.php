<?php

namespace WecarSwoole\Repository;

use WecarSwoole\MySQLFactory;

/**
 * MySQL 仓储基类
 * 子类必须设置 dbName 属性 （对应数据库配置文件的 key）
 * 不支持一个仓储中跨库查询
 * Class MySQLRepository
 * @package WecarSwoole\Repository
 */
abstract class MySQLRepository implements IRepository
{
    /**
     * @var \Dev\MySQL\Query
     */
    protected $query;

    /**
     * MySQLRepository constructor.
     * @throws \Exception
     */
    public function __construct()
    {
        if (!$this->dbAlias()) {
            throw new \Exception('dbName can not be null');
        }

        $this->query = MySQLFactory::build($this->dbAlias());
    }

    public function getDBContext()
    {
        return $this->query;
    }

    public function setDBContext($dbContext)
    {
        $this->query = $dbContext;
    }

    abstract protected function dbAlias(): string;
}