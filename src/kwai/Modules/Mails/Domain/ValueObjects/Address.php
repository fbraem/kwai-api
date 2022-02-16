<?php
/**
 * @package Modules
 * @subpackage Mails
 */
declare(strict_types = 1);

namespace Kwai\Modules\Mails\Domain\ValueObjects;

use Kwai\Core\Domain\ValueObjects\EmailAddress;

/**
 * Address value object
 */
final class Address
{
    /**
     * Address constructor.
     * @param EmailAddress $email
     * @param string $name
     */
    public function __construct(
        private EmailAddress $email,
        private string $name = ''
    ) {
    }

    /**
     * Get email address
     */
    public function getEmail(): EmailAddress
    {
        return $this->email;
    }

    /**
     * Get name
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * Factory method for creating an address.
     *
     * $address can be a string with an email address, a list array
     * with the first element an email address and an optional second element
     * with the name. Or it can be an associative array with the key as email
     * and the value as name.
     *
     * @param string|array $address
     * @return static
     */
    public static function create(string|array $address): self
    {
        if (is_array($address)) {
            if (array_is_list($address)) {
                return new self(new EmailAddress($address[0]), $address[1] ?? '');
            }
            $first_key = array_key_first($address);
            return new self(new EmailAddress($first_key), $address[$first_key]);
        }
        return new self(new EmailAddress($address));
    }
}
