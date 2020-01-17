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

    public function __construct(string $password)
    {
        $this->password = $password;
    }

    public function __toString(): string
    {
        return $this->password;
    }

    public static function fromString(string $str): self
    {
        return new self($str);
    }

    public function verify(string $password)
    {
        return password_verify($password, $this->password);
    }
}
