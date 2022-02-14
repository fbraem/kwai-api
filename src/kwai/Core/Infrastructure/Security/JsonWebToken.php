<?php
/**
 * @package Core
 * @subpackage Infrastructure
 */
declare(strict_types=1);

namespace Kwai\Core\Infrastructure\Security;

use Firebase\JWT\JWT;
use Firebase\JWT\Key;
use stdClass;

/**
 * Class JsonWebToken
 */
class JsonWebToken
{
    public function __construct(
        private string $secret,
        private string $algorithm = 'HS256',
        private object $tokenObject = new stdClass()
    ) {
    }

    public function encode(): string
    {
        return JWT::encode($this->tokenObject, $this->secret, $this->algorithm);
    }

    public function withProperty(string $property, string $value): self
    {
        $this->tokenObject->{$property} = $value;
        return $this;
    }

    public function getObject(): object
    {
        return clone($this->tokenObject);
    }

    public static function decode(
        string $token,
        string $secret,
        string $algorithm = 'HS256',
    ): self {
        return new self(
            $secret,
            $algorithm,
            JWT::decode($token, new Key($secret, $algorithm))
        );
    }
}
