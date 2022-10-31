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
 * Command for use case ChangePassword.
 */
final class ChangePasswordCommand
{
    public string $password;
}