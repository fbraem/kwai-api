<?php
/**
 * @package Applications
 * @subpackage Users
 */
declare(strict_types=1);

namespace Kwai\Applications;

use Kwai\Core\Infrastructure\Dependencies\FileSystemDependency;
use Kwai\Core\Infrastructure\Dependencies\MailerDependency;
use Kwai\Modules\Users\Presentation\REST\AttachAbilityAction;
use Kwai\Modules\Users\Presentation\REST\BrowseAbilitiesAction;
use Kwai\Modules\Users\Presentation\REST\BrowseRulesAction;
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
use Psr\Container\ContainerInterface;

/**
 * Class UsersApplication
 */
class UsersApplication extends Application
{
    public function addDependencies(): void
    {
        parent::addDependencies();

        $this->addDependency('filesystem', new FileSystemDependency());
        $this->addDependency('mailer', new MailerDependency());
    }

    public function createRouter(): Router
    {
        $uuid_regex = Application::UUID_REGEX;

        return Router::create()
            ->get(
                'users.browse',
                '/users',
                fn (ContainerInterface $container) => new BrowseUsersAction($container),
                [
                    'auth' => true
                ]
            )
            ->get(
                'user.abilities.browse',
                '/users/{uuid}/abilities',
                fn (ContainerInterface $container) => new GetUserAbilitiesAction($container),
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
                fn (ContainerInterface $container) => new AttachAbilityAction($container),
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
                fn (ContainerInterface $container) => new DetachAbilityAction($container),
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
                fn (ContainerInterface $container) => new BrowseAbilitiesAction($container),
                [
                    'auth' => true
                ]
            )
            ->post(
                'users.abilities.create',
                '/users/abilities',
                fn (ContainerInterface $container) => new CreateAbilityAction($container),
                [
                    'auth' => true
                ]
            )
            ->get(
                'user.abilities.read',
                '/users/abilities/{id}',
                fn (ContainerInterface $container) => new GetAbilityAction($container),
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
                fn (ContainerInterface $container) => new UpdateAbilityAction($container),
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
                fn (ContainerInterface $container) => new BrowseRulesAction($container),
                [
                    'auth' => true
                ]
            )
            ->get(
                'users.invitations.browse',
                '/users/invitations',
                fn (ContainerInterface $container) => new BrowseUserInvitationsAction($container),
                [
                    'auth' => true
                ]
            )
            ->post(
                'users.invitations.create',
                '/users/invitations',
                fn (ContainerInterface $container) => new CreateUserInvitationAction($container),
                [
                    'auth' => true
                ]
            )
            ->get(
                'users.invitations.token',
                '/users/invitations/{uuid}',
                fn (ContainerInterface $container) => new GetUserInvitationAction($container),
                [
                    'auth' => true
                ],
                [
                    'uuid' => $uuid_regex
                ]
            )
        ;
    }
}
