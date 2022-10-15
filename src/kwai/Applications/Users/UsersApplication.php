<?php
/**
 * @package Applications
 * @subpackage Users
 */
declare(strict_types=1);

namespace Kwai\Applications\Users;

use Kwai\Applications\Application;
use Kwai\Applications\Users\Actions\BrowsePermissionsAction;
use Kwai\Applications\Users\Actions\BrowseUserAccountsAction;
use Kwai\Applications\Users\Actions\BrowseUserInvitationsAction;
use Kwai\Applications\Users\Actions\BrowseUsersAction;
use Kwai\Applications\Users\Actions\ConfirmInvitationAction;
use Kwai\Applications\Users\Actions\CreateUserInvitationAction;
use Kwai\Applications\Users\Actions\CreateUserRecoveryAction;
use Kwai\Applications\Users\Actions\DeleteUserInvitationAction;
use Kwai\Applications\Users\Actions\GetUserAction;
use Kwai\Applications\Users\Actions\GetUserInvitationAction;
use Kwai\Applications\Users\Actions\UpdateUserAction;
use Kwai\Applications\Users\Actions\UpdateUserInvitationAction;

/**
 * Class UsersApplication
 */
class UsersApplication extends Application
{
    public function getActions(): array {
        return [
            BrowsePermissionsAction::class,
            BrowseUserAccountsAction::class,
            BrowseUserInvitationsAction::class,
            BrowseUsersAction::class,
            ConfirmInvitationAction::class,
            CreateUserInvitationAction::class,
            CreateUserRecoveryAction::class,
            DeleteUserInvitationAction::class,
            GetUserAction::class,
            GetUserInvitationAction::class,
            UpdateUserAction::class,
            UpdateUserInvitationAction::class
        ];
    }
}
