<?php

namespace REST\Teams\Actions;

use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;
use Aura\Payload\Payload;

use Core\Responders\Responder;
use Core\Responders\JSONResponder;
use Core\Responders\NotFoundResponder;

class TeamMembersDeleteAction implements \Core\ActionInterface
{
    //TODO: Remove sport dependency?
    public function __invoke(RequestInterface $request, Payload $payload) : ResponseInterface
    {
        $id = $request->getAttribute('route.id');

        $teamsTable = \Domain\Team\TeamsTable::getTableFromRegistry();
        try {
            $team = $teamsTable->get($id, [
                'contain' => ['Members', 'Members.Person']
            ]);
        } catch (\Cake\Datasource\Exception\RecordNotFoundException $rnfe) {
            return (
                new NotFoundResponder(
                    new Responder(),
                    _("Team doesn't exist.")
                ))->respond();
        }

        $membersTable = \Judo\Domain\Member\MembersTable::getTableFromRegistry();
        $json = $payload->getInput();
        $ids = [];

        foreach ($json['data'] as $memberData) {
            $ids[] = $memberData['data']['id'];
        }
        $members = $membersTable->find()->where(['id IN' => $ids])->toList();
        $teamsTable->Members->unlink($team, $members);

        $team->dirty('members', true);
        $teamsTable->save($team);

        $team = \Domain\Team\TeamsTable::getTableFromRegistry()->get($id, [
            'contain' => ['Members', 'Members.Person']
        ]);

        $payload->setOutput(\Judo\Domain\Member\MemberTransformer::createForCollection($team->members));
        return (
            new JSONResponder(
                new Responder(),
                $payload
            ))->respond();
    }
}
