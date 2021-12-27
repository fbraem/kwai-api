<?php
/**
 * @package Applications
 * @subpackage Users
 */
declare(strict_types=1);

namespace Kwai\Applications;

use Kwai\Modules\Users\Presentation\REST\AttachAbilityAction;
use Kwai\Modules\Users\Presentation\REST\BrowseAbilitiesAction;
use Kwai\Modules\Users\Presentation\REST\BrowseRulesAction;
use Kwai\Modules\Users\Presentation\REST\BrowseUserAccountsAction;
use Kwai\Modules\Users\Presentation\REST\BrowseUserInvitationsAction;
use Kwai\Modules\Users\Presentation\REST\BrowseUsersAction;
use Kwai\Modules\Users\Presentation\REST\CreateAbilityAction;
use Kwai\Modules\Users\Presentation\REST\CreateUserInvitationAction;
use Kwai\Modules\Users\Presentation\REST\DetachAbilityAction;
use Kwai\Modules\Users\Presentation\REST\GetAbilityAction;
use Kwai\Modules\Users\Presentation\REST\GetUserAbilitiesAction;
use Kwai\Modules\Users\Presentation\REST\GetUserInvitationAction;
use Kwai\Modules\Users\Presentation\REST\UpdateAbilityAction;
use Kwai\Core\Infrastructure\Presentation\Router;

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
                'user.abilities.browse',
                '/users/{uuid}/abilities',
                GetUserAbilitiesAction::class,
                [
                    'auth' => true
                ],
                [
                    'uuid' => $uuid_regex
                ]
            )
            ->patch(
                'user.abilities.attach',
                '/users/{uuid}/abilities/{ability}',
                AttachAbilityAction::class,
                [
                    'auth' => true
                ],
                [
                    'uuid' => $uuid_regex,
                    'ability' => '\d+'
                ]
            )
            ->delete(
                'user.abilities.detach',
                '/users/{uuid}/abilities/{ability}',
                DetachAbilityAction::class,
                [
                    'auth' => true
                ],
                [
                    'uuid' => $uuid_regex,
                    'ability' => '\d+'
                ]
            )
            ->get(
                'users.abilities.browse',
                '/users/abilities',
                BrowseAbilitiesAction::class,
                [
                    'auth' => true
                ]
            )
            ->post(
                'users.abilities.create',
                '/users/abilities',
                CreateAbilityAction::class,
                [
                    'auth' => true
                ]
            )
            ->get(
                'user.abilities.read',
                '/users/abilities/{id}',
                GetAbilityAction::class,
                [
                    'auth' => true
                ],
                [
                    'id' => '\d+'
                ]
            )
            ->patch(
                'users.abilities.update',
                '/users/abilities/{id}',
                UpdateAbilityAction::class,
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
