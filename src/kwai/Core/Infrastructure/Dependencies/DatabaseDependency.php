<?php
/**
 * @package
 * @subpackage
 */
declare(strict_types=1);

namespace Kwai\Core\Infrastructure\Dependencies;

use Kwai\Core\Infrastructure\Database\Connection;
use Kwai\Core\Infrastructure\Database\DatabaseException;
use Monolog\Handler\StreamHandler;
use Monolog\Logger;

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
        $logger = new Logger('kwai-db');
        if (isset($settings['logger'])) {
            if (isset($settings['logger']['file'])) {
                $logger->pushHandler(
                    new StreamHandler(
                        $settings['logger']['file'],
                        $settings['logger']['level'] ?? Logger::DEBUG
                    )
                );
            }
        }

        $dbConfig = $settings['database'];
        $dbDefault = $settings['default_database'];
        return new Connection(
            $dbConfig[$dbDefault]['dsn'],
            $dbConfig[$dbDefault]['user'],
            $dbConfig[$dbDefault]['pass'],
            $logger
        );
    }
}
