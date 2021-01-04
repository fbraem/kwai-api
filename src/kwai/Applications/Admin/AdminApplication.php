<?php
/**
 * @package Applications
 * @subpackage Admin
 */
declare(strict_types=1);

namespace Kwai\Applications\Admin;

use Kwai\Applications\Admin\Actions\BrowsePagesAction;
use Kwai\Applications\Admin\Actions\BrowseStoriesAction;
use Kwai\Applications\Application;
use Kwai\Core\Infrastructure\Dependencies\ConvertDependency;
use Kwai\Core\Infrastructure\Dependencies\FileSystemDependency;
use Kwai\Core\Infrastructure\Dependencies\MailerDependency;
use Kwai\Core\Infrastructure\Dependencies\TemplateDependency;
use Kwai\Applications\Admin\Actions\AttachAbilityAction;
use Kwai\Applications\Admin\Actions\BrowseAbilitiesAction;
use Kwai\Applications\Admin\Actions\BrowseRulesAction;
use Kwai\Applications\Admin\Actions\BrowseUserInvitationsAction;
use Kwai\Applications\Admin\Actions\BrowseUsersAction;
// use Kwai\Applications\Admin\Actions\ConfirmInvitationAction;
use Kwai\Applications\Admin\Actions\CreateAbilityAction;
use Kwai\Applications\Admin\Actions\CreateUserInvitationAction;
use Kwai\Applications\Admin\Actions\DetachAbilityAction;
use Kwai\Applications\Admin\Actions\GetAbilityAction;
use Kwai\Applications\Admin\Actions\GetUserAbilitiesAction;
use Kwai\Applications\Admin\Actions\GetUserAction;
use Kwai\Applications\Admin\Actions\GetUserInvitationAction;
use Kwai\Applications\Admin\Actions\UpdateAbilityAction;
use Kwai\Core\Infrastructure\Presentation\Router;
use Psr\Container\ContainerInterface;

/**
 * Class AdminApplication
 */
class AdminApplication extends Application
{
    public function addDependencies(): void
    {
        parent::addDependencies();

        $this->addDependency('filesystem', new FileSystemDependency());
        $this->addDependency('converter', new ConvertDependency());
        $this->addDependency('template', new TemplateDependency());
        $this->addDependency('mailer', new MailerDependency());
    }

    public function createRouter(): Router
    {
        $uuid_regex = Application::UUID_REGEX;

        return Router::create()
            ->get(
                'users.browse',
                '/admin',
                fn(ContainerInterface $container) => new BrowseUsersAction($container),
                [
                    'auth' => true
                ]
            )
            ->get(
                'users.get',
                '/admin/{uuid}',
                fn(ContainerInterface $container) => new GetUserAction($container),
                [
                    'auth' => true
                ],
                [
                    'uuid' => $uuid_regex
                ]
            )
            ->get(
                'user.abilities.browse',
                '/admin/{uuid}/abilities',
                fn(ContainerInterface $container) => new GetUserAbilitiesAction($container),
                [
                    'auth' => true
                ],
                [
                    'uuid' => $uuid_regex
                ]
            )
            ->patch(
                'user.abilities.attach',
                '/admin/{uuid}/abilities/{ability}',
                fn(ContainerInterface $container) => new AttachAbilityAction($container),
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
                '/admin/{uuid}/abilities/{ability}',
                fn(ContainerInterface $container) => new DetachAbilityAction($container),
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
                '/admin/abilities',
                fn(ContainerInterface $container) => new BrowseAbilitiesAction($container),
                [
                    'auth' => true
                ]
            )
            ->post(
                'users.abilities.create',
                '/admin/abilities',
                fn(ContainerInterface $container) => new CreateAbilityAction($container),
                [
                    'auth' => true
                ]
            )
            ->get(
                'user.abilities.read',
                '/admin/abilities/{id}',
                fn(ContainerInterface $container) => new GetAbilityAction($container),
                [
                    'auth' => true
                ],
                [
                    'id' => '\d+'
                ]
            )
            ->patch(
                'users.abilities.update',
                '/admin/abilities/{id}',
                fn(ContainerInterface $container) => new UpdateAbilityAction($container),
                [
                    'auth' => true
                ],
                [
                    'id' => '\d+'
                ]
            )
            ->get(
                'users.rules.browse',
                '/admin/rules',
                fn(ContainerInterface $container) => new BrowseRulesAction($container),
                [
                    'auth' => true
                ]
            )
            ->get(
                'users.invitations.browse',
                '/admin/invitations',
                fn(ContainerInterface $container) => new BrowseUserInvitationsAction($container),
                [
                    'auth' => true
                ]
            )
            ->post(
                'users.invitations.create',
                '/admin/invitations',
                fn(ContainerInterface $container) => new CreateUserInvitationAction($container),
                [
                    'auth' => true
                ]
            )
            ->get(
                'users.invitations.token',
                '/admin/invitations/{uuid}',
                fn(ContainerInterface $container) => new GetUserInvitationAction($container),
                [
                    'auth' => true
                ],
                [
                    'uuid' => $uuid_regex
                ]
            )
            ->get(
                'user.news.browse',
                '/admin/{uuid}/news',
                fn(ContainerInterface $container) => new BrowseStoriesAction($container),
                [
                    'auth' => true
                ],
                [
                    'uuid' => $uuid_regex
                ]
            )
            ->get(
                'user.pages.browse',
                '/admin/{uuid}/pages',
                fn(ContainerInterface $container) => new BrowsePagesAction($container),
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
