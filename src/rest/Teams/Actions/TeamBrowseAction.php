<?php

namespace REST\Teams\Actions;

use Interop\Container\ContainerInterface;

use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;

use Domain\Team\TeamsTable;
use Domain\Team\TeamTransformer;

class TeamBrowseAction
{
    private $container;

    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
    }

    public function __invoke(Request $request, Response $response, $args)
    {
        return (new \Core\ResourceResponse(
            TeamTransformer::createForCollection(
                TeamsTable::getTableFromRegistry()
                    ->find()
                    ->contain(['Season', 'TeamType'])
                    ->order([
                        'Season.name' => 'DESC',
                        'Teams.name' => 'ASC'
                    ])
                    ->all()
            )
        ))($response);
    }
}
