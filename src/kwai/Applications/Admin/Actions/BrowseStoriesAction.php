<?php
/**
 * @package Applications
 * @subpackage Club
 */
declare(strict_types=1);

namespace Kwai\Applications\Admin\Actions;

use Kwai\Modules\News\Presentation\AbstractBrowseStoriesAction;
use Kwai\Modules\News\UseCases\BrowseStoriesCommand;
use Psr\Http\Message\ServerRequestInterface as Request;

/**
 * Class BrowseStoriesAction
 *
 * Browse news stories of the club application.
 */
class BrowseStoriesAction extends AbstractBrowseStoriesAction
{
    /**
     * Create the command for the browse stories use case.
     * Only the stories of the club application will be retrieved.
     *
     * @param Request $request
     * @param array   $args
     * @return BrowseStoriesCommand
     */
    protected function createCommand(Request $request, array $args): BrowseStoriesCommand
    {
        $command = parent::createCommand($request, $args);
        $command->userUid = $args['uuid'];
        return $command;
    }
}
