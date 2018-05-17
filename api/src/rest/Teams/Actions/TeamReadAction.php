<?php

namespace REST\Teams\Actions;

use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;
use Aura\Payload\Payload;

use Core\Responders\Responder;
use Core\Responders\JSONResponder;
use Core\Responders\NotFoundResponder;

class TeamReadAction implements \Core\ActionInterface
{
    public function __invoke(RequestInterface $request, Payload $payload) : ResponseInterface
    {
        $id = $request->getAttribute('route.id');

        $parameters = $request->getAttribute('parameters');
        $contain = [
            'Season',
            'TeamType'
        ];
        if (isset($parameters['include'])) {
            foreach ($parameters['include'] as $include) {
                if ($include == 'members') {
                    $contain[] = 'Members';
                    $contain[] = 'Members.Person';
                }
            }
        }
        try {
            $team = \Domain\Team\TeamsTable::getTableFromRegistry()->get($id, [
                'contain' => $contain
            ]);
        } catch (\Cake\Datasource\Exception\RecordNotFoundException $rnfe) {
            return (
                new NotFoundResponder(
                    new Responder(),
                    _("Team doesn't exist.")
                ))->respond();
        }

        $payload->setOutput(\Domain\Team\TeamTransformer::createForItem($team));
        return (
            new JSONResponder(
                new Responder(),
                $payload
            ))->respond();
    }
}
