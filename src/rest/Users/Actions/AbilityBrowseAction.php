<?php

namespace REST\Users\Actions;

use Interop\Container\ContainerInterface;

use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;

use Domain\User\AbilitiesTable;
use Domain\User\AbilityTransformer;

use Cake\Datasource\Exception\RecordNotFoundException;

use Core\Responses\ResourceResponse;
use Core\Responses\NotFoundResponse;

class AbilityBrowseAction
{
    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
    }

    public function __invoke(Request $request, Response $response, $args)
    {
        $query = AbilitiesTable::getTableFromRegistry()->find();
        $query->contain(
            [
                'Rules',
                'Rules.RuleAction',
                'Rules.RuleSubject'
            ]
        );
        return (new ResourceResponse(
            AbilityTransformer::createForCollection(
                $query->all()
            )
        ))($response);
    }
}
