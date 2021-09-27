<?php
/**
 * @package Modules
 * @subpackage Coaches
 */
declare(strict_types=1);

namespace Kwai\Modules\Coaches\UseCases;

/**
 * Class CreateCoachCommand
 */
class CreateCoachCommand
{
    public int $member_id;

    use CoachCommandTrait;
}
