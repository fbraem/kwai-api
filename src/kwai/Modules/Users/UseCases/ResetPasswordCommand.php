<?php
/**
 * @package Modules
 * @subpackage Users
 */
declare(strict_types=1);

namespace Kwai\Modules\Users\UseCases;

/**
 * Class ResetPasswordCommand
 *
 * Command for the ResetPassword use case.
 */
final class ResetPasswordCommand
{
    public string $uuid;

    public string $password;
}