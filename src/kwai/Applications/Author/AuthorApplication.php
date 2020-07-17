<?php
/**
 * @package Applications
 * @subpackage Author
 */
declare(strict_types=1);

namespace Kwai\Applications\Author;

use Kwai\Applications\Application;
use Kwai\Applications\Author\Actions\BrowsePagesAction;
use Kwai\Applications\Author\Actions\BrowseStoriesAction;
use Kwai\Applications\Author\Actions\CreateStoryAction;
use Kwai\Applications\Author\Actions\DeleteStoryAction;
use Kwai\Applications\Author\Actions\UpdateStoryAction;
use Kwai\Applications\News\Actions\GetStoryAction;
use Kwai\Core\Infrastructure\Dependencies\ConvertDependency;
use Kwai\Core\Infrastructure\Dependencies\FileSystemDependency;
use Slim\Routing\RouteCollectorProxy;

/**
 * Class AuthorApplication
 *
 * Application for editing news stories and information.
 */
class AuthorApplication extends Application
{
    public function __construct()
    {
        parent::__construct('author');
    }

    /**
     * @inheritDoc
     */
    public function createRoutes(RouteCollectorProxy $group): void
    {
        $group->get('/stories', BrowseStoriesAction::class)
            ->setName('author.news.browse')
            ->setArgument('auth', 'true')
        ;
        $group->get('/stories/{id:[0-9]+}', GetStoryAction::class)
            ->setName('author.news.read')
            ->setArgument('auth', 'true')
        ;
        $group->post('/stories', CreateStoryAction::class)
            ->setName('author.news.create')
            ->setArgument('auth', 'true')
        ;
        $group->patch('/stories/{id:[0-9]+}', UpdateStoryAction::class)
            ->setName('author.news.update')
            ->setArgument('auth', 'true')
        ;
        $group->delete('/stories/{id:[0-9]+}', DeleteStoryAction::class)
            ->setName('author.news.delete')
            ->setArgument('auth', 'true')
        ;
        $group->get('/pages', BrowsePagesAction::class)
            ->setName('author.pages.browse')
            ->setArgument('auth', 'true')
        ;
    }

    public function addDependencies(): void
    {
        parent::addDependencies();

        $this->addDependency('filesystem', new FileSystemDependency());
        $this->addDependency('converter', new ConvertDependency());
    }
}
