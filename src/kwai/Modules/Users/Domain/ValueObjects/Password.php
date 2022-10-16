<?php
/**
 * Password value object
 *
 * @package Kwai/Modules
 * @subpackage Users
 */
declare(strict_types = 1);

namespace Kwai\Modules\Users\Domain\ValueObjects;

use InvalidArgumentException;

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
        $strength = 0;
        if (strlen($str) > 7) {
            $strength++;
        }
        if (preg_match("([a-z])", $str) && preg_match("([A-Z])", $str)) {
            $strength++;
        }
        if (preg_match("([0-9])", $str)) {
            $strength++;
        }
        if ($strength < 3) {
            throw new InvalidArgumentException('Password is not strong enough');
        }
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
