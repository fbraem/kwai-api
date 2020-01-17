<?php
declare(strict_types = 1);

class Database
{
    private static $db;

    public static function getDatabase()
    {
        if (! self::$db) {
            $config = include __DIR__ . '/../../api/config.php';
            $dbConfig = $config['database'][$config['default_database']];
            $connection = new \Opis\Database\Connection(
                $dbConfig['adapter'] . ':host=' . $dbConfig['host'] . ';dbname=' . $dbConfig['name'],
                $dbConfig['user'],
                $dbConfig['pass']
            );
            $connection->initCommand('SET NAMES UTF8');
            self::$db = new \Opis\Database\Database($connection);
        }
        return self::$db;
    }
}
