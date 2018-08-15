<?php

namespace REST\Countries\Actions;

use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;
use Aura\Payload\Payload;

use Core\Responders\Responder;
use Core\Responders\JSONResponder;

class BrowseAction implements \Core\ActionInterface
{
    public function __invoke(RequestInterface $request, Payload $payload) : ResponseInterface
    {
        $countries = \Domain\Person\CountriesTable::getTableFromRegistry()->find()->all();
        $payload->setOutput(\Domain\Person\CountryTransformer::createForCollection($countries));
        return (new JSONResponder(new Responder(), $payload))->respond();
    }
}
