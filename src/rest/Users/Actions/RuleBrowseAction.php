<?php

namespace REST\Users\Actions;

use Psr\Container\ContainerInterface;

use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;

use Domain\User\RulesTable;
use Domain\User\RuleTransformer;

use Cake\Datasource\Exception\RecordNotFoundException;

use Kwai\Core\Infrastructure\Presentation\Responses\ResourceResponse;
use Kwai\Core\Infrastructure\Presentation\Responses\NotFoundResponse;

class RuleBrowseAction
{
    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
    }

    public function __invoke(Request $request, Response $response, $args)
    {
        $query = RulesTable::getTableFromRegistry()->find();
        $query->contain(
            [
                'RuleAction',
                'RuleSubject'
            ]
        );
        return (new ResourceResponse(
            RuleTransformer::createForCollection(
                $query->all()
            )
        ))($response);
    }
}
