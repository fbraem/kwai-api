<?php
/**
 * @package Modules
 * @subpackage Users
 */
declare(strict_types=1);

namespace Kwai\Modules\Users\Domain;

use Kwai\Core\Domain\EntityTrait;

/**
 * Class UserRecoveryEntity
 *
 * @mixin UserRecovery
 */
final class UserRecoveryEntity
{
    use EntityTrait;

    public function __construct(
        private readonly int $id,
        private readonly UserRecovery $domain
    ) {
    }

    public function domain(): UserRecovery
    {
        return $this->domain;
    }
}