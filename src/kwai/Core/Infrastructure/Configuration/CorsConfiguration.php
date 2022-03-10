<?php
/**
 * @package Core
 * @subpackage Infrastructure
 */
declare(strict_types=1);

namespace Kwai\Core\Infrastructure\Configuration;

use Dotenv\Dotenv;
use Neomerx\Cors\Strategies\Settings;

/**
 * Class CorsConfiguration
 *
 * Configuration for CORS.
 */
class CorsConfiguration implements Configurable
{
    private string $scheme;

    private ?string $host = null;

    private int $port;

    private array $origin = ['*'];

    private array $methods = ['GET', 'POST', 'PUT', 'PATCH', 'DELETE', 'OPTIONS'];

    public function load(array $variables): void
    {
        $this->scheme = $variables['KWAI_CORS_SCHEME'] ?? 'http';
        $this->host = $variables['KWAI_CORS_HOST'] ?? null;
        $this->port = $variables['KWAI_CORS_PORT'] ?? $this->scheme === 'http' ? 80 : 443;
        $origin = $variables['KWAI_CORS_ORIGIN'] ?? null;
        if ($origin) {
            $this->origin = array_map(
                static fn($element) => trim($element),
                explode(',', $origin)
            );
        }
        $methods = $variables['KWAI_CORS_METHODS'] ?? null;
        if ($methods) {
            $this->methods = array_map(
                static fn($element) => trim($element),
                explode(',', $methods)
            );
        }
    }

    public function validate(Dotenv $env): void
    {
        // TODO: Implement validate() method.
    }

    public function createCorsSettings(): ?Settings
    {
        if ($this->host !== null) {
            return null;
        }

        $corsSettings = new Settings();
        $corsSettings->init(
            $this->scheme,
            $this->host,
            $this->port
        );
        $corsSettings->enableCheckHost();
        $corsSettings->setAllowedOrigins($this->origin);
        $corsSettings->setAllowedMethods($this->methods);
        $corsSettings->setAllowedHeaders(['Accept', 'Accept-Language', 'Content-Type', 'Authorization']);
        $corsSettings->setCredentialsSupported();

        return $corsSettings;
    }
}
