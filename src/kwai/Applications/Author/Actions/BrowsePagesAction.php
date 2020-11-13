<?php
/**
 * @package Applications
 * @subpackage Author
 */
declare(strict_types=1);

namespace Kwai\Applications\Author\Actions;

use Kwai\Modules\Pages\Presentation\AbstractBrowsePagesAction;
use Kwai\Modules\Pages\UseCases\BrowsePagesCommand;
use Psr\Http\Message\ServerRequestInterface as Request;

/**
 * Class BrowseStoriesAction
 *
 * Browse pages for an author.
 */
class BrowsePagesAction extends AbstractBrowsePagesAction
{
    /**
     * Create the command for the browse pages use case.
     * In this version disabled pages are also retrieved.
     *
     * @param Request $request
     * @param array   $args
     * @return BrowsePagesCommand
     */
    protected function createCommand(Request $request, array $args): BrowsePagesCommand
    {
        $command = parent::createCommand($request, $args);
        $parameters = $request->getAttribute('parameters');
        $command->enabled = (bool) ($parameters['filter']['enabled'] ?? false);
        return $command;
    }
}
