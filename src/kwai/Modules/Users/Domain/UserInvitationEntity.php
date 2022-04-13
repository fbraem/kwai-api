<?php
/**
 * @package Modules
 * @subpackage Users
 */
declare(strict_types=1);

namespace Kwai\Modules\Users\Domain;

use Kwai\Core\Domain\EntityTrait;

/**
 * Class UserInvitationEntity
 * @mixin UserInvitation
 */
class UserInvitationEntity
{
    use EntityTrait;

    /**
     * @param int $id
     * @param UserInvitation $domain
     */
    public function __construct(
        private readonly int $id,
        private readonly UserInvitation $domain
    ) {
    }

    public function domain(): UserInvitation
    {
        return $this->domain;
    }
}
