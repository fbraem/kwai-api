<?php
/**
 * @package Kwai
 * @subpackage Users
 */
declare(strict_types = 1);

namespace Kwai\Modules\Users\UseCases;

/**
 * AuthenticateUserCommand is a datatransferobject for AuthenicateUser usecase.
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
