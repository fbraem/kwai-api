<?php

namespace REST\Trainings\Actions;

use Psr\Container\ContainerInterface;

use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;

use Cake\Datasource\Exception\RecordNotFoundException;

use Domain\Training\DefinitionsTable;
use Domain\Training\DefinitionTransformer;

use Kwai\Core\Infrastructure\Responses\ResourceResponse;
use Kwai\Core\Infrastructure\Responses\NotFoundResponse;

class DefinitionReadAction
{
    private $container;

    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
    }

    public function __invoke(Request $request, Response $response, $args)
    {
        $contain = [];

        try {
            $response = (new ResourceResponse(
                DefinitionTransformer::createForItem(
                    DefinitionsTable::getTableFromRegistry()->get(
                        $args['id'],
                        [
                            'contain' => [
                                'Season',
                                'Team'
                            ]
                        ]
                    )
                )
            ))($response);
        } catch (RecordNotFoundException $rnfe) {
            $response = (new NotFoundResponse(
                _("Training definition doesn't exist")
            ))($response);
        }

        return $response;
    }
}
