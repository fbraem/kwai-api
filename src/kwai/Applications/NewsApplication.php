<?php
/**
 * @package Applications
 * @subpackage News
 */
declare(strict_types=1);

namespace Kwai\Applications;

use Kwai\Modules\News\Presentation\REST\BrowseStoriesAction;
use Kwai\Modules\News\Presentation\REST\CreateStoryAction;
use Kwai\Modules\News\Presentation\REST\GetArchiveAction;
use Kwai\Modules\News\Presentation\REST\GetStoryAction;
use Kwai\Core\Infrastructure\Presentation\Router;
use Kwai\Modules\News\Presentation\REST\UpdateStoryAction;

/**
 * Class NewsApplication
 */
class NewsApplication extends Application
{
    /**
     * @inheritDoc
     */
    public function createRouter(): Router
    {
        return Router::create()
            ->get(
                'news.browse',
                '/news/stories',
                BrowseStoriesAction::class
            )
            ->get(
                'news.get',
                '/news/stories/{id}',
                GetStoryAction::class,
                requirements: [
                    'id' => '\d+'
                ]
            )
            ->patch(
                'news.update',
                '/news/stories/{id}',
                UpdateStoryAction::class,
                [
                    'auth' => true
                ],
                [
                    'id' => '\d+'
                ]
            )
            ->post(
                'news.create',
                '/news/stories',
                CreateStoryAction::class,
                [
                    'auth' => true
                ]
            )
            ->get(
                'news.archive',
                '/news/archive',
                GetArchiveAction::class
            )
        ;
    }
}
