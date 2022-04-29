<?php
/**
 * @package Modules
 * @subpackage Users
 */
declare(strict_types=1);

namespace Kwai\Modules\Users\Domain;

use Kwai\Core\Domain\EntityTrait;

/**
 * Class UserEntity
 * @mixin User
 */
class UserEntity
{
    use EntityTrait;

    public function __construct(
        private readonly int $id,
        private readonly User $domain
    ) {
    }

    public function domain(): User
    {
        return $this->domain;
    }
}
