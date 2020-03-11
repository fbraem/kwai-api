<?php
/**
 * @package Kwai/Modules
 * @subpackage Mails
 */
declare(strict_types = 1);

namespace Kwai\Modules\Mails\Domain;

use Kwai\Core\Domain\DomainEntity;
use Kwai\Modules\Mails\Domain\ValueObjects\RecipientType;
use Kwai\Modules\Mails\Domain\ValueObjects\Address;

/**
 * Recipient Entity
 */
class Recipient implements DomainEntity
{
    /**
     * The type of the recipient
     * @var RecipientType
     */
    private $type;

    /**
     * The address of the recipient
     * @var Address
     */
    private $address;

    /**
     * Constructor
     * @param object $props Recipient properties
     */
    public function __construct(object $props)
    {
        $this->type = $props->type;
        $this->address = $props->address;
    }

    /**
     * Get the address
     * @return Address
     */
    public function getAddress(): Address
    {
        return $this->address;
    }

    /**
     * Get the type
     * @return RecipientType
     */
    public function getType(): RecipientType
    {
        return $this->type;
    }
}
