<?php
/**
 * @package Modules
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
     * Constructor
     *
     * @param RecipientType $type
     * @param Address       $address
     */
    public function __construct(
        private RecipientType $type,
        private Address $address
    ) {
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
