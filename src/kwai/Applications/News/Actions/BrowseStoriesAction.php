<?php
/**
 * @package Kwai
 * @subpackage News
 */
declare(strict_types=1);

namespace Kwai\Applications\News\Actions;

use Kwai\Modules\News\Presentation\AbstractBrowseStoriesAction;
use Kwai\Modules\News\UseCases\BrowseStoriesCommand;
use Psr\Http\Message\ServerRequestInterface as Request;

/**
 * Class BrowseStoriesAction
 *
 * Action to browse all news stories
 */
class BrowseStoriesAction extends AbstractBrowseStoriesAction
{
    /**
     * Create the command for the use case Browse Stories.
     *
     * @param Request $request
     * @param array   $args
     * @return BrowseStoriesCommand
     */
    protected function createCommand(Request $request, array $args): BrowseStoriesCommand
    {
        $command = parent::createCommand($request, $args);
        if (isset($parameters['filter']['year'])) {
            $command->publishYear = (int) $parameters['filter']['year'];
        }
        if (isset($parameters['filter']['month'])) {
            $command->publishMonth = (int) $parameters['filter']['month'];
        }
        $command->promoted = isset($parameters['filter']['promoted']);
        if (isset($parameters['filter']['category'])) {
            $command->category = (int) $parameters['filter']['category'];
        }
        return $command;
    }
}
