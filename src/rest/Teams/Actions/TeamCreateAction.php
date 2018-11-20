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

        try {
            (new InputValidator([
                'data.attributes.name' => v::notEmpty()->length(1, 255),
                'data.attributes.active' => [ v::boolType(), true ]
            ]))->validate($data);

            $teamsTable = TeamsTable::getTableFromRegistry();

            $season = (new EntityExistValidator('data.relationships.season', $teamsTable->Season, false))->validate($data);
            $teamType = (new EntityExistValidator('data.relationships.team_type', $teamsTable->TeamType, false))->validate($data);

            $attributes = \JmesPath\search('data.attributes', $data);

            $team = $teamsTable->newEntity();
            $team->name = $attributes['name'];
            $team->remark = $attributes['remark'] ?? null;
            $team->active = $attribute['active'] ?? true;
            $team->season = $season ?? null;
            $team->team_type = $teamType ?? null;
            $teamsTable->save($team);

            $route = $request->getAttribute('route');
            if (! empty($route)) {
                $route->setArgument('id', $team->id);
            }

            $response = (new ResourceResponse(
                TeamTransformer::createForItem($team)
            ))($response)->withStatus(201);
        } catch (ValidationException $ve) {
            $response = (new UnprocessableEntityResponse($ve->getErrors()))($response);
        }

        return $response;
    }
}
