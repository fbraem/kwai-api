<?php
declare(strict_types = 1);

use Phinx\Config\Config;
use Phinx\Migration\Manager;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Console\Input\StringInput;
use Symfony\Component\Console\Output\NullOutput;

class Database
{
    const CONNECTION_STR = 'sqlite:/var/tmp/kwai.db';

    private static $db;

    public static function getDatabase()
    {
        if (! self::$db) {
            $connection = new \Opis\Database\Connection(self::CONNECTION_STR);
            $connection->option(\PDO::ATTR_DEFAULT_FETCH_MODE, \PDO::FETCH_ASSOC);

            $configArray = require('../phinx.php');
            $configArray['environments']['test'] = [
                'adapter'    => 'sqlite',
                'connection' => $connection->getPDO()
            ];
            $config = new Config($configArray);
            $manager = new Manager($config, new StringInput(' '), new NullOutput());
            $manager->migrate('test');

            $connection = new \Opis\Database\Connection(self::CONNECTION_STR);
            $connection->option(\PDO::ATTR_STRINGIFY_FETCHES, false);
            self::$db = new \Opis\Database\Database($connection);
        }
        return self::$db;
    }
}
