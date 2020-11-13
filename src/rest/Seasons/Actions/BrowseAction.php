<?php

namespace REST\Seasons\Actions;

use Psr\Container\ContainerInterface;

use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;

use Domain\Game\SeasonsTable;
use Domain\Game\SeasonTransformer;

use Kwai\Core\Infrastructure\Presentation\Responses\ResourceResponse;

class BrowseAction
{
    private $container;

    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
    }

    public function __invoke(Request $request, Response $response, $args)
    {
        return (new ResourceResponse(
            SeasonTransformer::createForCollection(
                SeasonsTable::getTableFromRegistry()
                    ->find()
                    ->contain(['Teams'])
                    ->order(['start_date' => 'DESC'])
                    ->all()
            )
        ))($response);
    }
}
