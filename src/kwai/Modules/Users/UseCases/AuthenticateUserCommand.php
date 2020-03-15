<?php
/**
 * @package Kwai
 * @subpackage Users
 */
declare(strict_types = 1);

namespace Kwai\Modules\Users\UseCases;

/**
 * AuthenticateUserCommand is a DataTransferObject for the AuthenicateUser
 * usecase.
 */
final class AuthenticateUserCommand
{
    /**
     * Email
     * @var string
     */
    public $email;

    /**
     * Password
     * @var string
     */
    public $password;
}
