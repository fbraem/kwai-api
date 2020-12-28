<?php
/**
 * @package
 * @subpackage
 */
declare(strict_types=1);

namespace Kwai\Applications\Coach\Actions;

use Kwai\Modules\Trainings\UseCases\BrowseTrainingsCommand;
use Psr\Container\ContainerInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

/**
 * Class BrowseTrainingsAction
 */
class BrowseTrainingsAction
{
    public function __construct(
        private ContainerInterface $container
    ) {
    }

    public function __invoke(
        ServerRequestInterface $request,
        ResponseInterface $response,
        $args
    ): ResponseInterface {
        $command = new BrowseTrainingsCommand();
        var_dump($args);
        $response->getBody()->write(json_encode($request->getAttributes()));
        return $response;
    }
}
