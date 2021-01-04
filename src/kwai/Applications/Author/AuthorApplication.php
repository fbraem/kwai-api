<?php
/**
 * @package Applications
 * @subpackage Author
 */
declare(strict_types=1);

namespace Kwai\Applications\Author;

use Kwai\Applications\Author\Actions\BrowsePagesAction;
use Kwai\Applications\Author\Actions\BrowseStoriesAction;
use Kwai\Applications\Author\Actions\CreatePageAction;
use Kwai\Applications\Author\Actions\CreateStoryAction;
use Kwai\Applications\Author\Actions\DeleteStoryAction;
use Kwai\Applications\Author\Actions\GetPageAction;
use Kwai\Applications\Author\Actions\UpdatePageAction;
use Kwai\Applications\Author\Actions\UpdateStoryAction;
use Kwai\Applications\Application;
use Kwai\Applications\News\Actions\GetStoryAction;
use Kwai\Core\Infrastructure\Dependencies\ConvertDependency;
use Kwai\Core\Infrastructure\Dependencies\FileSystemDependency;
use Kwai\Core\Infrastructure\Presentation\Router;
use Psr\Container\ContainerInterface;

/**
 * Class AuthorApplication
 *
 * Application for editing news stories and information.
 */
class AuthorApplication extends Application
{
    /**
     * @inheritDoc
     */
    public function createRouter(): Router
    {
        return Router::create()
            ->get(
                'author.news.browse',
                '/author/stories',
                fn(ContainerInterface $container) => new BrowseStoriesAction($container),
                [
                    'auth' => true
                ]
            )
            ->post(
                'author.news.create',
                '/author/stories',
                fn(ContainerInterface $container) => new CreateStoryAction($container),
                [
                    'auth' => true
                ]
            )
            ->get(
                'author.news.read',
                '/author/stories/{id}',
                fn(ContainerInterface $container) => new GetStoryAction($container),
                [
                        'auth' => true
                    ],
                [
                        'id' => '\d+'
                    ]
            )
            ->patch(
                'author.news.update',
                '/author/stories/{id}',
                fn(ContainerInterface $container) => new UpdateStoryAction($container),
                [
                    'auth' => true
                ],
                [
                    'id' => '\d+'
                ]
            )
            ->delete(
                'author.news.delete',
                '/author/stories/{id}',
                fn(ContainerInterface $container) => new DeleteStoryAction($container),
                [
                    'auth' => true
                ],
                [
                    'id' => '\d+'
                ]
            )
            ->get(
                'author.pages.browse',
                '/author/pages',
                fn(ContainerInterface $container) => new BrowsePagesAction($container),
                [
                    'auth' => true
                ]
            )
            ->post(
                'author.pages.create',
                '/author/pages',
                fn(ContainerInterface $container) => new CreatePageAction($container),
                [
                    'auth' => true
                ]
            )
            ->get(
                'author.pages.read',
                '/author/pages/{id}',
                fn(ContainerInterface $container) => new GetPageAction($container),
                [
                    'auth' => true
                ],
                [
                    'id' => '\d+'
                ]
            )
            ->patch(
                'author.pages.update',
                '/author/pages/{id}',
                fn(ContainerInterface $container) => new UpdatePageAction($container),
                [
                    'auth' => true
                ],
                [
                    'id' => '\d+'
                ]
            )
        ;
    }

    public function addDependencies(): void
    {
        parent::addDependencies();

        $this->addDependency('filesystem', new FileSystemDependency());
        $this->addDependency('converter', new ConvertDependency());
    }
}
