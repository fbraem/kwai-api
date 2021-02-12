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
use Psr\Container\ContainerInterface;

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
                fn(ContainerInterface $container) => new LoginAction($container)
            )
            ->post(
                'auth.logout',
                '/auth/logout',
                fn(ContainerInterface $container) => new LogoutAction($container),
                [
                    'auth' => true
                ]
            )
            ->post(
                'auth.access_token',
                '/auth/access_token',
                fn(ContainerInterface $container) => new RefreshTokenAction($container),
            )
            ->get(
                'auth.get',
                '/auth',
                fn(ContainerInterface $container) => new GetUserAction($container),
                [
                    'auth' => true
                ]
            )
        ;
    }
}
