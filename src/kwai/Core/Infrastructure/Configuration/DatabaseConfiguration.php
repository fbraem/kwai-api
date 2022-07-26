<?php
/**
 * @package Core
 * @subpackage Infrastructure
 */
declare(strict_types=1);

namespace Kwai\Core\Infrastructure\Configuration;

use Dotenv\Dotenv;
use Kwai\Core\Infrastructure\Database\Connection;
use Kwai\Core\Infrastructure\Database\DatabaseException;
use Psr\Log\LoggerInterface;

/**
 * Class DatabaseConfiguration
 */
class DatabaseConfiguration implements Configurable
{
    public function __construct(
        private DsnDatabaseConfiguration $dsn,
        private string $user,
        private string $password,
        private ?LoggerConfiguration $loggerConfiguration = null
    ) {
    }

    public function getDsn(): DsnDatabaseConfiguration
    {
        return $this->dsn;
    }

    public function getUser(): string
    {
        return $this->user;
    }

    public function getPassword(): string
    {
        return $this->password;
    }

    public function getLogger(): LoggerInterface
    {
        return $this->loggerConfiguration->createLogger('kwai.db');
    }

    public static function createFromVariables(array $variables): self
    {
        return new self(
            new DsnDatabaseConfiguration($variables['KWAI_DB_DSN']),
            $variables['KWAI_DB_USER'],
            $variables['KWAI_DB_PASSWORD'],
            LoggerConfiguration::createFromPrefixedVariables('KWAI_DB_', $variables)
        );
    }

    public static function validate(Dotenv $env): void
    {
        $env->required([
            'KWAI_DB_DSN',
            'KWAI_DB_USER',
            'KWAI_DB_PASSWORD',
        ]);
    }

    /**
     * @throws DatabaseException
     */
    public function createConnection(): Connection
    {
        $db = new Connection(
            $this->getDsn(),
            $this->getLogger()
        );
        $db->connect(
            $this->user,
            $this->password,
        );
        return $db;
    }
}
