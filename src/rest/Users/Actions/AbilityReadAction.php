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

class AbilityReadAction
{
    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
    }

    public function __invoke(Request $request, Response $response, $args)
    {
        try {
            $response = (new ResourceResponse(
                AbilityTransformer::createForItem(
                    AbilitiesTable::getTableFromRegistry()->get(
                        $args['id'],
                        [
                            'contain' => [
                                'Rules',
                                'Rules.RuleAction',
                                'Rules.RuleSubject'
                            ]
                        ]
                    )
                )
            ))($response);
        } catch (RecordNotFoundException $rnfe) {
            $response = (new NotFoundResponse(_("Ability doesn't exist.")))($response);
        }

        return $response;
    }
}
