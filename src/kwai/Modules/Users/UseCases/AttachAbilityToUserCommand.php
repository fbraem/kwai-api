<?php
/**
 * @package Modules
 * @subpackage Users
 */
declare(strict_types=1);

namespace Kwai\Modules\Users\UseCases;

/**
 * Class AttachAbilityToUserCommand
 *
 * The command class for the use case AttachAbilityToUser
 */
class AttachAbilityToUserCommand
{
    /**
     * The unique id of the user.
     */
    public string $uuid;

    /**
     * The id of the ability to add to the user.
     */
    public int $abilityId;
}
