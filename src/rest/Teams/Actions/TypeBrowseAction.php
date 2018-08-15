<?php

namespace REST\Teams\Actions;

use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;
use Aura\Payload\Payload;

use Core\Responders\Responder;
use Core\Responders\JSONResponder;

class TypeBrowseAction implements \Core\ActionInterface
{
    public function __invoke(RequestInterface $request, Payload $payload) : ResponseInterface
    {
        $types = \Domain\Team\TeamTypesTable::getTableFromRegistry()->find()->all();

        $payload->setOutput(\Domain\Team\TeamTypeTransformer::createForCollection($types));

        return (new JSONResponder(new Responder(), $payload, '/api/teams'))->respond();
    }
}
