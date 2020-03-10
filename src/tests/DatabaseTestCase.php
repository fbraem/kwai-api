<?php
declare(strict_types=1);

namespace Tests;

use Core\Clubman;
use Kwai\Core\Infrastructure\Database\Connection;
use Kwai\Core\Infrastructure\Database\DatabaseException;
use PHPUnit\Framework\TestCase;

class DatabaseTestCase extends TestCase
{
    protected static ?Connection $db;

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
    }
}