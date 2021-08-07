<?php
/**
 * @package Applications
 * @subpackage Pages
 */
declare(strict_types=1);

namespace Kwai\Applications;

use Kwai\Core\Infrastructure\Presentation\Router;
use Kwai\Modules\Pages\Presentation\REST\BrowsePagesAction;
use Kwai\Modules\Pages\Presentation\REST\CreatePageAction;
use Kwai\Modules\Pages\Presentation\REST\GetPageAction;
use Kwai\Modules\Pages\Presentation\REST\UpdatePageAction;

/**
 * Class PageApplication
 *
 * Application for browsing/reading pages.
 */
class PagesApplication extends Application
{
    /**
     * @inheritDoc
     */
    public function createRouter(): Router
    {
        return Router::create()
            ->get(
                'pages.browse',
                '/pages',
                BrowsePagesAction::class
            )
            ->get(
                'pages.get',
                '/pages/{id}',
                GetPageAction::class,
                requirements: [
                    'id' => '\d+'
                ]
            )
            ->post(
                'pages.create',
                '/pages',
                CreatePageAction::class,
                [
                    'auth' => true
                ]
            )
            ->patch(
                'pages.update',
                '/pages/{id}',
                UpdatePageAction::class,
                [
                    'auth' => true
                ],
                [
                    'id' => '\d+'
                ]
            )
        ;
    }
}
