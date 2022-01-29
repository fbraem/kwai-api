<?php
/**
 * @package Modules
 * @subpackage Users
 */
declare(strict_types=1);

namespace Kwai\Modules\Users\UseCases;

/**
 * Class UpdateRoleCommand
 *
 * Command for the use case UpdateRole
 */
class UpdateRoleCommand
{
    /**
     * The id of the role to update
     */
    public int $id;

    /**
     * The name for the role
     */
    public string $name;

    /**
     * The remark for the role
     */
    public ?string $remark = null;

    /**
     * An array with ids of rules for the role
     * @var int[]
     */
    public array $rules = [];
}
