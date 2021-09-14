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
    public string $bio;
    public string $diploma;
    public bool $active = false;
    public ?string $remark = null;
    public ?int $user_id;
}
