<?php

namespace REST\Trainings\Actions;

use Interop\Container\ContainerInterface;

use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;

use Cake\Datasource\Exception\RecordNotFoundException;

use Domain\Training\DefinitionsTable;
use Domain\Training\DefinitionTransformer;

class DefinitionReadAction extends \Core\Action
{
    public function __construct(ContainerInterface $container)
    {
        parent::__construct($container);
    }

    public function __invoke(Request $request, Response $response, $args)
    {
        $contain = [];

        try {
            return (new \Core\ResourceResponse(
                DefinitionTransformer::createForItem(
                    DefinitionsTable::getTableFromRegistry()->get(
                        $args['id'],
                        [
                            'contain' => [ 'Season' ]
                        ]
                    )
                )
            ))($response);
        } catch (RecordNotFoundException $rnfe) {
            return $response->withStatus(404, _("Training definition doesn't exist"));
        }
    }
}
