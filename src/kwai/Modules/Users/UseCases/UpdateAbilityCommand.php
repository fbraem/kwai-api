<?php
/**
 * @package Kwai
 * @subpackage Users
 */
declare(strict_types=1);

namespace Kwai\Modules\Users\UseCases;

/**
 * Class UpdateAbilityCommand
 *
 * Command for the use case UpdateAbility
 */
class UpdateAbilityCommand
{
    /**
     * The id of the ability to update
     */
    public int $id;

    /**
     * The name for the ability
     */
    public string $name;

    /**
     * The remark for the ability
     */
    public ?string $remark = null;

    /**
     * An array with ids of rules for the ability
     * @var int[]
     */
    public array $rules = [];
}
