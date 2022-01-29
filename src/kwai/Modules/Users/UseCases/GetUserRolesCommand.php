<?php
/**
 * @package Modules
 * @subpackage Users
 */
declare(strict_types=1);

namespace Kwai\Modules\Users\UseCases;

/**
 * GetUserRolesCommand
 *
 * Command for the GetUserRoles use case.
 */
class GetUserRolesCommand
{
    public string $uuid;
}
