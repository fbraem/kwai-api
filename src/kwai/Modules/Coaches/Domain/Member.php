<?php
/**
 * @package Modules
 * @subpackage Coaches
 */
declare(strict_types=1);

namespace Kwai\Modules\Coaches\Domain;

use Kwai\Core\Domain\DomainEntity;
use Kwai\Core\Domain\ValueObjects\Name;

/**
 * Class Member
 *
 * Entity that represents a member of the club
 */
class Member implements DomainEntity
{
    public function __construct(
        private Name $name
    ) {
    }

    /**
     * Get the name of the member
     *
     * @return Name
     */
    public function getName(): Name
    {
        return $this->name;
    }
}
