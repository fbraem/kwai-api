<?php
/**
 * @package Core
 * @subpackage Infrastructure
 */
declare(strict_types=1);

namespace Kwai\Core\Infrastructure\Configuration;

use Dotenv\Dotenv;
use Monolog\Handler\StreamHandler;
use Monolog\Logger;
use PDepend\Util\Log;
use Psr\Log\LoggerInterface;
use Psr\Log\LogLevel;

/**
 * Class LoggerConfiguration
 *
 * Handle Dotenv configuration for a logger.
 */
class LoggerConfiguration implements Configurable
{
    public function __construct(
        private readonly ?string $file = null,
        private readonly string $level = LogLevel::INFO
    ) {
    }

    public static function createFromVariables(array $variables): self
    {
        return new self(
            $variables['LOG_FILE'] ?? null,
            $variables['LOG_LEVEL'] ?? LogLevel::INFO
        );
    }

    public static function createFromPrefixedVariables(
        string $prefix,
        array $variables
    ): self
    {
        return new self(
            $variables[$prefix . 'LOG_FILE'] ?? null,
            $variables[$prefix . 'LOG_LEVEL'] ?? LogLevel::INFO
        );
    }

    public static function validate(Dotenv $env): void
    {
    }

    /**
     * Create a logger with the resolved Dotenv variables.
     *
     * @param string $name
     * @return LoggerInterface
     */
    public function createLogger(string $name): LoggerInterface
    {
        $logger = new Logger($name);
        if ($this->file) {
            $logger->pushHandler(
                new StreamHandler(
                    $this->file,
                    $this->level
                )
            );
        }
        return $logger;
    }
}
