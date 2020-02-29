<?php
declare(strict_types=1);

namespace Tests;

use Core\Clubman;
use Kwai\Core\Infrastructure\Database\Connection;
use Kwai\Core\Infrastructure\Database\DatabaseException;
use PHPUnit\Framework\TestCase;

class DatabaseTestCase extends TestCase
{
    private static ?Connection $db = null;

    public static function getDatabase(): Connection
    {
        if (!self::$db) {
            $application = Clubman::getApplication();
            $config = $application->getContainer()->get('settings');

            try {
                self::$db = new Connection(
                    $config['database']['test']['dsn'],
                    $config['database']['test']['user'],
                    $config['database']['test']['pass']
                );
            } catch (DatabaseException $e) {
                echo $e->getMessage();
            }
        }
        return self::$db;
    }
}