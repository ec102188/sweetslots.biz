<?php
namespace providerBundle\examples\mysqlExample;

/**
 * Class DbConnect
 * @package providerBundle\examples\mysqlExample
 */
class DbConnect
{
    protected static $pdo;

    /**
     * @return \PDO
     */
    public static function getPdo()
    {
        if (!self::$pdo) {
            $dbCfg = require __DIR__ . '/config/db.php';
            self::$pdo = new \PDO($dbCfg['dsn'], $dbCfg['username'], $dbCfg['password'], $dbCfg['options']);
        }

        return self::$pdo;
    }
}
