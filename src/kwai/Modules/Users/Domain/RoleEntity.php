<?php
/**
 * @package Modules
 * @subpackage Users
 */
declare(strict_types=1);

namespace Kwai\Modules\Users\Domain;

use Kwai\Core\Domain\EntityTrait;

/**
 * Class RoleEntity
 *
 * @mixin Role
 */
class RoleEntity
{
    use EntityTrait;

    public function __construct(
        private readonly int $id,
        private readonly Role $domain
    ) {
    }

    public function domain(): Role
    {
        return $this->domain;
    }
}
