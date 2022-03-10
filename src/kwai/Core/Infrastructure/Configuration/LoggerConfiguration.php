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
use Psr\Log\LoggerInterface;
use Psr\Log\LogLevel;

/**
 * Class LoggerConfiguration
 *
 * Handle Dotenv configuration for a logger.
 *
 * A prefix can be used to distinguish between multiple loggers. For example:
 * the prefix "KWAI_DB_" can be used to configure a logger for the database.
 * KWAI_DB_LOG_FILE and KWAI_DB_LOG_LEVEL will be the variables for this logger.
 */
class LoggerConfiguration implements Configurable
{
    private ?string $file = null;

    private string $level = LogLevel::INFO;

    public function __construct(
        private ?string $prefix = null
    ) {
    }

    public function load(array $variables): void
    {
        if ($this->prefix) {
            $this->file = $variables[$this->prefix . 'LOG_FILE'] ?? null;
            if (isset($variables[$this->prefix . 'LOG_LEVEL'])) {
                $this->level = $variables[$this->prefix . 'LOG_LEVEL'];
            }
        } else {
            $this->file = $variables['LOG_FILE'] ?? null;
            if (isset($variables['LOG_LEVEL'])) {
                $this->level = $variables['LOG_LEVEL'];
            }
        }
    }

    public function validate(Dotenv $env): void
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
