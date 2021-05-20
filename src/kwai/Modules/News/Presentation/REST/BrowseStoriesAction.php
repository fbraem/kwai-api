<?php
/**
 * @package Kwai
 * @subpackage News
 */
declare(strict_types=1);

namespace Kwai\Modules\News\Presentation\REST;

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
        $parameters = $request->getAttribute('parameters');

        $command = parent::createCommand($request, $args);
        if (isset($parameters['filter']['year'])) {
            $command->publishYear = (int) $parameters['filter']['year'];
        }
        if (isset($parameters['filter']['month'])) {
            $command->publishMonth = (int) $parameters['filter']['month'];
        }
        $command->promoted = isset($parameters['filter']['promoted']);
        if (isset($parameters['filter']['application'])) {
            $command->application = (int) $parameters['filter']['application'];
        }
        if (isset($parameters['filter']['enabled'])) {
            // TODO: Not allowed for public view
            $command->enabled = $parameters['filter']['enabled'] === 'true';
        }
        return $command;
    }
}
