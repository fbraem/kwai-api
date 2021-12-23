<?php
/**
 * @package Application
 * @subpackage Auth
 */
declare(strict_types=1);

namespace Kwai\Applications;

use Kwai\Modules\Users\Presentation\REST\GetUserAction;
use Kwai\Modules\Users\Presentation\REST\LoginAction;
use Kwai\Modules\Users\Presentation\REST\LogoutAction;
use Kwai\Modules\Users\Presentation\REST\RefreshTokenAction;
use Kwai\Core\Infrastructure\Presentation\Router;

/**
 * Class AuthApplication
 */
class AuthApplication extends Application
{
    /**
     * @inheritDoc
     */
    public function createRouter(): Router
    {
        return Router::create()
            ->post(
                'auth.login',
                '/auth/login',
                LoginAction::class
            )
            ->post(
                'auth.logout',
                '/auth/logout',
                LogoutAction::class,
                [
                    'auth' => true
                ]
            )
            ->post(
                'auth.access_token',
                '/auth/access_token',
                RefreshTokenAction::class,
            )
            ->get(
                'auth.get',
                '/auth',
                GetUserAction::class,
                [
                    'auth' => true
                ]
            )
        ;
    }
}
