<?php
declare(strict_types=1);

namespace Tests;

use Kwai\Core\Infrastructure\Database\Connection;
use Kwai\Core\Infrastructure\Database\DatabaseException;
use Kwai\Core\Infrastructure\Dependencies\Settings;
use Monolog\Handler\StreamHandler;
use Monolog\Logger;

/**
 * Class Context
 *
 * Use this class to create a context for tests. When a database is available
 *
 */
class Context
{
    private static ?Connection $db = null;

    public static function withDatabase()
    {
        if (self::$db == null) {
            $config = (new Settings())();
            // TODO: see if we can use DatabaseDependency here ...
            $logger = new Logger('kwai-db');
            if (isset($config['logger']['database'])) {
                if (isset($config['logger']['database']['file'])) {
                    $logger->pushHandler(
                        new StreamHandler(
                            $config['logger']['database']['file'],
                            $config['logger']['database']['level'] ?? Logger::DEBUG
                        )
                    );
                }
            }
            try {
                self::$db = new Connection(
                    $config['database']['test']['dsn'],
                    $config['database']['test']['user'],
                    $config['database']['test']['pass'],
                    $logger
                );
            } catch (DatabaseException $e) {
                echo 'No database: ' . $e;
            }
        }
        return self::$db;
    }

    public static function createContext(): object
    {
        return (object)['db' => self::withDatabase()];
    }

    public static function hasDatabase(): bool
    {
        return self::$db !== null;
    }
}
