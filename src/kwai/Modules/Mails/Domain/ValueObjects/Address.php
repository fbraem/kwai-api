<?php
/**
 * @package Kwai/Modules
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
}
