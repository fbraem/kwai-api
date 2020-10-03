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
use Kwai\Applications\Author\Actions\CreatePageAction;
use Kwai\Applications\Author\Actions\CreateStoryAction;
use Kwai\Applications\Author\Actions\DeleteStoryAction;
use Kwai\Applications\Author\Actions\GetPageAction;
use Kwai\Applications\Author\Actions\UpdatePageAction;
use Kwai\Applications\Author\Actions\UpdateStoryAction;
use Kwai\Applications\News\Actions\GetStoryAction;
use Kwai\Core\Infrastructure\Dependencies\ConvertDependency;
use Kwai\Core\Infrastructure\Dependencies\FileSystemDependency;
use Kwai\Core\Infrastructure\Presentation\PreflightAction;
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
        $group->group(
            '/stories',
            function (RouteCollectorProxy $storiesGroup) {
                $storiesGroup
                    ->options('', PreflightAction::class)
                ;
                $storiesGroup
                    ->get('', BrowseStoriesAction::class)
                    ->setName('author.news.browse')
                    ->setArgument('auth', 'true')
                ;
                $storiesGroup
                    ->post('', CreateStoryAction::class)
                    ->setName('author.news.create')
                    ->setArgument('auth', 'true')
                ;
            }
        );

        $group->group(
            '/stories/{id:[0-9]+}',
            function (RouteCollectorProxy $storyGroup) {
                $storyGroup
                    ->options('', PreflightAction::class)
                ;
                $storyGroup
                    ->get('', GetStoryAction::class)
                    ->setName('author.news.read')
                    ->setArgument('auth', 'true')
                ;
                $storyGroup
                    ->patch('', UpdateStoryAction::class)
                    ->setName('author.news.update')
                    ->setArgument('auth', 'true')
                ;
                $storyGroup
                    ->delete('', DeleteStoryAction::class)
                    ->setName('author.news.delete')
                    ->setArgument('auth', 'true')
                ;
            }
        );

        $group->group(
            '/pages',
            function (RouteCollectorProxy $pagesGroup) {
                $pagesGroup
                    ->options('', PreflightAction::class)
                ;
                $pagesGroup
                    ->get('', BrowsePagesAction::class)
                    ->setName('author.pages.browse')
                    ->setArgument('auth', 'true')
                ;
                $pagesGroup
                    ->post('', CreatePageAction::class)
                    ->setName('author.pages.create')
                    ->setArgument('auth', 'true')
                ;
            }
        );

        $group->group(
            '/pages/{id:[0-9]+}',
            function (RouteCollectorProxy $pageGroup) {
                $pageGroup
                    ->options('', PreflightAction::class)
                ;
                $pageGroup
                    ->get('', GetPageAction::class)
                    ->setName('author.pages.read')
                    ->setArgument('auth', 'true')
                ;
                $pageGroup
                    ->patch('', UpdatePageAction::class)
                    ->setName('author.pages.update')
                    ->setArgument('auth', 'true')
                ;
            }
        );
    }

    public function addDependencies(): void
    {
        parent::addDependencies();

        $this->addDependency('filesystem', new FileSystemDependency());
        $this->addDependency('converter', new ConvertDependency());
    }
}
