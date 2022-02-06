<?php
/**
 * @package Application
 * @subpackage Auth
 */
declare(strict_types=1);

namespace Kwai\Applications\Auth;

use Kwai\Applications\Application;
use Kwai\Applications\Auth\Actions\LoginAction;
use Kwai\Applications\Auth\Actions\LogoutAction;
use Kwai\Applications\Auth\Actions\RefreshTokenAction;
use Kwai\Applications\Users\Actions\GetUserAction;

/**
 * Class AuthApplication
 */
class AuthApplication extends Application
{
    protected function getActions(): array
    {
        return [
            LoginAction::class,
            LogoutAction::class,
            RefreshTokenAction::class,
            GetUserAction::class
        ];
    }
}
