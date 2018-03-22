<?php

namespace REST\Teams\Actions;

use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;
use Aura\Payload\Payload;

use Core\Responders\Responder;
use Core\Responders\JSONResponder;
use Core\Responders\JSONErrorResponder;
use Core\Responders\HTTPCodeResponder;

class TeamCreateAction implements \Core\ActionInterface
{
    public function __invoke(RequestInterface $request, Payload $payload) : ResponseInterface
    {
        $data = $payload->getInput();

        $validator = new \REST\Teams\TeamValidator();
        $errors = $validator->validate($data);
        if (count($errors) > 0) {
            return (
                new JSONErrorResponder(
                    new HTTPCodeResponder(
                        new Responder(),
                        422
                    ),
                    $errors
                )
            )->respond();
        }

        $teamsTable = \Domain\Team\TeamsTable::getTableFromRegistry();

        $seasonId = \JmesPath\search('data.relationships.season.data.id', $data);
        if (isset($seasonId)) {
            try {
                $season = $teamsTable->Season->get($seasonId);
            } catch (\Cake\Datasource\Exception\RecordNotFoundException $rnfe) {
                return (
                    new JSONErrorResponder(
                        new HTTPCodeResponder(
                            new Responder(),
                            422
                        ),
                        [
                            '/data/relationships/season' => [
                                _('Season doesn\'t exist')
                            ]
                        ]
                    ))->respond();
            }
        }

        $teamTypeId = \JmesPath\search('data.relationships.teamtype.data.id', $data);
        if (isset($teamTypeId)) {
            try {
                $teamType = $teamsTable->TeamType->get($teamTypeId);
            } catch (\Cake\Datasource\Exception\RecordNotFoundException $rnfe) {
                return (
                    new JSONErrorResponder(
                        new HTTPCodeResponder(
                            new Responder(),
                            422
                        ),
                        [
                            '/data/relationships/team_type' => [
                                _('Teamtype doesn\'t exist')
                            ]
                        ]
                    ))->respond();
            }
        }

        $attributes = \JmesPath\search('data.attributes', $data);

        $team = $teamsTable->newEntity();
        $team->name = $attributes['name'];
        $team->remark = $attributes['remark'];
        if (isset($season)) {
            $team->season = $season;
        }
        if (isset($teamType)) {
            $team->team_type = $teamType;
        }
        $teamsTable->save($team);

        $payload->setOutput(\Domain\Team\TeamTransformer::createForItem($team));

        return (
            new JSONResponder(
                new HTTPCodeResponder(
                    new Responder(),
                    201
                ),
                $payload
            )
        )->respond();
    }
}
