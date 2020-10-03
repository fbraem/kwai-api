<?php
/**
 * @package Applications
 * @subpackage Admin
 */
declare(strict_types=1);

namespace Kwai\Applications\Admin;

use Kwai\Applications\Application;
use Kwai\Core\Infrastructure\Presentation\PreflightAction;
use Kwai\Modules\Users\Presentation\Rest\AttachAbilityAction;
use Kwai\Modules\Users\Presentation\Rest\BrowseAbilitiesAction;
use Kwai\Modules\Users\Presentation\Rest\BrowseRulesAction;
use Kwai\Modules\Users\Presentation\Rest\BrowseUserInvitationsAction;
use Kwai\Modules\Users\Presentation\Rest\BrowseUsersAction;
use Kwai\Modules\Users\Presentation\Rest\ConfirmInvitationAction;
use Kwai\Modules\Users\Presentation\Rest\CreateAbilityAction;
use Kwai\Modules\Users\Presentation\Rest\CreateUserInvitationAction;
use Kwai\Modules\Users\Presentation\Rest\DetachAbilityAction;
use Kwai\Modules\Users\Presentation\Rest\GetAbilityAction;
use Kwai\Modules\Users\Presentation\Rest\GetUserAbilitiesAction;
use Kwai\Modules\Users\Presentation\Rest\GetUserAction;
use Kwai\Modules\Users\Presentation\Rest\GetUserInvitationAction;
use Kwai\Modules\Users\Presentation\Rest\UpdateAbilityAction;
use Slim\Routing\RouteCollectorProxy;

/**
 * Class AdminApplication
 */
class AdminApplication extends Application
{
    /**
     * AdminApplication constructor.
     */
    public function __construct()
    {
        parent::__construct('admin');
    }

    public function createRoutes(RouteCollectorProxy $group): void
    {
        $uuid_regex = Application::UUID_REGEX;

        // Get all users
        $group->group(
            '',
            function (RouteCollectorProxy $usersGroup) {
                $usersGroup
                    ->options('', PreflightAction::class)
                ;
                $usersGroup
                    ->get('', BrowseUsersAction::class)
                    ->setName('users.browse')
                    ->setArgument('auth', 'true')
                ;
            }
        );

        // Get a user with the given uuid
        $group->group(
            "/$uuid_regex",
            function (RouteCollectorProxy $userGroup) {
                $userGroup
                    ->options('', PreflightAction::class)
                ;
                $userGroup
                    ->get('', GetUserAction::class)
                    ->setName('users.get')
                    ->setArgument('auth', 'true')
                ;
            }
        );

        // Get the abilities of the user with the given uuid
        $group->group(
            "/$uuid_regex/abilities",
            function (RouteCollectorProxy $userAbilitiesGroup) {
                $userAbilitiesGroup
                    ->options('', PreflightAction::class)
                ;
                $userAbilitiesGroup
                    ->get('', GetUserAbilitiesAction::class)
                    ->setName('user.abilities.browse')
                    ->setArgument('auth', 'true')
                ;
            }
        );

        $group->group(
            "/$uuid_regex/abilities/{ability:[0-9]+}",
            function (RouteCollectorProxy $userAbilityGroup) {
                $userAbilityGroup
                    ->options('', PreflightAction::class)
                ;
                // Attach an ability to the user with the given uuid
                $userAbilityGroup
                    ->patch('', AttachAbilityAction::class)
                    ->setName('user.abilities.attach')
                    ->setArgument('auth', 'true')
                ;
                // Detach an ability from the user with the given uuid
                $userAbilityGroup
                    ->delete('', DetachAbilityAction::class)
                    ->setName('user.abilities.detach')
                    ->setArgument('auth', 'true')
                ;
            }
        );

        // Browse all abilities
        $group->group(
            '/abilities',
            function (RouteCollectorProxy $abilitiesGroup) {
                $abilitiesGroup
                    ->options('', PreflightAction::class)
                ;
                $abilitiesGroup
                    ->get('', BrowseAbilitiesAction::class)
                    ->setName('users.abilities.browse')
                    ->setArgument('auth', 'true');
                // Create a new ability
                $abilitiesGroup
                    ->post('', CreateAbilityAction::class)
                    ->setName('users.abilities.create')
                    ->setArgument('auth', 'true');
            }
        );

        // Ability
        $group->group(
            '/abilities/{id:[0-9]+}',
            function (RouteCollectorProxy $abilityGroup) {
                $abilityGroup
                    ->options('', PreflightAction::class)
                ;
                // Get an ability with the given id
                $abilityGroup
                    ->get('', GetAbilityAction::class)
                    ->setName('users.abilities.read')
                    ->setArgument('auth', 'true')
                ;
                // Update an ability with the given id
                $abilityGroup
                    ->patch('', UpdateAbilityAction::class)
                    ->setName('users.abilities.update')
                    ->setArgument('auth', 'true')
                ;
            }
        );

        // Browse rules
        $group->group(
            '/rules',
            function (RouteCollectorProxy $rulesGroup) {
                $rulesGroup
                    ->options('', PreflightAction::class)
                ;
                $rulesGroup
                    ->get('', BrowseRulesAction::class)
                    ->setName('users.rules.browse')
                    ->setArgument('auth', 'true');
            }
        );

        $group->group(
            "/invitations",
            function (RouteCollectorProxy $invitationsGroup) {
                $invitationsGroup
                    ->options('', PreflightAction::class)
                ;
                // Create User Invitation
                $invitationsGroup
                    ->post('', CreateUserInvitationAction::class)
                    ->setName('users.invitations.create')
                    ->setArgument('auth', 'true')
                ;
                // Browse all invitations
                $invitationsGroup
                    ->get('', BrowseUserInvitationsAction::class)
                    ->setName('users.invitations.browse')
                    ->setArgument('auth', 'true')
                ;
            }
        );

        // Get an invitation
        $group->group(
            "/invitations/$uuid_regex",
            function (RouteCollectorProxy  $invitationGroup) {
                $invitationGroup
                    ->options('', PreflightAction::class)
                ;
                $invitationGroup
                    ->get('', GetUserInvitationAction::class)
                    ->setName('users.invitations.token')
                ;
                // Confirm an invitation
                $invitationGroup
                    ->post('', ConfirmInvitationAction::class)
                    ->setName('users.invitations.confirm')
                ;
            }
        );
    }
}
