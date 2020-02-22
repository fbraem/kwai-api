<?php
declare(strict_types = 1);

use Phinx\Config\Config;
use Phinx\Migration\Manager;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Console\Input\StringInput;
use Symfony\Component\Console\Output\NullOutput;

use Kwai\Core\Infrastructure\Database\Connection;

class Database
{
    private static $db;

    public static function getDatabase()
    {
        if (! self::$db) {
            $application = \Core\Clubman::getApplication();
            $config = $application->getContainer()->get('settings');

            $pdo = new \PDO(
                $config['database']['test']['dsn'],
                $config['database']['test']['user'],
                $config['database']['test']['pass'],
                [
                    \PDO::ATTR_ERRMODE => \PDO::ERRMODE_EXCEPTION,
                    \PDO::ATTR_PERSISTENT => true, // BEST OPTION
                    \PDO::ATTR_DEFAULT_FETCH_MODE => \PDO::FETCH_ASSOC
                ]
            );

            $configArray = require('../phinx.php');
            $configArray['environments']['test'] = [
                'connection' => $pdo
            ];
            $manager = new Manager(
                new Config($configArray),
                new StringInput(' '),
                new NullOutput()
            );
            $manager->migrate('test');

            self::$db = new Connection(
                $config['database']['test']['dsn'],
                $config['database']['test']['user'],
                $config['database']['test']['pass']
            );
        }
        return self::$db;
    }
}
