<?php
/**
 * Password value object
 *
 * @package Kwai/Modules
 * @subpackage Users
 */
declare(strict_types = 1);

namespace Kwai\Modules\Users\Domain\ValueObjects;

/**
 * Value object for a password.
 */
final class Password
{
    /**
     * Constructor.
     * @param string $password A hashed string used as password.
     */
    public function __construct(
        private readonly string $password
    ) {
    }

    public function __toString(): string
    {
        return $this->password;
    }

    /**
     * Creates a new password. String will be hashed.
     */
    public static function fromString(string $str): self
    {
        return new self(password_hash($str, PASSWORD_DEFAULT));
    }

    /**
     * Verify this password against the given password.
     * Returns true when the password matches
     */
    public function verify(string $password): bool
    {
        return password_verify($password, $this->password);
    }
}
