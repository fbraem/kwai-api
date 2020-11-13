<?php
/**
 * @package Kwai
 * @subpackage Users
 */
declare(strict_types=1);

namespace Kwai\Modules\Users\UseCases;

/**
 * Class GetUserInvitationCommand
 *
 * Command for the GetUserInvitation use case.
 */
class GetUserInvitationCommand
{
    /**
     * The unique id of a user invitation.
     */
    public string $uuid;
}
