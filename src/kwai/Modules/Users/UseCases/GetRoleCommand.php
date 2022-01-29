<?php
/**
 * @package Modules
 * @subpackage Users
 */
declare(strict_types=1);

namespace Kwai\Modules\Users\UseCases;

/**
 * Class GetRoleCommand
 *
 * Command for the GetRole use case.
 */
class GetRoleCommand
{
    /**
     * The id of an role.
     */
    public int $id;
}
