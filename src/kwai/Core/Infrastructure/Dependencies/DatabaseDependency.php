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
    public function __construct(
        private ?array $settings = null
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
        $logger = new Logger('kwai-db');
        if (isset($this->settings['logger']['database'])) {
            if (isset($this->settings['logger']['database']['file'])) {
                $logger->pushHandler(
                    new StreamHandler(
                        $this->settings['logger']['database']['file'],
                        $this->settings['logger']['database']['level'] ?? Logger::DEBUG
                    )
                );
            }
        }

        $dbConfig = $this->settings['database'];
        $dbDefault = $this->settings['default_database'];
        $db = new Connection(
            $dbConfig[$dbDefault]['dsn'],
            $logger
        );
        $db->connect(
            $dbConfig[$dbDefault]['user'],
            $dbConfig[$dbDefault]['pass'],
        );
        return $db;
    }
}
