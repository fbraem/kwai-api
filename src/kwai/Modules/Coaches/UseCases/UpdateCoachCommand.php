<?php
/**
 * @package Modules
 * @subpackage Coaches
 */
declare(strict_types=1);

namespace Kwai\Modules\Coaches\UseCases;

/**
 * Class UpdateCoachCommand
 */
class UpdateCoachCommand
{
    public int $id;

    use CoachCommandTrait;
}
