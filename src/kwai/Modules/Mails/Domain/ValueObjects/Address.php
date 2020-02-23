<?php
/**
 * @package Kwai/Modules
 * @subpackage Mails
 */
declare(strict_types = 1);

namespace Kwai\Modules\Mails\Domain\ValueObjects;

use Kwai\Core\Domain\EmailAddress;

/**
 * Address valueobject
 */
final class Address
{
    /**
     * The emailaddress
     * @var EmailAddress
     */
    private $email;

    /**
     * The name of for the address
     * @var string
     */
    private $name;

    public function __construct(
        EmailAddress $email,
        string $name
    ) {
        $this->email = $email;
        $this->name = $name;
    }

    public function getEmail(): EmailAddress
    {
        return $this->email;
    }

    public function getName(): string
    {
        return $this->name;
    }
}
