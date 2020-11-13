<?php

namespace REST\Seasons\Actions;

use Psr\Container\ContainerInterface;

use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;

use Domain\Game\SeasonsTable;
use Domain\Game\SeasonTransformer;

use Cake\Datasource\Exception\RecordNotFoundException;

use Kwai\Core\Infrastructure\Presentation\Responses\ResourceResponse;
use Kwai\Core\Infrastructure\Presentation\Responses\NotFoundResponse;

class ReadAction
{
    private $container;

    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
    }

    public function __invoke(Request $request, Response $response, $args)
    {
        try {
            $response = (new ResourceResponse(
                SeasonTransformer::createForItem(
                    SeasonsTable::getTableFromRegistry()->get($args['id'], [
                        'contain' => ['Teams']
                    ])
                )
            ))($response);
        } catch (RecordNotFoundException $rnfe) {
            $response = (new NotFoundResponse(_("Season doesn't exist")))($response);
        }
        return $response;
    }
}
