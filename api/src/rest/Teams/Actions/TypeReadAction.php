<?php

namespace REST\Teams\Actions;

use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;
use Aura\Payload\Payload;

use Core\Responders\Responder;
use Core\Responders\JSONResponder;
use Core\Responders\NotFoundResponder;

class TypeReadAction implements \Core\ActionInterface
{
    public function __invoke(RequestInterface $request, Payload $payload) : ResponseInterface
    {
        $id = $request->getAttribute('route.id');

        try {
            $type = \Domain\Team\TeamTypesTable::getTableFromRegistry()->get($id);
        } catch (\Cake\Datasource\Exception\RecordNotFoundException $rnfe) {
            return (
                new NotFoundResponder(
                    new Responder(),
                    _("Type doesn't exist.")
                ))->respond();
        }

        $payload->setOutput(\Domain\Team\TeamTypeTransformer::createForItem($type));
        return (
            new JSONResponder(
                new Responder(),
                $payload,
                '/api/teams'
            ))->respond();
    }
}
