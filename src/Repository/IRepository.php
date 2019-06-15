<?php

namespace WecarSwoole\Repository;

interface IRepository
{
    public function getDBContext();
    public function setDBContext($dbContext);
}
