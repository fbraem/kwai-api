<?php
/**
 * @package Kwai
 * @subpackage Users
 */
declare(strict_types=1);

namespace Kwai\Modules\Users\UseCases;

/**
 * Class GetAbilityCommand
 *
 * Command for the GetAbility use case.
 */
class GetAbilityCommand
{
    /**
     * The id of an ability.
     */
    public int $id;
}
