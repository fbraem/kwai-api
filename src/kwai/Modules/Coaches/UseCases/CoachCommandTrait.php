<?php
/**
 * @package Modules
 * @subpackage Coaches
 */
declare(strict_types=1);


namespace Kwai\Modules\Coaches\UseCases;


trait CoachCommandTrait
{
    public ?string $bio = null;
    public ?string $diploma = null;
    public bool $active = false;
    public ?string $remark = null;
    public ?int $user_id = null;
}
