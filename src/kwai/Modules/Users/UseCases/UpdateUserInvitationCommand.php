<?php
/**
 * @package Modules
 * @subpackage Users
 */
declare(strict_types=1);

namespace Kwai\Modules\Users\UseCases;

use Kwai\Core\Domain\ValueObjects\UniqueId;

/**
 * Class UpdateUserInvitationCommand
 *
 * Command for the update user invitation use case.
 */
final class UpdateUserInvitationCommand
{
    public string $uuid;
    public ?string $remark = null;
    public bool $renew = false;

    public function getUuid(): UniqueId
    {
        return new UniqueId($this->uuid);
    }
}
