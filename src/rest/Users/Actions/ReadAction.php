<?php

namespace REST\Users\Actions;

use Psr\Container\ContainerInterface;

use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;

use Domain\User\UsersTable;
use Domain\User\UserTransformer;

use Cake\Datasource\Exception\RecordNotFoundException;

use Core\Responses\ResourceResponse;
use Core\Responses\NotFoundResponse;

class ReadAction
{
    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
    }

    public function __invoke(Request $request, Response $response, $args)
    {
        $parameters = $request->getAttribute('parameters');

        $contain = [];
        if (isset($parameters['include'])) {
            foreach ($parameters['include'] as $include) {
                if ($include == 'abilities') {
                    $contain[] = 'Abilities';
                    $contain[] = 'Abilities.Rules';
                    $contain[] = 'Abilities.Rules.RuleAction';
                    $contain[] = 'Abilities.Rules.RuleSubject';
                }
            }
        }

        try {
            $response = (new ResourceResponse(
                UserTransformer::createForItem(
                    UsersTable::getTableFromRegistry()->get(
                        $args['id'],
                        [
                            'contain' => $contain
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
