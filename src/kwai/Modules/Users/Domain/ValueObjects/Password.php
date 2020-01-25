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
     * The password of the user.
     * @var string
     */
    private $password;

    /**
     * Constructor.
     * @param string $password A hashed string used as password.
     */
    public function __construct(string $password)
    {
        $this->password = $password;
    }

    public function __toString(): string
    {
        return $this->password;
    }

    /**
     * Creates a new password. String will be hashed.
     * @param  string $str
     * @return self
     */
    public static function fromString(string $str): self
    {
        return new self(password_hash($str, PASSWORD_DEFAULT));
    }

    /**
     * Verify this password against the given password.
     * @param  string $password
     * @return bool             Returns true when the password matches
     */
    public function verify(string $password)
    {
        return password_verify($password, $this->password);
    }
}
