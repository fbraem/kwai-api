<?php

namespace REST\Users\Actions;

use Interop\Container\ContainerInterface;

use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;

use Domain\User\RuleGroupsTable;
use Domain\User\RuleGroupTransformer;

use Cake\Datasource\Exception\RecordNotFoundException;

use Core\Responses\ResourceResponse;
use Core\Responses\NotFoundResponse;

class RuleGroupReadAction
{
    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
    }

    public function __invoke(Request $request, Response $response, $args)
    {
        $parameters = $request->getAttribute('parameters');

        try {
            $response = (new ResourceResponse(
                RuleGroupTransformer::createForItem(
                    RuleGroupsTable::getTableFromRegistry()->get(
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
            $response = (new NotFoundResponse(_("User doesn't exist.")))($response);
        }

        return $response;
    }
}
