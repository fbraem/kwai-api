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
    private array $origin;

    private array $methods;

    private int $port;

    /**
     * @param string                $scheme
     * @param string|null           $host
     * @param int|null              $port
     * @param string[]|string $origin
     * @param string[]|string $methods
     */
    public function __construct(
        private string $scheme = 'http',
        private ?string $host = null,
        ?int $port = null,
        array|string $origin = ['*'],
        array|string $methods = ['GET', 'POST', 'PUT', 'PATCH', 'DELETE', 'OPTIONS']
    ) {
        $this->port = $port ?: ($scheme === 'http' ? 80 : 443);
        if (is_string($origin)) {
            $this->origin = array_map(
                static fn($element) => trim($element),
                explode(',', $origin)
            );
        } else {
            $this->origin = $origin;
        }
        if (is_string($methods)) {
            $this->methods = array_map(
                static fn($element) => trim($element),
                explode(',', $methods)
            );
        } else {
            $this->methods = $methods;
        }
    }

    public static function createFromVariables(array $variables): self
    {
        $parameters = [
            'KWAI_CORS_SCHEME' => 'scheme',
            'KWAI_CORS_HOST' => 'host',
            'KWAI_CORS_PORT' => 'port',
            'KWAI_CORS_ORIGIN' => 'origin',
            'KWAI_CORS_METHODS' => 'methods'
        ];
        $args = [];
        foreach($parameters as $variableName => $argName) {
            if (isset($variables[$variableName])) {
                $args[$argName] = $variables[$variableName];
            }
        }
        if (isset($args['port'])) {
            $args['port'] = intval($args['port']);
        }
        return new self(...$args);
    }

    public static function validate(Dotenv $env): void
    {
        $env->ifPresent('KWAI_CORS_PORT')->isInteger();
    }

    /**
     * Creates a Cors Setting. When there is no host configured, null will be
     * returned.
     *
     * @return Settings|null
     */
    public function createCorsSettings(): ?Settings
    {
        if ($this->host === null) {
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
