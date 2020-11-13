<?php
/**
 * @package Kwai/Modules
 * @subpackage Mails
 */
declare(strict_types = 1);

namespace Kwai\Modules\Mails\Domain\ValueObjects;

use Kwai\Core\Domain\ValueObjects\EmailAddress;

/**
 * Address valueobject
 */
final class Address
{
    /**
     * The emailaddress
     */
    private EmailAddress $email;

    /**
     * The name of for the address
     */
    private string $name;

    /**
     * Address constructor.
     * @param EmailAddress $email
     * @param string $name
     */
    public function __construct(
        EmailAddress $email,
        string $name = ''
    ) {
        $this->email = $email;
        $this->name = $name;
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
