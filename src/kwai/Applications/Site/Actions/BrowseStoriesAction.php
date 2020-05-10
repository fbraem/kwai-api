<?php
/**
 * @package Applications
 * @subpackage News
 */
declare(strict_types=1);

namespace Kwai\Applications\Site\Actions;

use Kwai\Modules\News\Presentation\AbstractBrowseStoriesAction;
use Kwai\Modules\News\UseCases\BrowseStoriesCommand;
use Psr\Http\Message\ServerRequestInterface as Request;

/**
 * Class BrowseStoriesAction
 *
 * Browse news stories. The site application will only get the promoted news stories.
 */
class BrowseStoriesAction extends AbstractBrowseStoriesAction
{
    protected function createCommand(Request $request, array $args): BrowseStoriesCommand
    {
        $command = new BrowseStoriesCommand();
        $parameters = $request->getAttribute('parameters');
        $command->limit = $parameters['page']['limit'] ?? 10;
        $command->offset = $parameters['page']['offset'] ?? 0;

        // Only get the promoted stories
        $command->promoted = true;

        return $command;
    }
}
