<?php
/**
 * @package
 * @subpackage
 */
declare(strict_types=1);

namespace Kwai\Core\Infrastructure\Dependencies;

use Kwai\Core\Infrastructure\Database\Connection;
use Kwai\Core\Infrastructure\Database\DatabaseException;

/**
 * Class DatabaseDependency
 */
class DatabaseDependency implements Dependency
{
    /**
     * Creates a database connection.
     *
     * @param array $settings
     * @return Connection
     * @throws DatabaseException
     */
    public function __invoke(array $settings)
    {
        $dbConfig = $settings['database'];
        $dbDefault = $settings['default_database'];
        return new Connection(
            $dbConfig[$dbDefault]['dsn'],
            $dbConfig[$dbDefault]['user'],
            $dbConfig[$dbDefault]['pass']
        );
    }
}
