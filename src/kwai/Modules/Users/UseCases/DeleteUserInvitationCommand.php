<?php
/**
 * @package Modules
 * @subpackage Users
 */
declare(strict_types=1);

namespace Kwai\Modules\Users\UseCases;

/**
 * Class DeleteUserInvitationCommand
 *
 * Command for the DeleteUserInvitation use case.
 */
class DeleteUserInvitationCommand
{
    public string $uuid;
}
