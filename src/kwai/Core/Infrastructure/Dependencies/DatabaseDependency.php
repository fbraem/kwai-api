<?php
/**
 * @package
 * @subpackage
 */
declare(strict_types=1);

namespace Kwai\Core\Infrastructure\Dependencies;

use Kwai\Core\Infrastructure\Configuration\Configuration;
use Kwai\Core\Infrastructure\Database\Connection;
use Kwai\Core\Infrastructure\Database\DatabaseException;
use Monolog\Handler\StreamHandler;
use Monolog\Logger;

/**
 * Class DatabaseDependency
 */
class DatabaseDependency implements Dependency
{
    public function __construct(
        private ?Configuration $settings = null
    ) {
        $this->settings ??= depends('kwai.settings', Settings::class);
    }

    /**
     * Creates a database connection.
     *
     * @return Connection
     * @throws DatabaseException
     */
    public function create()
    {
        $dbConfig = $this->settings->getDatabaseConfiguration();

        $db = new Connection(
            (string) $dbConfig->getDsn(),
            $dbConfig->getLogger()
        );
        $db->connect(
            $dbConfig->getUser(),
            $dbConfig->getPassword(),
        );
        return $db;
    }
}
