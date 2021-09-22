<?php
/**
 * @package Modules
 * @subpackage Club
 */
declare(strict_types=1);

namespace Kwai\Modules\Club\Domain;

use Kwai\Core\Domain\DomainEntity;
use Kwai\Core\Domain\ValueObjects\Name;

/**
 * Class Member
 *
 * Represents a member of a club.
 */
class Member implements DomainEntity
{
    /**
     * Constructor.
     */
    public function __construct(
        private Name $name
    ) {
    }
}
