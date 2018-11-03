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

class TeamUpdateAction
{
    private $container;

    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
    }

    public function __invoke(Request $request, Response $response, $args)
    {
        $teamsTable = TeamsTable::getTableFromRegistry();
        try {
            $team = $teamsTable->get($args['id'], [
                'contain' => [ 'Season' ]
            ]);
        } catch (RecordNotFoundException $rnfe) {
            return $response->withStatus(404, _("Team doesn't exist"));
        }

        $data = $request->getParsedBody();

        $validator = new TeamInputValidator();
        if (! $validator->validate($data)) {
            return $validator->unprocessableEntityResponse($response);
        }

        $seasonData = \JmesPath\search('data.relationships.season.data', $data);
        if (isset($seasonData)) {
            if ($seasonData['id']) {
                try {
                    $season = $teamsTable->Season->get($seasonData['id']);
                    $team->season = $season;
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
            } else {
                $team->season_id = null;
            }
        }

        $teamTypeId = \JmesPath\search('data.relationships.team_type.data.id', $data);
        if (isset($teamTypeId)) {
            try {
                $team_type = $teamsTable->TeamType->get($teamTypeId);
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

        if (isset($attributes['name'])) {
            $team->name = $attributes['name'];
        }
        if (isset($team_type)) {
            $team->team_type = $team_type;
        }
        if (isset($attributes['active'])) {
            $team->active = $attributes['active'];
        }
        if (isset($attributes['remark'])) {
            $team->remark = $attributes['remark'];
        }

        $teamsTable->save($team);

        return (new \Core\ResourceResponse(
            TeamTransformer::createForItem($team)
        ))($response);
    }
}
