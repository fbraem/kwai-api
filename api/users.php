<?php
declare(strict_types=1);

require '../src/vendor/autoload.php';

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
use function Kwai\Core\Infrastructure\createApplication;

$app = createApplication();

$app->group('/users', function (RouteCollectorProxy $group) {
    $uuid_regex = '{uuid:[0-9a-f]{8}-[0-9a-f]{4}-4[0-9a-f]{3}-[89ab][0-9a-f]{3}-[0-9a-f]{12}+}';

    // Get all users
    $group->get('', BrowseUsersAction::class)
        ->setName('users.browse')
        ->setArgument('auth', 'true')
    ;
    // Get a user with the given uuid
    $group->get("/$uuid_regex", GetUserAction::class)
        ->setName('users.get')
        ->setArgument('auth', 'true')
    ;
    // Get the abilities of the user with the given uuid
    $group->get("/$uuid_regex/abilities", GetUserAbilitiesAction::class)
        ->setName('user.abilities.browse')
        ->setArgument('auth', 'true')
    ;
    // Attach an ability to the user with the given uuid
    $group->patch("/$uuid_regex/abilities/{ability:[0-9]+}", AttachAbilityAction::class)
        ->setName('user.abilities.attach')
        ->setArgument('auth', 'true')
    ;
    // Detach an ability from the user with the given uuid
    $group->delete("/$uuid_regex/abilities/{ability:[0-9]+}", DetachAbilityAction::class)
        ->setName('user.abilities.detach')
        ->setArgument('auth', 'true')
    ;
    // Browse all abilities
    $group->get('/abilities', BrowseAbilitiesAction::class)
        ->setName('users.abilities.browse')
        ->setArgument('auth', 'true')
    ;
    // Create a new ability
    $group->post('/abilities', CreateAbilityAction::class)
        ->setName('users.abilities.create')
        ->setArgument('auth', 'true')
    ;
    // Get an ability with the given id
    $group->get('/abilities/{id:[0-9]+}', GetAbilityAction::class)
        ->setName('users.abilities.read')
        ->setArgument('auth', 'true')
    ;
    // Update an ability with the given id
    $group->patch('/abilities/{id:[0-9]+}', UpdateAbilityAction::class)
        ->setName('users.abilities.update')
        ->setArgument('auth', 'true')
    ;
    // Browse rules
    $group->get('/rules', BrowseRulesAction::class)
        ->setName('users.rules.browse')
        ->setArgument('auth', 'true')
    ;
    // Create User Invitation
    $group->post('/invitations', CreateUserInvitationAction::class)
        ->setName('users.invitations.create')
        ->setArgument('auth', 'true')
    ;
    // Browse all invitations
    $group->get('/invitations', BrowseUserInvitationsAction::class)
        ->setName('users.invitations.browse')
        ->setArgument('auth', 'true')
    ;
    // Get an invitation
    $group->get("/invitations/$uuid_regex", GetUserInvitationAction::class)
        ->setName('users.invitations.token')
    ;
    // Confirm an invitation
    $group->post("/invitations/$uuid_regex", ConfirmInvitationAction::class)
        ->setName('users.invitations.confirm')
    ;
});

$app->run();
