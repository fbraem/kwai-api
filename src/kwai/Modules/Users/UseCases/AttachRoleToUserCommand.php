<?php
/**
 * @package Modules
 * @subpackage Users
 */
declare(strict_types=1);

namespace Kwai\Modules\Users\UseCases;

/**
 * Class AttachRoleToUserCommand
 *
 * The command class for the use case AttachRoleToUser
 */
class AttachRoleToUserCommand
{
    /**
     * The unique id of the user.
     */
    public string $uuid;

    /**
     * The id of the role to add to the user.
     */
    public int $roleId;
}
