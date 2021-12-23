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
 * Class User
 *
 * A user that is also a coach.
 */
class User implements DomainEntity
{
    public function __construct(
        private Name $name
    ) {
    }

    public function getName(): Name
    {
        return $this->name;
    }
}
