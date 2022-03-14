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
    private ?array $vars = null;

    public function __construct(
        private ?Dotenv $env = null
    ) {
        $this->env ??= Dotenv::createImmutable(
            __DIR__ . '/../../../../../config',
            '.kwai'
        );
    }

    public function getDatabaseConfiguration(): DatabaseConfiguration
    {
        if ($this->vars === null) {
            $this->vars = $this->env->load();
        }
        DatabaseConfiguration::validate($this->env);
        return DatabaseConfiguration::createFromVariables($this->vars);
    }

    public function getCorsConfiguration(): CorsConfiguration
    {
        if ($this->vars === null) {
            $this->vars = $this->env->load();
        }
        CorsConfiguration::validate($this->env);
        return CorsConfiguration::createFromVariables($this->vars);
    }

    public function getLoggerConfiguration(): LoggerConfiguration
    {
        if ($this->vars === null) {
            $this->vars = $this->env->load();
        }
        LoggerConfiguration::validate($this->env);
        return LoggerConfiguration::createFromVariables($this->vars);
    }

    public function getSecurityConfiguration(): SecurityConfiguration
    {
        if ($this->vars === null) {
            $this->vars = $this->env->load();
        }
        SecurityConfiguration::validate($this->env);
        return SecurityConfiguration::createFromVariables($this->vars);
    }

    public function getMailerConfiguration(): MailerConfiguration
    {
        if ($this->vars === null) {
            $this->vars = $this->env->load();
        }
        MailerConfiguration::validate($this->env);
        return MailerConfiguration::createFromVariables($this->vars);
    }

    public function getWebsiteConfiguration(): WebsiteConfiguration
    {
        if ($this->vars === null) {
            $this->vars = $this->env->load();
        }
        WebsiteConfiguration::validate($this->env);
        return WebsiteConfiguration::createFromVariables($this->vars);
    }
}
