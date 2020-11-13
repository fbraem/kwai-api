<?php
/**
 * @package Applications
 * @subpackage Page
 */
declare(strict_types=1);

namespace Kwai\Applications\Pages\Actions;

use Kwai\Modules\Pages\Presentation\AbstractBrowsePagesAction;
use Kwai\Modules\Pages\UseCases\BrowsePagesCommand;
use Psr\Http\Message\ServerRequestInterface as Request;

/**
 * Class BrowseStoriesAction
 *
 * Browse pages.
 */
class BrowsePagesAction extends AbstractBrowsePagesAction
{
    /**
     * Create the command for the browse pages use case.
     *
     * @param Request $request
     * @param array   $args
     * @return BrowsePagesCommand
     */
    protected function createCommand(Request $request, array $args): BrowsePagesCommand
    {
        $command = parent::createCommand($request, $args);
        $command->enabled = true;
        return $command;
    }
}
