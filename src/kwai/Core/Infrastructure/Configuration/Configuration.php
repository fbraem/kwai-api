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
use Dotenv\Repository\Adapter\ArrayAdapter;
use Dotenv\Repository\RepositoryBuilder;
use Dotenv\Store\StringStore;

/**
 * Class Configuration
 */
class Configuration
{
    private array $variables = [];

    public function __construct(
        private Dotenv $env
    ) {
        $this->variables = $env->load();
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
        $repository = RepositoryBuilder::createWithNoAdapters()
            ->immutable()
            ->addAdapter(ArrayAdapter::create()->get())
            ->make()
        ;
        $env = Dotenv::create($repository, $path, $file);
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
        $repository = RepositoryBuilder::createWithNoAdapters()
            ->immutable()
            ->addAdapter(ArrayAdapter::create()->get())
            ->make()
        ;
        $env = new Dotenv(new StringStore($content), new Parser(), new Loader(), $repository);
        return new self($env);
    }

    public function getDatabaseConfiguration(): DatabaseConfiguration
    {
        DatabaseConfiguration::validate($this->env);
        return DatabaseConfiguration::createFromVariables($this->variables);
    }

    public function getCorsConfiguration(): CorsConfiguration
    {
        CorsConfiguration::validate($this->env);
        return CorsConfiguration::createFromVariables($this->variables);
    }

    public function getLoggerConfiguration(): LoggerConfiguration
    {
        LoggerConfiguration::validate($this->env);
        return LoggerConfiguration::createFromVariables($this->variables);
    }

    public function getSecurityConfiguration(): SecurityConfiguration
    {
        SecurityConfiguration::validate($this->env);
        return SecurityConfiguration::createFromVariables($this->variables);
    }

    public function getMailerConfiguration(): MailerConfiguration
    {
        MailerConfiguration::validate($this->env);
        return MailerConfiguration::createFromVariables($this->variables);
    }

    public function getWebsiteConfiguration(): WebsiteConfiguration
    {
        WebsiteConfiguration::validate($this->env);
        return WebsiteConfiguration::createFromVariables($this->variables);
    }

    public function getFilesystemConfiguration(): FilesystemConfiguration
    {
        FilesystemConfiguration::validate($this->env);
        return FilesystemConfiguration::createFromVariables($this->variables);
    }

    public function getVariable(string $key, string $default = ''): string
    {
        return $this->variables[$key] ?? $default;
    }
}
