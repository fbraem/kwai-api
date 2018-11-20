<?php

namespace REST\Teams\Actions;

use Interop\Container\ContainerInterface;

use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;

use Domain\Team\TeamsTable;
use Domain\Team\TeamTransformer;
use Domain\Team\TeamTypesTable;

use Respect\Validation\Validator as v;

use Core\Validators\ValidationException;
use Core\Validators\InputValidator;
use Core\Validators\EntityExistValidator;

use Core\Responses\UnprocessableEntityResponse;
use Core\Responses\ResourceResponse;
use Core\Responses\NotFoundResponse;

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

            $data = $request->getParsedBody();

            (new InputValidator([
                'data.attributes.name' => v::notEmpty()->length(1, 255),
                'data.attributes.active' => [ v::boolType(), true ]
            ], true))->validate($data);

            $seasonData = \JmesPath\search('data.relationships.season.data', $data);
            if (isset($seasonData)) {
                $season = (new EntityExistValidator('data.relationships.season', $teamsTable->Season, false))->validate($data);
                if ($season) {
                    $team->season = $season;
                } else {
                    $team->season_id = null;
                }
            }

            $typeData = \JmesPath\search('data.relationships.team_type.data', $data);
            if (isset($typeData)) {
                $team_type = (new EntityExistValidator('data.relationships.team_type', $teamsTable->TeamType, false))->validate($data);
                if ($team_type) {
                    $team->team_type = $team_type;
                } else {
                    $team->team_type_id = null;
                }
            }

            $teamType = (new EntityExistValidator('data.relationships.team_type', $teamsTable->TeamType, false))->validate($data);

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

            $response = (new ResourceResponse(
                TeamTransformer::createForItem($team)
            ))($response);
        } catch (RecordNotFoundException $rnfe) {
            $response = (new NotFoundResponse(("Team doesn't exist")))($response);
        } catch (ValidationException $ve) {
            $response = (new UnprocessableEntityResponse($ve->getErrors()))($response);
        }

        return $response;
    }
}
