<?php

namespace REST\Teams\Actions;

use Interop\Container\ContainerInterface;

use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;

use Domain\Team\TeamsTable;
use Domain\Team\TeamTransformer;

use Core\Responses\ResourceResponse;

class TeamBrowseAction
{
    private $container;

    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
    }

    public function __invoke(Request $request, Response $response, $args)
    {
        $table = TeamsTable::getTableFromRegistry();
        $query = $table->find();

        return (new ResourceResponse(
            TeamTransformer::createForCollection(
                $query->select($table)
                    ->select($table->Season)
                    ->select($table->TeamCategory)
                    ->select(['members_count' => $query->func()->count('Members.id')])
                    ->contain(['Season', 'TeamCategory'])
                    ->leftJoinWith('Members')
                    ->order([
                        'Season.name' => 'DESC',
                        'Teams.name' => 'ASC'
                    ])
                    ->group('Teams.id')
                    ->all()
            )
        ))($response);
    }
}
