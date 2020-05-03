<?php
declare(strict_types=1);

namespace Tests;

use Kwai\Core\Infrastructure\Database\Connection;
use Kwai\Core\Infrastructure\Database\DatabaseException;
use Kwai\Core\Infrastructure\Dependencies\Settings;
use Monolog\Handler\StreamHandler;
use Monolog\Logger;
use PHPUnit\Framework\TestCase;
use Throwable;

/**
 * @SuppressWarnings(PHPMD.ShortVariable)
 */
class DatabaseTestCase extends TestCase
{
    protected static ?Connection $db;

    private static bool $success = true;

    /**
     * @throws DatabaseException
     */
    public static function setUpBeforeClass(): void
    {
        parent::setUpBeforeClass();

        $config = (new Settings())();
        // TODO: see if we can use DatabaseDependency here ...
        $logger = new Logger('kwai-db');
        if (isset($config['logger'])) {
            if (isset($config['logger']['file'])) {
                $logger->pushHandler(
                    new StreamHandler(
                        $config['logger']['file'],
                        $config['logger']['level'] ?? Logger::DEBUG
                    )
                );
            }
        }

        self::$db = new Connection(
            $config['database']['test']['dsn'],
            $config['database']['test']['user'],
            $config['database']['test']['pass'],
            $logger
        );
        self::$db->begin();
    }

    protected function onNotSuccessfulTest(Throwable $t): void
    {
        // When a transaction is active, rollback it.
        self::$success = false;
        if (self::$db != null) {
            if (self::$db->inTransaction()) {
                self::$db->rollback();
            }
        }
        parent::onNotSuccessfulTest($t);
    }

    public static function tearDownAfterClass(): void
    {
        if (self::$success) {
            if (self::$db != null) {
                if (self::$db->inTransaction()) {
                    try {
                        self::$db->commit();
                    } catch (DatabaseException $e) {
                        //TODO: What do we do here?
                    }
                }
            }
        }
        parent::tearDownAfterClass();
    }
}
