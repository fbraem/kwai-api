<?php
/**
 * @package Kwai
 * @subpackage Users
 */
declare(strict_types=1);

namespace Kwai\Modules\Users\UseCases;

/**
 * Class DetachAbilityFromUserCommand
 *
 * The command class for the use case DetachAbilityFromUser
 */
class DetachAbilityFromUserCommand
{
    /**
     * The unique id of the user.
     */
    public string $uuid;

    /**
     * The id of the ability to remove from the user.
     */
    public int $abilityId;
}
