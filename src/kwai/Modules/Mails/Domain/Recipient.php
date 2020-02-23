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
     * @param RecipientType $type    The type of the recipient
     * @param Address       $address The address of the recipient
     */
    public function __construct(
        RecipientType $type,
        Address $address
    ) {
        $this->type = $type;
        $this->address = $address;
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
