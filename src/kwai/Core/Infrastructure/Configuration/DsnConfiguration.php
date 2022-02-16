<?php
/**
 * @package Core
 * @subpackage Infrastructure
 */
declare(strict_types=1);

namespace Kwai\Core\Infrastructure\Configuration;

use Stringable;

/**
 * Class DsnConfiguration
 */
class DsnConfiguration implements Stringable
{
    public function __construct(private string $dsn)
    {
    }

    /**
     * @return string
     */
    public function getDsn(): string
    {
        return $this->dsn;
    }

    public function __toString(): string
    {
        return $this->dsn;
    }

    /**
     * Factory method to create a configuration.
     * User, password and host will be encoded.
     *
     * @param string $user
     * @param string $pwd
     * @param string $host
     * @param int    $port
     * @param string $scheme
     * @return static
     */
    public static function create(
        string $scheme,
        string $user,
        string $pwd,
        string $host,
        int $port,
    ): self
    {
        $user = urlencode($user);
        $pwd = urlencode($pwd);
        $host = urlencode($host);
        return new self(
            "$scheme://$user:$pwd@$host:$port"
        );
    }
}
