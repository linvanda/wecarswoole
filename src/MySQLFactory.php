<?php

namespace WecarSwoole;

use WecarSwoole\Exceptions\ConfigNotFoundException;
use EasySwoole\EasySwoole\Config;
use Dev\MySQL\Connector\CoConnectorBuilder;
use Dev\MySQL\Connector\DBConfig;
use Dev\MySQL\Pool\CoPool;
use Dev\MySQL\Query;
use Dev\MySQL\Transaction\CoTransaction;

/**
 * MySQL 查询器工厂，组装查询器
 * Class MySQLFactory
 * @package WecarSwoole
 */
class MySQLFactory
{
    /**
     * @param string $dbAlias 数据库配置别名，对应配置文件中数据库配置的 key
     * @return Query
     * @throws \Exception
     */
    public static function build(string $dbAlias): Query
    {
        $dbConf = Config::getInstance()->getConf("mysql.$dbAlias");
        if (!$dbConf) {
            throw new ConfigNotFoundException("mysql.".$dbAlias);
        }

        if (!isset($dbConf['read']) && !isset($dbConf['write'])) {
            $writeConf = $dbConf;
            $readConfs = [$writeConf];
        } else {
            $writeConf = $dbConf['write'] ?? [];
            $readConfs = $dbConf['read'] ?? [$writeConf];
        }

        $writeConfObj = self::createConfObj($writeConf);
        $readConfObjs = [];

        foreach ($readConfs as $readConf) {
            $readConfObjs[] = self::createConfObj($readConf);
        }

        $mySQLBuilder = CoConnectorBuilder::instance($writeConfObj, $readConfObjs);
        $pool = CoPool::instance($mySQLBuilder, $dbConf['pool']['size'] ?? 30);
        $transaction = new CoTransaction($pool);

        return new Query($transaction);
    }

    private static function createConfObj(array $config): DBConfig
    {
        if (!$config) {
            return null;
        }

        return new DBConfig(
            $config['host'],
            $config['user'],
            $config['password'],
            $config['database'],
            $config['port'] ?? 3306,
            $config['timeout'] ?? 3,
            $config['charset'] ?? 'utf8'
        );
    }
}