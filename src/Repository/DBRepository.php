<?php

namespace WecarSwoole\Repository;

/**
 * 数据库仓储基类
 * Class DBRepository
 * @package WecarSwoole\Repository
 */
abstract class DBRepository extends Repository
{
    abstract public function getDBContext();
    abstract public function setDBContext($dbContext);
}
