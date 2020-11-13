<?php
/**
 * @package
 * @subpackage
 */
declare(strict_types=1);

namespace Kwai\Applications\Admin\Actions;

use Kwai\Applications\Club\ClubApplication;
use Kwai\Modules\Pages\Presentation\AbstractBrowsePagesAction;
use Kwai\Modules\Pages\UseCases\BrowsePagesCommand;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

/**
 * Class BrowsePagesAction
 *
 * Browse pages owned by a user
 */
class BrowsePagesAction extends AbstractBrowsePagesAction
{
    /**
     * Create the command for the browse pages use case.
     * Only the pages of the user will be returned.
     *
     * @param Request $request
     * @param array   $args
     * @return BrowsePagesCommand
     */
    protected function createCommand(Request $request, array $args): BrowsePagesCommand
    {
        $command = parent::createCommand($request, $args);
        $command->userUid = $args['uuid'];
        return $command;
    }
}
