<?php
/**
 * @package Modules
 * @subpackage Users
 */
declare(strict_types=1);

namespace Kwai\Modules\Users\UseCases;

/**
 * Class CreateRoleCommand
 *
 * Command for use case CreateRole
 */
class CreateRoleCommand
{
    /**
     * The name for the role
     */
    public string $name;

    /**
     * The remark for the role
     */
    public ?string $remark = null;

    /**
     * An array with ids of rules for the roles
     * @var int[]
     */
    public array $rules = [];
}
