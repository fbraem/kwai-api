<?php
/**
 * @package Modules
 * @subpackage Users
 */
declare(strict_types=1);

namespace Kwai\Modules\Users\UseCases;

/**
 * Class UpdateUserCommand
 *
 * The command for the use case UpdateUser.
 */
class UpdateUserCommand
{
    public string $uuid;

    public ?string $email = null;

    public ?string $first_name = null;

    public ?string $last_name = null;

    public ?string $remark = null;
}
