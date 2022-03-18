<?php
/**
 * @package Core
 * @subpackage Infrastructure
 */
declare(strict_types=1);

namespace Kwai\Core\Infrastructure\Configuration;

use Dotenv\Dotenv;
use Dotenv\Loader\Loader;
use Dotenv\Parser\Parser;
use Dotenv\Repository\RepositoryBuilder;
use Dotenv\Store\StringStore;

/**
 * Class Configuration
 */
class Configuration
{
    public function __construct(
        private Dotenv $env
    ) {
    }

    /**
     * Load configuration from a file.
     *
     * @param string $path
     * @param string $file
     * @return static
     */
    public static function createFromFile(string $path, string $file): self
    {
        $env = Dotenv::createImmutable($path, $file);
        $env->load();
        return new self($env);
    }

    /**
     * Load configuration from a string.
     *
     * @param string $content
     * @return static
     */
    public static function createFromString(string $content): self
    {
        $repository = RepositoryBuilder::createWithDefaultAdapters()->immutable()->make();
        $env = new Dotenv(new StringStore($content), new Parser(), new Loader(), $repository);
        $env->load();
        return new self($env);
    }

    public function getDatabaseConfiguration(): DatabaseConfiguration
    {
        DatabaseConfiguration::validate($this->env);
        return DatabaseConfiguration::createFromVariables($_SERVER);
    }

    public function getCorsConfiguration(): CorsConfiguration
    {
        CorsConfiguration::validate($this->env);
        return CorsConfiguration::createFromVariables($_SERVER);
    }

    public function getLoggerConfiguration(): LoggerConfiguration
    {
        LoggerConfiguration::validate($this->env);
        return LoggerConfiguration::createFromVariables($_SERVER);
    }

    public function getSecurityConfiguration(): SecurityConfiguration
    {
        SecurityConfiguration::validate($this->env);
        return SecurityConfiguration::createFromVariables($_SERVER);
    }

    public function getMailerConfiguration(): MailerConfiguration
    {
        MailerConfiguration::validate($this->env);
        return MailerConfiguration::createFromVariables($_SERVER);
    }

    public function getWebsiteConfiguration(): WebsiteConfiguration
    {
        WebsiteConfiguration::validate($this->env);
        return WebsiteConfiguration::createFromVariables($_SERVER);
    }
}
