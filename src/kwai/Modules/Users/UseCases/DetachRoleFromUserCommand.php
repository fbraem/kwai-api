<?php
/**
 * @package Kwai
 * @subpackage Users
 */
declare(strict_types=1);

namespace Kwai\Modules\Users\UseCases;

/**
 * Class DetachRoleFromUserCommand
 *
 * The command class for the use case DetachRoleFromUser
 */
class DetachRoleFromUserCommand
{
    /**
     * The unique id of the user.
     */
    public string $uuid;

    /**
     * The id of the role to remove from the user.
     */
    public int $roleId;
}
