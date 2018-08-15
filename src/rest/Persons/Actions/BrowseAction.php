<?php

namespace REST\Persons\Actions;

use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;
use Aura\Payload\Payload;

use Core\Responders\Responder;
use Core\Responders\JSONResponder;

class BrowseAction implements \Core\ActionInterface
{
    public function __invoke(RequestInterface $request, Payload $payload) : ResponseInterface
    {
        $persons = \Domain\Person\PersonsTable::getTableFromRegistry()
            ->find()
            ->contain(['Nationality', 'Contact', 'Contact.Country'])
            ->all();
        $payload->setOutput(\Domain\Person\PersonTransformer::createForCollection($persons));
        return (new JSONResponder(new Responder(), $payload))->respond();
    }
}
