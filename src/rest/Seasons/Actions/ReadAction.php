<?php

namespace REST\Seasons\Actions;

use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;
use Aura\Payload\Payload;

use Core\Responders\Responder;
use Core\Responders\JSONResponder;
use Core\Responders\NotFoundResponder;

class ReadAction implements \Core\ActionInterface
{
    public function __invoke(RequestInterface $request, Payload $payload) : ResponseInterface
    {
        $id = $request->getAttribute('route.id');

        try {
            $season = \Domain\Game\SeasonsTable::getTableFromRegistry()->get($id);
        } catch (\Cake\Datasource\Exception\RecordNotFoundException $rnfe) {
            return (
                new NotFoundResponder(
                    new Responder(),
                    _("Season doesn't exist.")
                ))->respond();
        }

        $payload->setOutput(\Domain\Game\SeasonTransformer::createForItem($season));
        return (
            new JSONResponder(
                new Responder(),
                $payload
            ))->respond();
    }
}
