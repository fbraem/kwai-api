<?php

namespace REST\Users\Actions;

use Psr\Container\ContainerInterface;

use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;

use Domain\User\UsersTable;
use Domain\User\AbilityTransformer;

use Cake\Datasource\Exception\RecordNotFoundException;

use Core\Responses\ResourceResponse;
use Core\Responses\NotFoundResponse;

class UserAbilityBrowseAction
{
    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
    }

    public function __invoke(Request $request, Response $response, $args)
    {
        $parameters = $request->getAttribute('parameters');

        $contain = [
            'Abilities',
            'Abilities.Rules',
            'Abilities.Rules.RuleAction',
            'Abilities.Rules.RuleSubject'
        ];

        try {
            $user = UsersTable::getTableFromRegistry()->get(
                $args['id'],
                [
                    'contain' => $contain
                ]
            );

            $response = (new ResourceResponse(
                AbilityTransformer::createForCollection(
                    $user->abilities
                )
            ))($response);
        } catch (\Cake\Datasource\Exception\RecordNotFoundException $rnfe) {
            $response = (new NotFoundResponse(_("User doesn't exist.")))($response);
        }

        return $response;
    }
}
