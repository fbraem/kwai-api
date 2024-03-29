<?php
/**
 * Class EmailAddress
 */
declare(strict_types = 1);

namespace Kwai\Core\Domain\ValueObjects;

use InvalidArgumentException;
use Stringable;

/**
 * Value object for an email address.
 */
final class EmailAddress implements Stringable
{
    /**
     * The username part of the email address.
     */
    private string $userName;

    /**
    * The domain part of the email address.
     */
    private string $domain;

    /**
     * Constructs a new EmailAddress.
     *
     * @param string $emailAddress
     * @throws InvalidArgumentException Thrown when the email address is invalid
     */
    public function __construct(string $emailAddress)
    {
        if (filter_var($emailAddress, FILTER_VALIDATE_EMAIL)) {
            list($this->userName, $this->domain) = explode('@', $emailAddress);
        } else {
            throw new InvalidArgumentException(
                "'$emailAddress' is not a valid email address."
            );
        }
    }

    /**
     * Returns a string representation.
     *
     * @return string The full email address.
     */
    public function __toString(): string
    {
        return $this->userName . '@' . $this->domain;
    }
}
