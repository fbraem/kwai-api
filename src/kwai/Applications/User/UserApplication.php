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
use Kwai\Core\Infrastructure\Presentation\PreflightAction;
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

        $group->group(
            '/login',
            function (RouteCollectorProxy $loginGroup) {
                $loginGroup
                    ->options('', PreflightAction::class)
                    ;
                $loginGroup
                    ->post('', LoginAction::class)
                    ->setName('user.login')
                ;
            }
        );
        $group->group(
            '/logout',
            function (RouteCollectorProxy $logoutGroup) {
                $logoutGroup
                    ->options('', PreflightAction::class)
                ;
                $logoutGroup
                    ->post('', LogoutAction::class)
                    ->setName('user.logout')
                    ->setArgument('auth', 'true')
                ;
            }
        );

        $group->group(
            '/access_token',
            function (RouteCollectorProxy $accessTokenGroup) {
                $accessTokenGroup
                    ->options('', PreflightAction::class)
                ;
                $accessTokenGroup
                    ->post('', RefreshTokenAction::class)
                    ->setName('user.access_token')
                ;
            }
        );

        $group->group(
            '',
            function (RouteCollectorProxy $userGroup) {
                $userGroup
                    ->options('', PreflightAction::class)
                ;
                $userGroup
                    ->get('', GetUserAction::class)
                    ->setName('user.get')
                    ->setArgument('auth', 'true')
                ;
            }
        );

        $group->group(
            "/invitations/$uuid_regex",
            function (RouteCollectorProxy $invitationGroup) {
                $invitationGroup
                    ->options('', PreflightAction::class)
                ;
                $invitationGroup
                    ->post('', ConfirmInvitationAction::class)
                    ->setName('users.invitations.confirm')
                ;
            }
        );
    }
}
