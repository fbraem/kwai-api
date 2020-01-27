<?php
/**
 * Class EmailAddress
 */
declare(strict_types = 1);

namespace Kwai\Core\Domain;

/**
 * Value object for an email address.
 */
final class EmailAddress
{
    /**
     * @var string $userName is the username part of the emailaddress.
     */
    private $userName;

    /**
    * @var string $domain is the domain part of the emailaddress.
     */
    private $domain;

    /**
     * Constructs a new EmailAddress.
     *
     * @param string $emailAddress
     * @throws \InvalidArgumentException Thrown when the emailaddress is invalid
     */
    public function __construct(string $emailAddress)
    {
        if (filter_var($emailAddress, FILTER_VALIDATE_EMAIL)) {
            list($this->userName, $this->domain) = explode('@', $emailAddress);
        } else {
            throw new \InvalidArgumentException(
                "'$emailAddress' is not a valid emailaddress."
            );
        }
    }

    /**
     * Returns a string representation.
     *
     * @return string The full emailaddress.
     */
    public function __toString(): string
    {
        return $this->userName . '@' . $this->domain;
    }
}
