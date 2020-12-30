<?php
/**
 * @package Applications
 * @subpackage Coach
 */
declare(strict_types=1);

namespace Kwai\Applications\Coaches\Actions;

use Kwai\Core\Infrastructure\Presentation\Action;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

/**
 * Class GetCoachAction
 */
class GetCoachAction extends Action
{
    /**
     * @inheritDoc
     */
    public function __invoke(Request $request, Response $response, array $args)
    {
    }
}
