<?php
/**
 * @package Applications
 * @subpackage Users
 */
declare(strict_types=1);

namespace Kwai\Applications;

use Kwai\Modules\Users\Presentation\REST\AttachRoleAction;
use Kwai\Modules\Users\Presentation\REST\BrowseRolesAction;
use Kwai\Modules\Users\Presentation\REST\BrowseRulesAction;
use Kwai\Modules\Users\Presentation\REST\BrowseUserAccountsAction;
use Kwai\Modules\Users\Presentation\REST\BrowseUserInvitationsAction;
use Kwai\Modules\Users\Presentation\REST\BrowseUsersAction;
use Kwai\Modules\Users\Presentation\REST\CreateRoleAction;
use Kwai\Modules\Users\Presentation\REST\CreateUserInvitationAction;
use Kwai\Modules\Users\Presentation\REST\DetachRoleAction;
use Kwai\Modules\Users\Presentation\REST\GetRoleAction;
use Kwai\Modules\Users\Presentation\REST\GetUserRolesAction;
use Kwai\Modules\Users\Presentation\REST\GetUserAction;
use Kwai\Modules\Users\Presentation\REST\GetUserInvitationAction;
use Kwai\Modules\Users\Presentation\REST\UpdateRoleAction;
use Kwai\Core\Infrastructure\Presentation\Router;
use Kwai\Modules\Users\Presentation\REST\UpdateUserAction;

/**
 * Class UsersApplication
 */
class UsersApplication extends Application
{
    public function createRouter(): Router
    {
        $uuid_regex = Application::UUID_REGEX;

        return Router::create()
            ->get(
                'users.browse',
                '/users',
                BrowseUsersAction::class,
                [
                    'auth' => true
                ]
            )
            ->get(
                'users.get',
                '/users/{uuid}',
                GetUserAction::class,
                [
                    'auth' => true
                ],
                [
                    'uuid' => $uuid_regex
                ]
            )
            ->patch(
                'users.update',
                '/users/{uuid}',
                UpdateUserAction::class,
                [
                    'auth' => true
                ],
                [
                    'uuid' => $uuid_regex
                ]
            )
            ->get(
                'user.roles.browse',
                '/users/{uuid}/roles',
                GetUserRolesAction::class,
                [
                    'auth' => true
                ],
                [
                    'uuid' => $uuid_regex
                ]
            )
            ->patch(
                'user.roles.attach',
                '/users/{uuid}/roles/{role}',
                AttachRoleAction::class,
                [
                    'auth' => true
                ],
                [
                    'uuid' => $uuid_regex,
                    'role' => '\d+'
                ]
            )
            ->delete(
                'user.roles.detach',
                '/users/{uuid}/roles/{role}',
                DetachRoleAction::class,
                [
                    'auth' => true
                ],
                [
                    'uuid' => $uuid_regex,
                    'role' => '\d+'
                ]
            )
            ->get(
                'users.roles.browse',
                '/users/roles',
                BrowseRolesAction::class,
                [
                    'auth' => true
                ]
            )
            ->post(
                'users.roles.create',
                '/users/roles',
                CreateRoleAction::class,
                [
                    'auth' => true
                ]
            )
            ->get(
                'user.roles.read',
                '/users/roles/{id}',
                GetRoleAction::class,
                [
                    'auth' => true
                ],
                [
                    'id' => '\d+'
                ]
            )
            ->patch(
                'users.roles.update',
                '/users/roles/{id}',
                UpdateRoleAction::class,
                [
                    'auth' => true
                ],
                [
                    'id' => '\d+'
                ]
            )
            ->get(
                'users.rules.browse',
                '/users/rules',
                BrowseRulesAction::class,
                [
                    'auth' => true
                ]
            )
            ->get(
                'users.invitations.browse',
                '/users/invitations',
                BrowseUserInvitationsAction::class,
                [
                    'auth' => true
                ]
            )
            ->post(
                'users.invitations.create',
                '/users/invitations',
                CreateUserInvitationAction::class,
                [
                    'auth' => true
                ]
            )
            ->get(
                'users.invitations.token',
                '/users/invitations/{uuid}',
                GetUserInvitationAction::class,
                [
                    'auth' => true
                ],
                [
                    'uuid' => $uuid_regex
                ]
            )
            ->get(
                'user_accounts',
                '/users/accounts',
                BrowseUserAccountsAction::class,
                [
                    'auth' => true
                ]
            )
        ;
    }
}
