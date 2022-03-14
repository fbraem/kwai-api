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
    private string $dsn;

    private string $user;

    private string $password;

    public function __construct(
        private LoggerConfiguration $loggerConfiguration = new LoggerConfiguration('KWAI_DB_')
    ) {
    }

    public function getDsn(): DsnDatabaseConfiguration
    {
        return new DsnDatabaseConfiguration($this->dsn);
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

    public function load(array $variables): void
    {
        $this->dsn = $variables['KWAI_DB_DSN'];
        $this->user = $variables['KWAI_DB_USER'];
        $this->password = $variables['KWAI_DB_PASSWORD'];

        $this->loggerConfiguration->load($variables);
    }

    public function validate(Dotenv $env): void
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
