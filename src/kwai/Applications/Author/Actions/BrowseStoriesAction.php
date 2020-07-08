<?php
/**
 * @package Applications
 * @subpackage Author
 */
declare(strict_types=1);

namespace Kwai\Applications\Author\Actions;

use Kwai\Modules\News\Presentation\AbstractBrowseStoriesAction;
use Kwai\Modules\News\UseCases\BrowseStoriesCommand;
use Psr\Http\Message\ServerRequestInterface as Request;

/**
 * Class BrowseStoriesAction
 *
 * Browse news stories for an author.
 */
class BrowseStoriesAction extends AbstractBrowseStoriesAction
{
    /**
     * Create the command for the browse stories use case.
     * In this version disabled stories are also retrieved.
     *
     * @param Request $request
     * @param array   $args
     * @return BrowseStoriesCommand
     */
    protected function createCommand(Request $request, array $args): BrowseStoriesCommand
    {
        $command = parent::createCommand($request, $args);
        $parameters = $request->getAttribute('parameters');
        $command->enabled = (bool) ($parameters['filter']['enabled'] ?? false);
        return $command;
    }
}
