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
    public string $uuid;
}
