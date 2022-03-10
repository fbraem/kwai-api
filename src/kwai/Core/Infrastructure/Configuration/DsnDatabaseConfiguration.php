<?php
/**
 * @package Core
 * @subpackage Infrastructure
 */
declare(strict_types=1);

namespace Kwai\Core\Infrastructure\Configuration;

use Stringable;

/**
 * Class DsnDatabaseConfiguration
 */
class DsnDatabaseConfiguration implements Stringable
{
    private string $driver;

    private array $parameters = [];

    public function __construct(
        private string $dsn
    ) {
        $parts = explode(':', $dsn, 2);
        if (count($parts) != 2) {
            throw new \InvalidArgumentException('Invalid DSN specified');
        }
        $this->driver = $parts[0];
        $parameter_strings = explode(';', $parts[1]);
        foreach ($parameter_strings as $parameter) {
            $parameter_parts = explode('=', $parameter, 2);
            if (count($parameter_parts) != 2) {
                throw new \InvalidArgumentException('Invalid DSN specified');
            }
            $this->parameters[$parameter_parts[0]] = $parameter_parts[1];
        }
    }

    public function getDriver(): string
    {
        return $this->driver;
    }

    public function __toString(): string
    {
        return $this->dsn;
    }

    public function getDatabaseName(): string
    {
        return $this->parameters['dbname'];
    }

    public function getHost(): string
    {
        return $this->parameters['host'];
    }

    public function getCharset(): string
    {
        return $this->parameters['charset'];
    }
}
