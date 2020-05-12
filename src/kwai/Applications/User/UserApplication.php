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
use Kwai\Applications\User\Actions\LoginAction;
use Kwai\Applications\User\Actions\LogoutAction;
use Kwai\Applications\User\Actions\RefreshTokenAction;
use Slim\Routing\RouteCollectorProxy;

/**
 * Class UserApplication
 */
class UserApplication extends Application
{
    /**
     * UserApplication constructor.
     */
    public function __construct()
    {
        parent::__construct('user');
    }

    /**
     * @inheritDoc
     */
    public function createRoutes(RouteCollectorProxy $group): void
    {
        $uuid_regex = Application::UUID_REGEX;

        $group->post('/login', LoginAction::class)
            ->setName('user.login')
        ;
        $group->post('/logout', LogoutAction::class)
            ->setName('user.logout')
            ->setArgument('auth', 'true')
        ;
        $group->post('/access_token', RefreshTokenAction::class)
            ->setName('user.access_token')
        ;
        $group->get("/$uuid_regex", GetUserAction::class)
            ->setName('user.get')
            ->setArgument('auth', 'true')
        ;
        $group->post("/invitations/$uuid_regex", ConfirmInvitationAction::class)
            ->setName('users.invitations.confirm')
        ;
    }
}
