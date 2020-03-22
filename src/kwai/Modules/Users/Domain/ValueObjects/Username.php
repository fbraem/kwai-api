<?php
/**
 * Username value object
 *
 * @package Kwai/Modules
 * @subpackage Users
 */
declare(strict_types = 1);

namespace Kwai\Modules\Users\Domain\ValueObjects;

/**
 * Value object for a username.
 */
final class Username
{
    /**
     * The first name of the user.
     */
    private ?string $firstName;

    /**
     * The last name of the user.
     */
    private ?string $lastName;

    /**
     * Constructor
     * @param string|null $firstName
     * @param string|null $lastName
     */
    public function __construct(?string $firstName = null, ?string $lastName = null)
    {
        $this->firstName = $firstName;
        $this->lastName = $lastName;
    }

    /**
     * Returns a string representation
     */
    public function __toString(): string
    {
        return join(
            ' ',
            array_filter([$this->firstName, $this->lastName])
        );
    }

    /**
     * Get first name
     * @return string|null
     */
    public function getFirstName(): ?string
    {
        return $this->firstName;
    }

    /**
     * Get last name
     * @return string|null
     */
    public function getLastName(): ?string
    {
        return $this->lastName;
    }
}
