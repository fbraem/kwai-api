<?php

namespace REST\Users\Actions;

use Interop\Container\ContainerInterface;

use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;

use Domain\User\UsersTable;
use Domain\User\RuleGroupTransformer;

use Cake\Datasource\Exception\RecordNotFoundException;

use Core\Responses\ResourceResponse;
use Core\Responses\NotFoundResponse;

class UserRuleBrowseAction
{
    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
    }

    public function __invoke(Request $request, Response $response, $args)
    {
        $parameters = $request->getAttribute('parameters');

        $contain = [
            'RuleGroups',
            'RuleGroups.Rules',
            'RuleGroups.Rules.RuleAction',
            'RuleGroups.Rules.RuleSubject'
        ];

        try {
            $user = UsersTable::getTableFromRegistry()->get(
                $args['id'],
                [
                    'contain' => $contain
                ]
            );

            $response = (new ResourceResponse(
                RuleGroupTransformer::createForCollection(
                    $user->rule_groups
                )
            ))($response);
        } catch (\Cake\Datasource\Exception\RecordNotFoundException $rnfe) {
            $response = (new NotFoundResponse(_("User doesn't exist.")))($response);
        }

        return $response;
    }
}
