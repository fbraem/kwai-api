<?php
/**
 * @package Modules
 * @subpackage Users
 */
declare(strict_types=1);

namespace Kwai\Modules\Users\UseCases;

use Kwai\Core\Domain\ValueObjects\UniqueId;

/**
 * Class RenewUserInvitationCommand
 *
 * Command for the RenewUserInvitation use case.
 */
class RenewUserInvitationCommand
{
    public string $uuid;
    public int $expiration = 15;

    public function getUniqueId(): UniqueId
    {
        return new UniqueId($this->uuid);
    }
}
