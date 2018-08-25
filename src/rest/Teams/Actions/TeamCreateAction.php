<?php

namespace REST\Teams\Actions;

use Interop\Container\ContainerInterface;

use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;

use Domain\Team\TeamsTable;
use Domain\Team\TeamTransformer;
use Domain\Team\TeamTypesTable;

use REST\Teams\TeamInputValidator;
use REST\Teams\TeamEmptyValidator;

use Cake\Datasource\Exception\RecordNotFoundException;

class TeamCreateAction
{
    private $container;

    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
    }

    public function __invoke(Request $request, Response $response, $args)
    {
        $data = $request->getParsedBody();

        $validator = new TeamInputValidator();
        if (! $validator->validate($data)) {
            return $validator->unprocessableEntityResponse($response);
        }

        $teamsTable = TeamsTable::getTableFromRegistry();

        $seasonId = \JmesPath\search('data.relationships.season.data.id', $data);
        if (isset($seasonId)) {
            try {
                $season = $teamsTable->Season->get($seasonId);
            } catch (RecordNotFoundException $rnfe) {
                $response
                    ->getBody()
                    ->write(
                        json_encode([
                            'errors' => [
                                'source' => [
                                    'pointer' => '/data/relationships/season'
                                ],
                                'title' => _("Season doesn't exist")
                            ]
                        ])
                    )
                ;

                return $response
                    ->withStatus(422)
                    ->withHeader('content-type', 'application/vnd.api+json')
                ;
            }
        }

        $teamTypeId = \JmesPath\search('data.relationships.team_type.data.id', $data);
        if (isset($teamTypeId)) {
            try {
                $teamType = $teamsTable->TeamType->get($teamTypeId);
            } catch (RecordNotFoundException $rnfe) {
                $response
                    ->getBody()
                    ->write(
                        json_encode([
                            'errors' => [
                                'source' => [
                                    'pointer' => '/data/relationships/team_type'
                                ],
                                'title' => _("Teamtype doesn't exist")
                            ]
                        ])
                    )
                ;

                return $response
                    ->withStatus(422)
                    ->withHeader('content-type', 'application/vnd.api+json')
                ;
            }
        }

        $attributes = \JmesPath\search('data.attributes', $data);

        $team = $teamsTable->newEntity();
        $team->name = $attributes['name'];
        $team->remark = $attributes['remark'] ?? null;
        $team->active = $attribute['active'] ?? true;
        $team->season = $season ?? null;
        $team->team_type = $teamType ?? null;
        $teamsTable->save($team);

        return (new \Core\ResourceResponse(
            TeamTransformer::createForItem($team)
        ))($response)->withStatus(201);
    }
}
