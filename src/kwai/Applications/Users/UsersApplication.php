<?php
/**
 * @package Applications
 * @subpackage Users
 */
declare(strict_types=1);

namespace Kwai\Applications\Users;

use Kwai\Applications\Application;
use Kwai\Applications\Auth\Actions\LoginAction;
use Kwai\Applications\Auth\Actions\LogoutAction;
use Kwai\Applications\Auth\Actions\RefreshTokenAction;
use Kwai\Applications\Users\Actions\AttachRoleAction;
use Kwai\Applications\Users\Actions\BrowseRolesAction;
use Kwai\Applications\Users\Actions\BrowseRulesAction;
use Kwai\Applications\Users\Actions\BrowseUserAccountsAction;
use Kwai\Applications\Users\Actions\BrowseUserInvitationsAction;
use Kwai\Applications\Users\Actions\BrowseUsersAction;
use Kwai\Applications\Users\Actions\ConfirmInvitationAction;
use Kwai\Applications\Users\Actions\CreateRoleAction;
use Kwai\Applications\Users\Actions\CreateUserInvitationAction;
use Kwai\Applications\Users\Actions\DetachRoleAction;
use Kwai\Applications\Users\Actions\GetRoleAction;
use Kwai\Applications\Users\Actions\GetUserAction;
use Kwai\Applications\Users\Actions\GetUserInvitationAction;
use Kwai\Applications\Users\Actions\GetUserRolesAction;
use Kwai\Applications\Users\Actions\UpdateUserAction;

/**
 * Class UsersApplication
 */
class UsersApplication extends Application
{
    public function getActions(): array {
        return [
            AttachRoleAction::class,
            BrowseRolesAction::class,
            BrowseRulesAction::class,
            BrowseUserAccountsAction::class,
            BrowseUserInvitationsAction::class,
            BrowseUsersAction::class,
            ConfirmInvitationAction::class,
            CreateRoleAction::class,
            CreateUserInvitationAction::class,
            DetachRoleAction::class,
            GetRoleAction::class,
            GetUserAction::class,
            GetUserInvitationAction::class,
            GetUserRolesAction::class,
            LoginAction::class,
            LogoutAction::class,
            RefreshTokenAction::class,
            UpdateUserAction::class
        ];
    }
}
