<?php

namespace REST\Teams\Actions;

use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;
use Aura\Payload\Payload;

use Core\Responders\Responder;
use Core\Responders\JSONResponder;
use Core\Responders\NotFoundResponder;

class TeamMembersBrowseAction implements \Core\ActionInterface
{
    public function __invoke(RequestInterface $request, Payload $payload) : ResponseInterface
    {
        $id = $request->getAttribute('route.id');

        try {
            $team = \Domain\Team\TeamsTable::getTableFromRegistry()->get($id, [
                'contain' => ['Members', 'Members.Person']
            ]);
        } catch (\Cake\Datasource\Exception\RecordNotFoundException $rnfe) {
            return (
                new NotFoundResponder(
                    new Responder(),
                    _("Team doesn't exist.")
                ))->respond();
        }

        //TODO: Remove sport dependency?
        $payload->setOutput(\Judo\Domain\Member\MemberTransformer::createForCollection($team->members));
        return (
            new JSONResponder(
                new Responder(),
                $payload
            ))->respond();
    }
}
