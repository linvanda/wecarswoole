<?php

namespace WecarSwoole\Repository;

use WecarSwoole\Exceptions\PropertyCannotBeNullException;
use WecarSwoole\MySQLFactory;

/**
 * MySQL 仓储基类
 * 子类必须设置 dbName 属性 （对应数据库配置文件的 key）
 * 不支持一个仓储中跨库查询
 * Class MySQLRepository
 * @package WecarSwoole\Repository
 */
class MySQLRepository implements IRepository
{
    /**
     * @var \Devar\MySQL\Query
     */
    protected $query;
    protected $dbName = 'user_center';

    /**
     * MySQLRepository constructor.
     * @throws \Exception
     */
    public function __construct()
    {
        if (!$this->dbName) {
            throw new PropertyCannotBeNullException(get_called_class(), 'dbName');
        }

        $this->query = MySQLFactory::build($this->dbName);
    }

    public function getDBContext()
    {
        return $this->query;
    }

    public function setDBContext($dbContext)
    {
        $this->query = $dbContext;
    }
}