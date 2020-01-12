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
     * The firstname of the user.
     * @var string
     */
    private $firstname;

    /**
     * The lastname of the user.
     * @var string
     */
    private $lastname;

    /**
     * Constructor
     */
    public function __construct(?string $firstname, ?string $lastname)
    {
        $this->firstname = $firstname;
        $this->lastname = $lastname;
    }

    /**
     * Returns a string representation
     */
    public function __toString(): string
    {
        return $this->firstname . ' ' . $this->lastname;
    }
}
