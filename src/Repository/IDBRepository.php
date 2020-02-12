<?php

namespace WecarSwoole\Repository;

interface IDBRepository extends IRepository
{
    public function getDBContext();
    public function setDBContext($dbContext);
}
