<?php
/**
 * @package Modules
 * @subpackage Users
 */
declare(strict_types = 1);

namespace Kwai\Modules\Users\UseCases;

/**
 * AuthenticateUserCommand
 *
 * Command for the AuthenticateUser usecase.
 */
final class AuthenticateUserCommand
{
    /**
     * Email
     */
    public string $email;

    /**
     * Password
     */
    public string $password;
}
