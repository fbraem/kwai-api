<?php

namespace REST\Teams\Actions;

use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;
use Aura\Payload\Payload;

use Core\Responders\Responder;
use Core\Responders\JSONResponder;
use Core\Responders\NotFoundResponder;

class TeamMembersAddAction implements \Core\ActionInterface
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
        foreach ($json['data'] as $memberData) {
            try {
                $member = $membersTable->get(
                    $memberData['data']['id'],
                    [
                        'contain' => ['Person']
                    ]
                );
                $team->members[] = $member;
            } catch (\Cake\Datasource\Exception\RecordNotFoundException $rnfe) {
                //Skip this member
            }
        }

        $team->dirty('members', true);
        $teamsTable->save($team);

        $payload->setOutput(\Judo\Domain\Member\MemberTransformer::createForCollection($team->members));
        return (
            new JSONResponder(
                new Responder(),
                $payload
            ))->respond();
    }
}
