<?php
/**
 * @package
 * @subpackage
 */
declare(strict_types=1);

namespace Kwai\Core\Domain\ValueObjects;

/**
 * Class Name
 *
 * A value object for a name.
 */
class Name
{
    /**
     * Name constructor.
     *
     * @param string|null $firstname
     * @param string|null $lastname
     */
    public function __construct(
        private ?string $firstname = null,
        private ?string $lastname = null)
    {
    }

    public function __toString() : string
    {
        return implode(
            ' ',
            array_filter([$this->firstname, $this->lastname])
        );
    }

    /**
     * Get the firstname
     *
     * @return string|null
     */
    public function getFirstName(): ?string
    {
        return $this->firstname;
    }

    /**
     * Get the lastname
     *
     * @return string|null
     */
    public function getLastName(): ?string
    {
        return $this->lastname;
    }
}
