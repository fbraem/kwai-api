<?php

namespace REST\Seasons\Actions;

use Interop\Container\ContainerInterface;

use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;

use Domain\Game\SeasonsTable;
use Domain\Game\SeasonTransformer;

use Cake\Datasource\Exception\RecordNotFoundException;

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
            return (new \Core\ResourceResponse(
                SeasonTransformer::createForItem(
                    SeasonsTable::getTableFromRegistry()->get($args['id'], [
                        'contain' => ['Teams']
                    ])
                )
            ))($response);
        } catch (RecordNotFoundException $rnfe) {
            return $response->withStatus(404, _("Season doesn't exist"));
        }
    }
}
