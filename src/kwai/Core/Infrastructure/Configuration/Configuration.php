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
        $config = new DatabaseConfiguration();
        $config->validate($this->env);
        $config->load($this->vars);
        return $config;
    }

    public function getCorsConfiguration(): CorsConfiguration
    {
        if ($this->vars === null) {
            $this->vars = $this->env->load();
        }
        $config = new CorsConfiguration();
        $config->validate($this->env);
        $config->load($this->vars);
        return $config;
    }

    public function getLoggerConfiguration(): LoggerConfiguration
    {
        if ($this->vars === null) {
            $this->vars = $this->env->load();
        }
        $config = new LoggerConfiguration();
        $config->validate($this->env);
        $config->load($this->vars);
        return $config;
    }

    public function getSecurityConfiguration(): SecurityConfiguration
    {
        if ($this->vars === null) {
            $this->vars = $this->env->load();
        }
        $config = new SecurityConfiguration();
        $config->validate($this->env);
        $config->load($this->vars);
        return $config;
    }

    public function getMailerConfiguration(): MailerConfiguration
    {
        if ($this->vars === null) {
            $this->vars = $this->env->load();
        }
        $config = new MailerConfiguration();
        $config->validate($this->env);
        $config->load($this->vars);
        return $config;
    }

    public function getWebsiteConfiguration(): WebsiteConfiguration
    {
        if ($this->vars === null) {
            $this->vars = $this->env->load();
        }
        $config = new WebsiteConfiguration();
        $config->validate($this->env);
        $config->load($this->vars);
        return $config;
    }
}
