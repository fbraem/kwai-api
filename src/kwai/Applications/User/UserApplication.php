<?php
/**
 * @package Application
 * @subpackage User
 */
declare(strict_types=1);

namespace Kwai\Applications\User;

use Kwai\Applications\Application;
use Kwai\Applications\User\Actions\ConfirmInvitationAction;
use Kwai\Applications\User\Actions\GetUserAction;
use Kwai\Applications\User\Actions\GetUserInvitationAction;
use Kwai\Applications\User\Actions\LoginAction;
use Kwai\Applications\User\Actions\LogoutAction;
use Kwai\Applications\User\Actions\RefreshTokenAction;
use Kwai\Core\Infrastructure\Presentation\Router;
use Psr\Container\ContainerInterface;

/**
 * Class UserApplication
 */
class UserApplication extends Application
{
    /**
     * @inheritDoc
     */
    public function createRouter(): Router
    {
        $uuid_regex = Application::UUID_REGEX;

        return Router::create()
            ->post(
                'user.login',
                '/user/login',
                fn(ContainerInterface $container) => new LoginAction($container)
            )
            ->post(
                'user.logout',
                '/user/logout',
                fn(ContainerInterface $container) => new LogoutAction($container),
                [
                    'auth' => true
                ]
            )
            ->post(
                'user.access_token',
                '/user/access_token',
                fn(ContainerInterface $container) => new RefreshTokenAction($container),
            )
            ->get(
                'user.get',
                '/user',
                fn(ContainerInterface $container) => new GetUserAction($container),
                [
                    'auth' => true
                ]
            )
            ->get(
                'users.invitations.tokens',
                '/user/invitations/{uuid}',
                fn(ContainerInterface $container) => new GetUserInvitationAction($container),
                requirements: [
                    'uuid' => $uuid_regex
                ]
            )
            ->post(
                'users.invitations.confirm',
                '/user/invitations/{uuid}',
                fn(ContainerInterface $container) => new ConfirmInvitationAction($container),
                requirements: [
                    'uuid' => $uuid_regex
                ]
            )
        ;
    }
}
