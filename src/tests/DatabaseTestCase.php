<?php
declare(strict_types=1);

namespace Tests;

use Core\Clubman;
use Kwai\Core\Infrastructure\Database\Connection;
use Kwai\Core\Infrastructure\Database\DatabaseException;
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
        $application = Clubman::getApplication();
        $config = $application->getContainer()->get('settings');

        self::$db = new Connection(
            $config['database']['test']['dsn'],
            $config['database']['test']['user'],
            $config['database']['test']['pass']
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
                    self::$db->commit();
                }
            }
        }
        parent::tearDownAfterClass();
    }
}
