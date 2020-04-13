<?php

namespace REST\Teams\Actions;

use Psr\Container\ContainerInterface;

use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;

use Domain\Team\TeamsTable;
use Domain\Team\TeamTransformer;

use Respect\Validation\Validator as v;

use Kwai\Core\Infrastructure\Validators\ValidationException;
use Kwai\Core\Infrastructure\Validators\InputValidator;
use Kwai\Core\Infrastructure\Validators\EntityExistValidator;

use Kwai\Core\Infrastructure\Responses\UnprocessableEntityResponse;
use Kwai\Core\Infrastructure\Responses\ResourceResponse;
use Kwai\Core\Infrastructure\Responses\NotFoundResponse;

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

            $seasonData = \JmesPath\search('data.relationships.season', $data);
            if (isset($seasonData)) {
                $season = (new EntityExistValidator('data.relationships.season', $teamsTable->Season, false))->validate($data);
                if ($season) {
                    $team->season = $season;
                } else {
                    $team->season_id = null;
                    $team->season = null;
                }
            }

            $teamCategoryData = \JmesPath\search('data.relationships.team_category', $data);
            if (isset($teamCategoryData)) {
                $team_category = (new EntityExistValidator('data.relationships.team_category', $teamsTable->TeamCategory, false))->validate($data);
                if ($team_category) {
                    $team->team_category = $team_category;
                } else {
                    $team->team_category_id = null;
                    $team->team_category = null;
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
