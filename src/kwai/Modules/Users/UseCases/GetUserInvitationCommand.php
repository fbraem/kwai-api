<?php
/**
 * @package Modules
 * @subpackage Users
 */
declare(strict_types=1);

namespace Kwai\Modules\Users\UseCases;

use Kwai\Core\Domain\ValueObjects\UniqueId;

/**
 * Class GetUserInvitationCommand
 *
 * Command for the GetUserInvitation use case.
 */
class GetUserInvitationCommand
{
    /**
     * @param string $uuid
     */
    public function __construct(private string $uuid)
    {
    }

    public function getUuid(): UniqueId
    {
        return new UniqueId($this->uuid);
    }
}
