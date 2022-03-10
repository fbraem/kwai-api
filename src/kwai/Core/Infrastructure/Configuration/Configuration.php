<?php
/**
 * @package Core
 * @subpackage Infrastructure
 */
declare(strict_types=1);

namespace Kwai\Core\Infrastructure\Configuration;

use Dotenv\Dotenv;

/**
 * Class Configuration
 */
class Configuration
{
    private DatabaseConfiguration $databaseConfiguration;

    private CorsConfiguration $corsConfiguration;

    private LoggerConfiguration $loggerConfiguration;

    private SecurityConfiguration $securityConfiguration;

    public function __construct(
        private ?Dotenv $env = null
    ) {
        $this->env ??= Dotenv::createImmutable(
            __DIR__ . '/../../../../../config',
            '.kwai'
        );
        $this->databaseConfiguration = new DatabaseConfiguration();
        $this->corsConfiguration = new CorsConfiguration();
        $this->loggerConfiguration = new LoggerConfiguration();
        $this->securityConfiguration = new SecurityConfiguration();
    }

    public function getDatabaseConfiguration(): DatabaseConfiguration
    {
        return $this->databaseConfiguration;
    }

    public function getCorsConfiguration(): CorsConfiguration
    {
        return $this->corsConfiguration;
    }

    public function getLoggerConfiguration(): LoggerConfiguration
    {
        return $this->loggerConfiguration;
    }

    public function getSecurityConfiguration(): SecurityConfiguration
    {
        return $this->securityConfiguration;
    }

    public function load(): void
    {
        $vars = $this->env->load();

        $this->databaseConfiguration->validate($this->env);
        $this->databaseConfiguration->load($vars);

        $this->corsConfiguration->validate($this->env);
        $this->corsConfiguration->load($vars);

        $this->loggerConfiguration->validate($this->env);
        $this->loggerConfiguration->load($vars);

        $this->securityConfiguration->validate($this->env);
        $this->securityConfiguration->load($vars);
    }
}
